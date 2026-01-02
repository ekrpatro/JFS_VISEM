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
    YEAR(i.issue_date) AS year,
    MONTH(i.issue_date) AS month,
    ROUND(SUM(i.quantity * i.unit_price), 2) AS tot_cost,
    d.total_count
FROM 
    issueitem i
INNER JOIN 
    (
        SELECT 
            YEAR(dining_date) AS yr,
            MONTH(dining_date) AS mn,
            SUM(boys + girls) AS total_count 
        FROM 
            dining_count_status 
        WHERE 
            dining_date BETWEEN '$start_date' AND '$end_date' 
        GROUP BY 
            YEAR(dining_date), MONTH(dining_date)
    ) d ON YEAR(i.issue_date) = d.yr AND MONTH(i.issue_date) = d.mn
WHERE 
    i.issue_date BETWEEN '$start_date' AND '$end_date' 
GROUP BY 
    YEAR(i.issue_date), MONTH(i.issue_date)
ORDER BY 
    YEAR(i.issue_date), MONTH(i.issue_date) ASC";

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
            <td colspan='5' style='text-align:center'><h2>Monthwise Food Cost </h2></td>                  
        </tr>
        <tr>
            <td colspan='5' style='text-align:center'><b>Date : " . $between_dates . "</b></td>                  
        </tr>
        <tr>
            <th>S.No</th>
            <th>Month</th>			
            <th>Total Cost</th>
            <th>Student Count</th>
            <th>Perday <br>Each Student<br>Cost<br>(Rs.)</th>
        </tr>";

$sno = 0;
$grand_tot_cost = 0;
$tot_std_count=0;
$months = array(
    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 
    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 
    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
);
// Fetch and output data
while ($rec = mysqli_fetch_assoc($result)) {
    // Handle null values (e.g., if no records exist for a particular day)
    $tot_cost = isset($rec['tot_cost']) ? $rec['tot_cost'] : 0;
    $total_count = isset($rec['total_count']) ? $rec['total_count'] : 0;
    $perhead_cost = $tot_cost/$total_count;

    // Calculate grand total cost (sum of all days)
    

    // Output each row
    echo "<tr>
            <td style='text-align:center'>" . ++$sno . "</td>
            <td style='text-align:left'><b>" . $months[$rec['month']]."-".$rec['year'] . "</b></td>
            <td style='text-align:right'><b>" . number_format($tot_cost, 2) . "</b></td>
            <td style='text-align:center'><b>" . $total_count . "</b></td>
            <td style='text-align:right'><b>" . number_format($perhead_cost, 2) . "</b></td>
        </tr>";
		
}



echo "</table>";

// Close the database connection
mysqli_close($dbconn);
?>
