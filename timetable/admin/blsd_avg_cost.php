<?php
include("admindbconn.php");

// Fetch data from POST request
$start_date = mysqli_real_escape_string($dbconn, $_POST['start_date']);
$end_date = mysqli_real_escape_string($dbconn, $_POST['end_date']);
$boys = 1237;
$girls = 1038;

$tot_strength = $boys + $girls;
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

// Output the table
echo "<table border='1' style='border-collapse:collapse;' align='center' cellpadding='20'>
        <tr>
            <td colspan='4' style='text-align:center'><h2>Food Cost Report</h2></td>                  
        </tr>
        <tr>
            <td colspan='4' style='text-align:center'>Date : " . $between_dates . "</td>                  
        </tr>
        <tr>
            <td colspan='2' style='text-align:center'>Number of Boys</td>                  
            <td colspan='2' style='text-align:center'>" . $boys . "</td>  
        </tr>
        <tr>
            <td colspan='2' style='text-align:center'>Number of Girls</td>                  
            <td colspan='2' style='text-align:center'>" . $girls . "</td>  
        </tr>
        <tr>
            <td colspan='2' style='text-align:center'>Total Students</td>                  
            <td colspan='2' style='text-align:center'>" . ($boys + $girls) . "</td>  
        </tr>
        <tr>
            <th>S.No</th>
            <th>Category</th>
            <th>Cost</th>
            <th>Avg. Cost/Head</th>
        </tr>";

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
                <td style='text-align:right'>" . number_format($cost / $tot_strength, 2) . "</td>
            </tr>";
    }
}

echo "<tr>
        <td colspan='2' style='text-align:center'>Total Cost</td>
        <td style='text-align:right'>" . number_format($grand_tot_cost, 2) . "</td>
		<td style='text-align:right'>" . number_format($grand_tot_cost/ $tot_strength, 2) . "</td>
    </tr>";

echo "</table>";

// Close database connection
mysqli_close($dbconn);
?>
