<?php
include("admindbconn.php");

// Fetch data from the issueitem table
$start_date = $_POST['issue_start_date'];
$end_date = $_POST['issue_end_date'];

$formatted_start_date = date('d-m-Y', strtotime($start_date));
$formatted_end_date = date('d-m-Y', strtotime($end_date));
$print_dates = "From: " . $formatted_start_date . " To " . $formatted_end_date;

// SQL query to fetch data
$sql = "SELECT 
    ii.issue_date,
    COALESCE(SUM(CASE WHEN M.item_category = 'grocery' THEN ii.quantity * ii.unit_price ELSE 0 END), 0) AS grocery,
    COALESCE(SUM(CASE WHEN M.item_category = 'veg' THEN ii.quantity * ii.unit_price ELSE 0 END), 0) AS vegetable,
    COALESCE(SUM(CASE WHEN M.item_category = 'nonveg' THEN ii.quantity * ii.unit_price ELSE 0 END), 0) AS nonveg,
    COALESCE(SUM(CASE WHEN M.item_category = 'milk' THEN ii.quantity * ii.unit_price ELSE 0 END), 0) AS milk,
    COALESCE(SUM(CASE WHEN M.item_category = 'bakery' THEN ii.quantity * ii.unit_price ELSE 0 END), 0) AS bakery,
    COALESCE(SUM(CASE WHEN M.item_category = 'single-use' THEN ii.quantity * ii.unit_price ELSE 0 END), 0) AS single_use,
    COALESCE(SUM(CASE WHEN M.item_category = 'gas' THEN ii.quantity * ii.unit_price ELSE 0 END), 0) AS gas,
    COALESCE(SUM(CASE WHEN M.item_category = 'firewood' THEN ii.quantity * ii.unit_price ELSE 0 END), 0) AS firewood
FROM 
    item M
LEFT JOIN 
    issueitem ii ON ii.itemid = M.id
WHERE 
    ii.issue_date BETWEEN '$start_date' AND '$end_date'
GROUP BY 
    ii.issue_date
ORDER BY 
    ii.issue_date;";

$result = mysqli_query($dbconn, $sql);

if (!$result) {
    echo "Error: " . mysqli_error($dbconn);
} else {
    echo "<div>Query executed successfully.</div>";  // Confirmation message

    // Table header
    $tbl = "<table border='1' style='border-collapse:collapse;' align='center' cellpadding='10'>
                <tr>
                    <td colspan='10' style='text-align:center'><h3>Datewise Mess Expenses </h3></td>                  
                </tr>
                <tr>
                    <td colspan='10' style='text-align:right'><b>" . $print_dates . "</b></td> 
                </tr>
                <tr>
                    <th>Date</th>
                    <th>Grocery</th>
                    <th>Vegetable</th>
                    <th>Non Veg</th>
                    <th>Milk Products</th>
                    <th>Bakery</th>
                    <th>Use and Throw</th>
                    <th>Gas</th>
                    <th>Fire Wood</th>
                    <th>Total</th>
                </tr>";

    // Initialize grand totals
    $grand_totals = [
        'grocery' => 0,
        'vegetable' => 0,
        'nonveg' => 0,
        'milk' => 0,
        'bakery' => 0,
        'single_use' => 0,
        'gas' => 0,
        'firewood' => 0
    ];

    // Process each row
    while ($row = mysqli_fetch_assoc($result)) {
        $total = 0;  // Initialize total for the current row
        $data_row = "<tr>
                        <td style='text-align:center'>" . date('d-m-Y', strtotime($row['issue_date'])) . "</td>";

        // Loop through each item category
        foreach ($grand_totals as $key => $value) {
            // Get the cost for the current category and format to 2 decimal places
            $cost = isset($row[$key]) ? number_format($row[$key], 2) : '0.00';
            
            // Append formatted cost to the data row
            $data_row .= "<td style='text-align:right'>" . $cost . "</td>";
            
            // Add the cost to the grand totals and row total
            $grand_totals[$key] += $row[$key];
            $total += $row[$key];
        }

        // Append the total for the current row and close the table row tag
        $data_row .= "<td style='text-align:right'><b>" . number_format($total, 2) . "</b></td>
                    </tr>";
        
        // Output the data row
        $tbl .= $data_row;
    }

    // Grand Total
    $grand_total = array_sum($grand_totals);
    $tbl .= "<tr>
                <td style='text-align:center'>Grand Total</td>";

    foreach ($grand_totals as $total) {
        $tbl .= "<td style='text-align:right'><b>" . number_format($total, 2) . "</b></td>";
    }

    $tbl .= "<td style='text-align:right'><b>" . number_format($grand_total, 2) . "</b></td>
            </tr>";
    
    $tbl .= "</table>";  // Close table tag
    
    // Output the table
    echo $tbl;
}

// Close database connection
mysqli_close($dbconn);
?>
