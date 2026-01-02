<?php
include("admindbconn.php");

// Fetch data from POST request
$start_date = mysqli_real_escape_string($dbconn, $_POST['start_date']);
$end_date = mysqli_real_escape_string($dbconn, $_POST['end_date']);
$boys = 0;
$girls = 0;

$tot_strength = $boys + $girls;
$bgq="SELECT sum(boys+girls) as tot FROM `dining_count_status` WHERE dining_date='$start_date'";
$bg_result = mysqli_query($dbconn, $bgq);
if ($bg_result) {
    // Fetch the result
    $row = mysqli_fetch_assoc($bg_result);
    if ($row) {
        // Store the total strength
        $tot_strength = $row['tot'];
        //echo "Total strength: " . $tot_strength;
    } 
} 


if ($start_date == $end_date) {
    $formatted_start_date = date('d-m-Y', strtotime($start_date));
    $between_dates = $formatted_start_date;
    $sql = "SELECT `issue_category`, SUM(`quantity` * `unit_price`) AS tot_cost 
            FROM `issueitem` 
            WHERE `issue_date` = '$start_date' 
            GROUP BY `issue_category`";
} else {
    $formatted_start_date = date('d-m-Y', strtotime($start_date));
    $formatted_end_date = date('d-m-Y', strtotime($end_date));
    $between_dates = $formatted_start_date . " to " . $formatted_end_date;
    $sql = "SELECT `issue_category`, SUM(`quantity` * `unit_price`) AS tot_cost 
            FROM `issueitem` 
            WHERE `issue_date` BETWEEN '$start_date' AND '$end_date'
            GROUP BY `issue_category`";
}

$result = mysqli_query($dbconn, $sql);

$issue_cat_arr = [
    'B' => 'Breakfast',
    'L' => 'Lunch',
    'S' => 'Snacks',
    'D' => 'Dinner',
    'A' => 'Common'
];

// Define the desired order of categories
$desired_order = ['B', 'L', 'S', 'D', 'A'];

// Fetch all results into an associative array
$data = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[$row['issue_category']] = $row['tot_cost'];
    }
} else {
    echo "Error: " . mysqli_error($dbconn);
    exit;
}
$dayName="";
if($start_date==$end_date)
{
	

// Convert the date to a timestamp and get the day name
$dayName = date('l', strtotime($start_date)); 

//echo $dayName;  
}
// Output the table
$print_tbl= "<table border='1' style='border-collapse:collapse;' align='center' cellpadding='20'>
        <tr>
            <td colspan='3' style='text-align:center'><h2>Food Cost Report</h2></td>                  
        </tr>
        <tr>
            <td colspan='3' style='text-align:left'>Date : " . $between_dates . "</td>                  
        </tr>";
		if($dayName != "")
		{
			$print_tbl .= "<tr> <td colspan='3' style='text-align:left'>Day Name : " . $dayName . "</td> </tr>";
			//$print_tbl .= "<tr> <td colspan='3' style='text-align:left'>Cost/Head : " . $dayName . "</td> </tr>";
		}
		
        $print_tbl .=
        "<tr>
            <th>S.No</th>
            <th>Category</th>
            <th>Cost</th>
           
        </tr>";
		echo $print_tbl;

$sno = 0;
$grand_tot_cost = 0;

// Output data in the desired order
foreach ($desired_order as $category) {
    if (isset($data[$category])) {
        $cost = $data[$category];
        $grand_tot_cost += $cost;
        echo "<tr>
                <td style='text-align:center'>" . ++$sno . "</td>
                <td style='text-align:left'>" . $issue_cat_arr[$category] . "</td>
                <td style='text-align:right'>" . number_format($cost, 2) . "</td>
                
            </tr>";
    }
}

echo "<tr>
        <td colspan='2' style='text-align:center'>Total Cost</td>
        <td style='text-align:right'><b>" . number_format($grand_tot_cost, 2) . "</b></td>
		
    </tr>";
	$per_head=$grand_tot_cost/$tot_strength;
	echo "<tr>
        <td colspan='2' style='text-align:center'>Total Student</td>
        <td style='text-align:right'><b>" . $tot_strength . "</b></td>
		
    </tr>";
	echo "<tr>
        <td colspan='2' style='text-align:center'>Cost/Head</td>
        <td style='text-align:right'><b>" . number_format($per_head, 2) . "</b></td>
		
    </tr>";

echo "</table>";

// Close database connection
mysqli_close($dbconn);
?>
