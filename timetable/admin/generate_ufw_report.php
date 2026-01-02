<?php
// generate_wastage_report.php
//include("admindbconn.php");
session_start();
include("admindbconn.php");
// Session validation
if (!isset($_SESSION['user_name']) || $_SESSION["role"] != 'ADMIN') {
    echo "Invalid User";
    exit(0);
}


$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$formatted_start_date = date('d-m-Y', strtotime($start_date));
$formatted_end_date = date('d-m-Y', strtotime($end_date));
$print_dates = "Date : <b>" . $formatted_start_date."</b>";
if ($start_date != $end_date) {
    $print_dates = " Date: " . $formatted_start_date . " to " . $formatted_end_date;
}

$din_q ="select boys+girls as tot_din from dining_count_status where dining_date='".$start_date."'";

$din_result = mysqli_query($dbconn, $din_q);
$tot_din=" Data Notfound";

if($din_result) 
{
	if ($row = mysqli_fetch_assoc($din_result)) 
	{
		$tot_din=$row['tot_din'];
	}
	else
	{
		echo "<table border='1' style='border-collapse:collapse;color:red;font-size:60px;' align='center' cellpadding='10'>
		<tr><td> D I N I N G - COUNT  D A T A... N O T ......FOUND</TD></tr>
		</table>";
		exit(0);
	}
		
} 


$report_type = $_POST['report_type'];
$issue_cat_arr = [
    'A' => 'All',
    'B' => 'Breakfast',
    'L' => 'Lunch',
    'S' => 'Snacks',
    'D' => 'Dinner'
];
// Query to fetch wastage data
/*
$sql = "SELECT w.food_name, w.issue_category, 
               COALESCE(SUM(w.quantity), 0) AS total_wastage_quantity, 
               COALESCE(SUM(i.quantity), 0) AS total_issued_quantity, 
               CASE WHEN SUM(i.quantity) > 0 THEN ROUND((SUM(w.quantity) / SUM(i.quantity)) * 100, 2) ELSE 0 END AS wastage_percentage 
        FROM wastage_food w 
        LEFT JOIN issueitem i 
        ON w.item_id = i.itemid 
        AND w.wastage_date = i.issue_date 
        AND w.issue_category = i.issue_category 
        WHERE w.wastage_date = '$start_date' 
        GROUP BY w.food_name, w.issue_category 
        ORDER BY w.issue_category ASC";
		*/
		//item_id=194  eggs  1kg=18 eggs     1 egg =0.056 kg
$sql="SELECT 
    w.food_name, 
    w.issue_category, 
    COALESCE(SUM(
        CASE 
            WHEN w.item_id = 194 THEN w.quantity * 0.056 
            ELSE w.quantity 
        END
    ), 0) AS total_wastage_quantity, 
    COALESCE(SUM(
        CASE 
            WHEN i.itemid = 194 THEN i.quantity * 0.056 
            ELSE i.quantity 
        END
    ), 0) AS total_issued_quantity, 
    CASE 
        WHEN SUM(
            CASE 
                WHEN i.itemid = 194 THEN i.quantity * 0.056 
                ELSE i.quantity 
            END
        ) = 0 
        THEN 0 
        ELSE ROUND((
            SUM(
                CASE 
                    WHEN w.item_id = 194 THEN w.quantity * 0.056 
                    ELSE w.quantity 
                END
            ) / SUM(
                CASE 
                    WHEN i.itemid = 194 THEN i.quantity * 0.056 
                    ELSE i.quantity 
                END
            )
        ) * 100, 2) 
    END AS wastage_percentage 
FROM wastage_food w 
LEFT JOIN issueitem i 
    ON w.item_id = i.itemid 
    AND w.wastage_date = i.issue_date 
    AND w.issue_category = i.issue_category 
WHERE w.wastage_date = '$start_date' 
GROUP BY w.food_name, w.issue_category 
ORDER BY w.issue_category ASC;
";

$result = mysqli_query($dbconn, $sql);

// Define category names
$issue_cat_arr = ['B' => 'Breakfast', 'L' => 'Lunch', 'S' => 'Snacks', 'D' => 'Dinner'];
$food_issue_arr = [];

// Store fetched data in categorized array
while ($row = mysqli_fetch_assoc($result)) {
    if ($row['total_wastage_quantity'] > 0.0) {
        $food_issue_arr[$row['issue_category']][] = $row;
    }
}

