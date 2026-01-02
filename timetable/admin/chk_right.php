<?php
include("admindbconn.php");

// Check connection
if ($dbconn->connect_error) {
    die("Connection failed: " . $dbconn->connect_error);
}

// Define the date range
$start_date = '2024-08-16';
$end_date = '2024-08-31';

// SQL query to join tables using RIGHT JOIN and filter where the match_status is 'No'
$sql = "
    SELECT 
        pi.invoice_no,
        pi.purchase_date AS item_purchase_date,
        COALESCE(SUM(pi.quantity * pi.rate), 0) AS calculated_total_amount,
        COALESCE(pb.total_amount, 0) AS billed_total_amount,
        CASE
            WHEN COALESCE(SUM(pi.quantity * pi.rate), 0) = COALESCE(pb.total_amount, 0) THEN 'Yes'
            ELSE 'No'
        END AS match_status
    FROM 
        purchaseitem pi
    Left JOIN 
        purchase_bills pb ON pi.invoice_no = pb.invoice_no AND pi.purchase_date = pb.purchase_date
    WHERE
        pb.purchase_date BETWEEN ? AND ?
    GROUP BY 
        pi.invoice_no, pi.purchase_date, pb.total_amount
    HAVING 
        match_status = 'No'
    ORDER BY 
        pi.invoice_no, pi.purchase_date
";

// Prepare and execute the query
$stmt = $dbconn->prepare($sql);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

// Display results
echo "<table border='1' style='border-collapse:collapse;' align='center' cellpadding='5'>";
echo "<thead>
        <tr><th>Invoice No</th><th>Purchase Date</th><th>Calculated Total Amount</th><th>Billed Total Amount</th><th>Match</th></tr>
      </thead><tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td style='text-align:center;'>" . htmlspecialchars($row['invoice_no']) . "</td>
        <td style='text-align:center;'>" . htmlspecialchars($row['item_purchase_date']) . "</td>
        <td style='text-align:right;'>" . htmlspecialchars(number_format($row['calculated_total_amount'], 2)) . "</td>
        <td style='text-align:right;'>" . htmlspecialchars(number_format($row['billed_total_amount'], 2)) . "</td>
        <td style='text-align:center;'>" . htmlspecialchars($row['match_status']) . "</td>
    </tr>";
}

echo '</tbody></table>';

// Close connection
$stmt->close();
$dbconn->close();
?>
