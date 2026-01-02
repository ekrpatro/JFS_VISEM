<?php
// generate_wastage_report.php
include("admindbconn.php");

// Fetch data from wastage_food table
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$formatted_start_date = date('d-m-Y', strtotime($start_date));
$formatted_end_date = date('d-m-Y', strtotime($end_date));
$print_dates = "Date : " . $formatted_start_date;
if ($start_date != $end_date) {
    $print_dates = " Date: " . $formatted_start_date . " to " . $formatted_end_date;
}
$report_type = $_POST['report_type'];

$din_q ="select boys+girls as tot_din from dining_count_status where dining_date='".$start_date."'";

$din_result = mysqli_query($dbconn, $din_q);
$tot_din=" Data Notfound";

if($din_result) 
{
	if ($row = mysqli_fetch_assoc($din_result)) 
	{
		$tot_din=$row['tot_din'];
	}
		
} 


$sql = "SELECT wastage_date, w.issue_category, food_name,food_weight, itemname, w.unit_price, w.quantity as wastage_quantity, j.quantity as issue_quantity 
        FROM wastage_food w 
        INNER JOIN item i ON i.id = w.item_id 
        INNER JOIN issueitem j ON j.itemid = w.item_id AND j.issue_category = w.issue_category AND j.issue_date = w.wastage_date
        WHERE wastage_date BETWEEN '$start_date' AND '$end_date'
        ORDER BY wastage_date";

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
} else {
    $head_cols = ($report_type == 'with_price') ? 7 : 6;
    $price_cols = ($report_type == 'with_price') ? "<th>Unit Price</th><th>Total Price</th>" : "";

    $tbl = "<table border='1' style='border-collapse:collapse;' align='center' cellpadding='10'>
            <tr><td colspan='$head_cols' style='text-align:center'><h3>Cooked Food Unutlized Report</h3></td></tr>
            <tr><th colspan=3 style='text-align:left'>Total Students Dined : ".$tot_din." </th><td colspan='".($head_cols-3)."' style='text-align:right'>$print_dates</td></tr>
            <tr>
                <th>S.No</th>
				
                <th>Food Category</th>
				
                <th>Menu Item</th>
				
                <th>Ingredients</th>
                <th>Issue<br>Quantity (Kg)</th>
                
				<th>Cooked Food <br>Unutilized (Kg)</th>
                $price_cols
            </tr>";

    echo $tbl;

    $sno = 0;
    $tot_cost = 0;
    $printed_categories = [
        'B' => false,
        'L' => false,
        'S' => false,
        'D' => false
    ];

    // Iterate through each issue category in the specified order
    foreach (['B', 'L', 'S', 'D'] as $category) {
        $found_category = false;

        while ($row = mysqli_fetch_assoc($result)) {
            if ($row['issue_category'] == $category) {
                $found_category = true;
                $issue_category_name = isset($issue_cat_arr[$row['issue_category']]) ? $issue_cat_arr[$row['issue_category']] : 'NIL';

                // Print regular data row
                $tot_cost += ($row['unit_price'] * $row['wastage_quantity']);
                $data_row = "<tr>
                                <td style='text-align:center'>" . ++$sno . "</td>
								
                                <td style='text-align:left'>" . $issue_category_name . "</td>
								 
                                <td>" . $row['food_name'] . "</td>
								
                                <td>" . $row['itemname'] . "</td>
                                <td style='text-align:center'>" . $row['issue_quantity'] . "</td>
								<td style='text-align:center'>" . number_format($row['food_weight'],2) . "</td>
                                ";   //<td style='text-align:center'>" . $row['wastage_quantity'] . "</td>

                if ($report_type == 'with_price') {
                    $data_row .= "<td style='text-align:right'>" . number_format($row['unit_price'], 2) . "</td>
                                  <td style='text-align:right'>" . number_format(($row['unit_price'] * $row['wastage_quantity']), 2) . "</td>";
                }

                echo $data_row . "</tr>";
            }
        }

        // If no data found for the current category, print "NIL WASTAGE"
        if (!$found_category) {
            echo "<tr><td style='text-align:center'>" . ++$sno . "</td><td colspan='$head_cols' style='text-align:center;font-weight:bold;'>" . $issue_cat_arr[$category] . " : Nil</td></tr>";
        }

        // Rewind result set for next category
        mysqli_data_seek($result, 0);
    }

    if ($report_type == 'with_price') {
        echo "<tr><td colspan='" . ($head_cols - 1) . "' style='text-align:center'>Total Cost</td><td style='text-align:right'>" . $tot_cost . "</td></tr>";
    }

    echo "</table>";

    if (mysqli_num_rows($result) == 0) {
        echo "NIL WASTAGE";
    }
}

// Close database connection
mysqli_close($dbconn);
?>