// Start table output
echo "<table border='1' style='border-collapse:collapse;width:80%;' align='center' cellpadding='10'>
	<tr><th colspan=6 style='text-align:center'>Unutilized Cooked Food Report</th></tr>
	<tr><td colspan='2' style='text-align:left'>Total Students Dined: <b>".$tot_din."</b> </td>
    <td colspan='4' style='text-align:right'>$print_dates</td></tr>
    <tr><th>S.No</th><th>Food Category</th><th>Menu Item</th><th>Total Issued Qty (Kg.)</th><th>Total Wastage Qty (Kg.)</th><th>Wastage %</th></tr>";

$sno = 0;

// Loop through categories
foreach ($issue_cat_arr as $key => $category_name) {
    if (!isset($food_issue_arr[$key]) || empty($food_issue_arr[$key])) {
        echo "<tr><td style='text-align:center'>" . (++$sno) . "</td>
              <td style='text-align:center;font-weight:bold'>" . $category_name . "</td>
              <td colspan='4' style='text-align:center;font-weight:bold'>NIL</td></tr>";
    } else {
        // Merge category rows using rowspan
        $first = true;
        $rowspan = count($food_issue_arr[$key]);

        foreach ($food_issue_arr[$key] as $item) {
            echo "<tr><td style='text-align:center'>" . (++$sno) . "</td>";

            if ($first) {
                echo "<td rowspan='$rowspan' style='text-align:center;font-weight:bold'>" . $category_name . "</td>";
                $first = false;
            }

            echo "<td style='text-align:center'>" . $item['food_name'] . "</td>
					<td style='text-align:center'>" . number_format($item['total_issued_quantity'], 2) . "</td>
                  <td style='text-align:center'>" . number_format($item['total_wastage_quantity'], 2) . "</td>
                  
                  <td style='text-align:center'>" . number_format($item['wastage_percentage'], 2) . "%</td></tr>";
        }
    }
}

echo "</table>";


$sql = "SELECT wastage_date, w.issue_category, food_name, food_weight, itemname, w.unit_price, 
               w.quantity as wastage_quantity, j.quantity as issue_quantity 
        FROM wastage_food w 
        INNER JOIN item i ON i.id = w.item_id 
        INNER JOIN issueitem j ON j.itemid = w.item_id 
            AND j.issue_category = w.issue_category 
            AND j.issue_date = w.wastage_date
        WHERE wastage_date BETWEEN '$start_date' AND '$end_date'
        ORDER BY issue_category, food_name, wastage_date";

$result = mysqli_query($dbconn, $sql);


if (!$result) {
    echo "Error: " . mysqli_error($dbconn);
    exit;
}

// Count occurrences of issue_category, food_name, and food_weight
$category_food_counts = [];
while ($row = mysqli_fetch_assoc($result)) {
    $category = $row['issue_category'];
    $food = $row['food_name'];
    $food_weight = $row['food_weight'];
    
    if (!isset($category_food_counts[$category])) {
        $category_food_counts[$category] = [];
    }
    if (!isset($category_food_counts[$category][$food])) {
        $category_food_counts[$category][$food] = [];
    }
    if (!isset($category_food_counts[$category][$food][$food_weight])) {
        $category_food_counts[$category][$food][$food_weight] = 0;
    }
    
    $category_food_counts[$category][$food][$food_weight]++;
}

// Reset the result pointer
mysqli_data_seek($result, 0);

// Table header
$head_cols = ($report_type == 'with_price') ? 6 : 5;
$price_cols = ($report_type == 'with_price') ? "<th>Unit Price</th><th>Total Price</th>" : "";
//echo  $fname_str;
echo "<br>";
echo "<table border='1' style='border-collapse:collapse;width:80%;' align='center' cellpadding='10'>
    <tr><td colspan='$head_cols' style='text-align:center'><h3>Unutilized Cooked Food (Main Ingredients)</h3></td></tr>
    <tr>
        <th>S.No</th>
        <th>Food Category</th>
        <th>Menu Item</th>
        <th>Item Name</th>
        <th>Issue Quantity<br>(Kg.)</th>
       
        $price_cols
    </tr>";
		
		//<th>Total Unutilized<br>Quantity(Kg.)</th>

$sno = 0;
$tot_cost = 0;
$printed_categories = [];
$printed_food = [];
$printed_weight = [];
 $eggs_found=0;
