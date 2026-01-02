<?php
include("admindbconn.php");

// Fetch data from POST request
$start_date = mysqli_real_escape_string($dbconn, $_POST['start_date']);
$end_date = mysqli_real_escape_string($dbconn, $_POST['end_date']);

// Format the dates to display in a readable format
$formatted_start_date = date('d-m-Y', strtotime($start_date));
$formatted_end_date = date('d-m-Y', strtotime($end_date));

// SQL query to fetch data from both issueitem and dining_count_status tables
$sql = "SELECT 
            i.issue_date, 	
            ROUND(SUM(i.quantity * i.unit_price), 2) AS tot_cost,
            d.total_count,
            ROUND(SUM(i.quantity * i.unit_price) / d.total_count, 2) AS perhead_cost
        FROM 
            issueitem i
        INNER JOIN 
            (
                SELECT 
                    dining_date, 
                    SUM(boys + girls) AS total_count 
                FROM 
                    dining_count_status 
                WHERE 
                    dining_date BETWEEN '$start_date' AND '$end_date' 
                GROUP BY 
                    dining_date
            ) d 
        ON i.issue_date = d.dining_date
        WHERE 
            i.issue_date BETWEEN '$start_date' AND '$end_date' 
        GROUP BY 
            i.issue_date, d.total_count  
        ORDER BY 
            i.issue_date ASC";

// Determine the date range to display in the table header
if ($start_date == $end_date) {
    $between_dates = $formatted_start_date;
} else {
    $between_dates = $formatted_start_date . " to " . $formatted_end_date;
}

// Execute the SQL query
$result = mysqli_query($dbconn, $sql);

// Output the table
echo "<table border='1' style='border-collapse:collapse;' align='center' cellpadding='20'>
        <tr>
            <td colspan='5' style='text-align:center'><h2>PerHead Food Cost </h2></td>                  
        </tr>
        <tr>
            <td colspan='5' style='text-align:center'><b>Date : " . $between_dates . "</b></td>                  
        </tr>
        <tr>
            <th>S.No</th>
            <th>Issue<br>Date</th>
            <th>Total Cost</th>
            <th>Student Count</th>
            <th>Perday <br>Each Student<br>Cost<br>(Rs.)</th>
        </tr>";

$sno = 0;
$grand_tot_cost = 0;
$tot_std_count=0;

// Fetch and output data
while ($rec = mysqli_fetch_assoc($result)) {
    // Handle null values (e.g., if no records exist for a particular day)
    $tot_cost = isset($rec['tot_cost']) ? $rec['tot_cost'] : 0;
    $total_count = isset($rec['total_count']) ? $rec['total_count'] : 0;
    $perhead_cost = isset($rec['perhead_cost']) ? $rec['perhead_cost'] : 0;

    // Calculate grand total cost (sum of all days)
    $grand_tot_cost += $tot_cost;

    // Output each row
    echo "<tr>
            <td style='text-align:center'>" . ++$sno . "</td>
            <td style='text-align:left'>" . $rec['issue_date'] . "</td>
            <td style='text-align:right'>" . number_format($tot_cost, 2) . "</td>
            <td style='text-align:center'>" . $total_count . "</td>
            <td style='text-align:right'>" . number_format($perhead_cost, 2) . "</td>
        </tr>";
		$tot_std_count += $total_count;
}

// Output grand total cost if needed
echo "<tr>
        <td colspan='2' style='text-align:center'><b>Grand Total</b></td>
        <td style='text-align:right'><b>" . number_format($grand_tot_cost, 2) . "</b></td>
        <td style='text-align:center'><b>".$tot_std_count."</b></td>
		<td style='text-align:right'><b>".number_format($grand_tot_cost/$tot_std_count, 2)."</b></td>
    </tr>";

echo "</table>";

// Close the database connection
mysqli_close($dbconn);
?>
