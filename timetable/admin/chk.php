<?php
include("admindbconn.php");

// Check connection
if ($dbconn->connect_error) {
    die("Connection failed: " . $dbconn->connect_error);
}

// Define the date range
$start_date = $_GET['start_date'];
$end_date = $_GET['end_date'];

// SQL query to join tables, compare totals, and filter by date range
$sql = "
    SELECT 
        pb.invoice_no,pi.seller_id,
        pb.purchase_date AS bill_purchase_date,
        COALESCE(SUM(pi.quantity * pi.rate), 0) AS calculated_total_amount,
        pb.total_amount AS billed_total_amount,
        CASE
            WHEN COALESCE(SUM(pi.quantity * pi.rate), 0) = pb.total_amount THEN 'Yes'
            ELSE 'No'
        END AS match_status
    FROM 
        purchase_bills pb
    LEFT JOIN 
        purchaseitem pi ON pb.invoice_no = pi.invoice_no AND pb.purchase_date = pi.purchase_date
    WHERE
        pb.purchase_date BETWEEN ? AND ?
    GROUP BY 
        pb.invoice_no, pb.purchase_date, pb.total_amount
    ORDER BY 
		  pi.seller_id
         
";
// //pb.purchase_date,pb.invoice_no

// Prepare and execute the query
$stmt = $dbconn->prepare($sql);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

// Display results
echo "<table border='1' style='border-collapse:collapse;' align='center' cellpadding='5'>";
echo "<thead>
        <tr><th>S.no</th>
		<th>Invoice No</th> 
		<th>Seller Id</th><th>Purchase Date</th>
		<th>Calculated Total Amount</th>
		<th>Billed Total Amount</th>
		<th>Match</th></tr>
      </thead><tbody>";
	  $sno=0;

while ($row = $result->fetch_assoc()) {
	//if($row['match_status']=='No')
    echo "<tr>
		 <td style='text-align:center;'>" . (++$sno) . "</td>
        <td style='text-align:center;'>" . htmlspecialchars($row['invoice_no']) . "</td>
		<td style='text-align:center;'>" . htmlspecialchars($row['seller_id']) . "</td>
        <td style='text-align:center;'>" . htmlspecialchars($row['bill_purchase_date']) . "</td>
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
