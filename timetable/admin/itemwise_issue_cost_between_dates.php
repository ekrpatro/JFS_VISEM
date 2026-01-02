<?php
include("admindbconn.php");

// Fetch data from POST request
$start_date = mysqli_real_escape_string($dbconn, $_POST['start_date']);
$end_date = mysqli_real_escape_string($dbconn, $_POST['end_date']);

// Format the start and end dates for display
$formatted_start_date = date('d-m-Y', strtotime($start_date));
$formatted_end_date = date('d-m-Y', strtotime($end_date));
$between_dates = $formatted_start_date . " and " . $formatted_end_date;

// Use the dynamic dates in the SQL query
$sql = "SELECT j.itemid, i.itemname,i.measurement_unit, SUM(j.quantity) AS tot_qty, SUM(j.unit_price * j.quantity) AS tot_price 
        FROM issueitem j
        INNER JOIN item i ON i.id = j.itemid
        WHERE j.issue_date BETWEEN '$start_date' AND '$end_date' 
        GROUP BY j.itemid order by i.disp_priority;";

// Execute the query
$result = mysqli_query($dbconn, $sql);

// Output the table
echo "<table border='1' style='border-collapse:collapse;' align='center' cellpadding='20'>
        <tr>
            <td colspan='6' style='text-align:center'><h2>Issued Itemwise Cost Report</h2></td>                  
        </tr>
        <tr>
            <td colspan='6' style='text-align:center'><b>Date: " . $between_dates . "</b></td>                  
        </tr>
        <tr>
            <th>S.No</th>
            <th>ItemCode</th>
            <th>ItemName</th>
            <th>Quantity</th>
			 <th>Unit/Cost</th>
            <th>Cost</th>
        </tr>";

$sno = 0;
$grand_tot_cost = 0;

// Check if there are results and output them
if ($result) {
    while ($rec = mysqli_fetch_assoc($result)) {
		if($rec['tot_qty'] >0)
		{
			$unit_cost=$rec['tot_price']/$rec['tot_qty'];
        echo "<tr>
                <td style='text-align:center'>" . ++$sno . "</td>
                <td style='text-align:left'>" . $rec['itemid'] . "</td>
                <td style='text-align:left'>" . $rec['itemname'] . "</td>
                <td style='text-align:left'>" . $rec['tot_qty'] ." ".$rec['measurement_unit']. "</td>
				<td style='text-align:left'>" . number_format($unit_cost, 2) . "</td>
                <td style='text-align:right'>" . number_format($rec['tot_price'], 2) . "</td>
              </tr>";
        $grand_tot_cost += $rec['tot_price'];
		}
    }
} else {
    echo "<tr><td colspan='6' style='text-align:center'>No data found for the selected dates.</td></tr>";
}

echo "<tr>
        <td colspan='4' style='text-align:center'>Total Cost</td>
        <td style='text-align:right' colspan=2><b>" . number_format($grand_tot_cost, 2) . "</b></td>
    </tr>";

echo "</table>";

// Close database connection
mysqli_close($dbconn);
?>
