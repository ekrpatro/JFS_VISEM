<?php
// generate_wastage_report.php
include("admindbconn.php");

$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$formatted_start_date = date('d-m-Y', strtotime($start_date));
$formatted_end_date = date('d-m-Y', strtotime($end_date));
$print_dates = "Date : " . $formatted_start_date;
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
$issue_cat_arr = [
    'A' => 'All',
    'B' => 'Breakfast',
    'L' => 'Lunch',
    'S' => 'Snacks',
    'D' => 'Dinner'
];

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
$head_cols = ($report_type == 'with_price') ? 8 : 7;
$price_cols = ($report_type == 'with_price') ? "<th>Unit Price</th><th>Total Price</th>" : "";

echo "<table border='1' style='border-collapse:collapse;' align='center' cellpadding='10'>
        <tr><td colspan='$head_cols' style='text-align:center'><h3>Cooked Food Unutlized Report</h3></td></tr>
         <tr><th colspan=3 style='text-align:left'>Total Students Dined : ".$tot_din." </th><td colspan='".($head_cols-3)."' style='text-align:right'>$print_dates</td></tr>
          
        <tr>
            <th>S.No</th>
            
            <th>Food Category</th>
            <th>Menu Item</th>
            <th>Unutilized<br>Cooked Food Weight<br>(Kg.)</th>
            <th>Item Name</th>
            <th>Issue Quantity<br>(Kg.)</th>
            <th>Unutilized Quantity<br> (Kg.)</th>
            $price_cols
        </tr>";

$sno = 0;
$tot_cost = 0;
$printed_categories = [];
$printed_food = [];
$printed_weight = [];

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
        echo "<td style='text-align:left' rowspan='" . $category_rowspan . "'>" . $issue_category_name . "</td>";
        $printed_categories[$category] = true;
    }

    // Print food name with rowspan if not printed already under this category
    if (!isset($printed_food[$category][$food])) {
        $food_rowspan = array_sum($category_food_counts[$category][$food]);
        echo "<td rowspan='" . $food_rowspan . "'>" . $food . "</td>";
        $printed_food[$category][$food] = true;
    }

    // Print food weight with rowspan if not printed already under this category and food name
    if (!isset($printed_weight[$category][$food][$food_weight])) {
        $weight_rowspan = $category_food_counts[$category][$food][$food_weight];
        echo "<td style='text-align:center' rowspan='" . $weight_rowspan . "'>" . number_format($food_weight, 2) . "</td>";
        $printed_weight[$category][$food][$food_weight] = true;
    }

    echo "<td>" . $row['itemname'] . "</td>";
    echo "<td style='text-align:center'>" . $row['issue_quantity'] . "</td>";
    echo "<td style='text-align:center'>" . $row['wastage_quantity'] . "</td>";

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

echo "</table>";

// Close database connection
mysqli_close($dbconn);
?>