// Loop through result set
while ($row = mysqli_fetch_assoc($result)) {
    $category = $row['issue_category'];
    $food = $row['food_name'];
    $food_weight = $row['food_weight'];
    $issue_category_name = isset($issue_cat_arr[$category]) ? $issue_cat_arr[$category] : 'NIL';    
    // Calculate price
    $total_price = $row['unit_price'] * $row['wastage_quantity'];
    $tot_cost += $total_price;
    echo "<tr>";
    echo "<td style='text-align:center'>" . (++$sno) . "</td>";
    // Print category name with rowspan if not printed already
    if (!isset($printed_categories[$category])) {
        $category_rowspan = 0;
        foreach ($category_food_counts[$category] as $food_data) {
            foreach ($food_data as $count) {
                $category_rowspan += $count;
            }
        }
        echo "<td style='text-align:center;font-weight:bold' rowspan='" . $category_rowspan . "'>" . $issue_category_name . "</td>";
        $printed_categories[$category] = true;
    }

   // Print food name with rowspan if not printed already under this category
    if (!isset($printed_food[$category][$food])) {
        $food_rowspan = array_sum($category_food_counts[$category][$food]);
        echo "<td rowspan='" . $food_rowspan . "' style='text-align:center'>" . $food . "</td>";
        $printed_food[$category][$food] = true;
    }

   
	IF(strtoupper($row['itemname'] )=='EGGS')
	{
		 $eggs_found=1;
	}
    echo "<td style='text-align:center'>" . $row['itemname'] . "</td>";
	$issue_quantity = ( strtoupper($row['itemname'] )=='EGGS') ? $row['issue_quantity'] * 0.056 : $row['issue_quantity'];
    echo "<td style='text-align:center'>" . $issue_quantity . "</td>";
   // echo "<td style='text-align:center'>" . $row['wastage_quantity'] . "</td>";
	
	

    if ($report_type == 'with_price') {
        echo "<td style='text-align:right'>" . number_format($row['unit_price'], 2) . "</td>";
        echo "<td style='text-align:right'>" . number_format($total_price, 2) . "</td>";
    }

    echo "</tr>";
}

// Print total cost row if applicable
if ($report_type == 'with_price') {
    echo "<tr><td colspan='" . ($head_cols - 1) . "' style='text-align:center;font-weight:bold;'>Total Cost</td>
          <td style='text-align:right;font-weight:bold;'>" . number_format($tot_cost, 2) . "</td></tr>";
}
if( $eggs_found==1)
{
	 echo "<tr><td colspan='" . ($head_cols - 1) . "' style='text-align:left;font-weight:bold;'>
	 Note: 18 Eggs = 1 Kg</td>    </tr>";
}
echo "</table>";
//add wastage
//INSERT INTO `dining_wastage`(`id`, `dining_date`, `hall_no`, `breakfast`, `lunch`, `snacks`, `dinner`, `created_time`) VALUES ('[value-1]','[value-2]','[value-3]','[value-4]','[value-5]','[value-6]','[value-7]','[value-8]')
$waste_q ="select * from dining_wastage where dining_date='".$start_date."'";

$wastage_result = mysqli_query($dbconn, $waste_q);


if($wastage_result) 
{
	
			
	// Loop through result set
	$grand_tot =0;
	echo "<table  align='center' border='1' width='800px' style='border-collapse:collapse; margin-top:20px;'>";
	echo "<tr style='height: 40px;'><th colspan=6  style='text-align:center'>Food Wastage (Kg.)</th></tr>";
	echo "<tr style='height: 40px;'><th>Dining <br>Hall Number</th><th>Breakfast</th> <th>Lunch</th><th>Snacks</th><th>Dinner</th><th>Total</th></tr>";
	
	while ($row = mysqli_fetch_assoc($wastage_result)) {
		$hall_no = $row['hall_no'];
		$breakfast = $row['breakfast'];
		$lunch = $row['lunch'];
		$snacks = $row['snacks'];
		$dinner = $row['dinner'];
		$tot=$breakfast+$lunch+$snacks+$dinner;
		$grand_tot += $tot;
		$disp_hall = ($hall_no==1 ? "Dining Hall-I Basket" : "Dining Hall-II Basket") ;
		echo "<tr style='height: 40px;'><td style='text-align:center'>".$disp_hall."</td> 
		<td style='text-align:center'>".$breakfast."</td><td style='text-align:center'>".$lunch."</td>
		<td style='text-align:center'>".$snacks."</td><td style='text-align:center'>".$dinner."</td><td style='text-align:center'>".$tot."</td>
		</tr>";
		}
echo "<tr><th colspan=5  style='text-align:center'>Grand Total</th><td style='text-align:center'>$grand_tot</td></tr>";		
		echo "</table>";
} 
else
	{
		echo "<table border='1' style='border-collapse:collapse;color:red;font-size:60px;' align='center' cellpadding='10'>
		<tr><td> D I N I N G - WASTAGE D A T A... N O T ......FOUND</TD></tr>
		</table>";
		exit(0);
	}
//end wastage

// Close database connection
mysqli_close($dbconn);
?>
