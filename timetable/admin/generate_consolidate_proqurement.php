<?php
include("admindbconn.php");

// Check connection
if ($dbconn->connect_error) {
    die("Connection failed: " . $dbconn->connect_error);
}

$startdate = $_POST['startdate'];
$enddate = $_POST['enddate'];

// Fetch quantities
$sql = "SELECT
    p.itemid,
    i.itemname,
    CONCAT(SUM(p.quantity), ' ', i.measurement_unit) AS total_quantity,
    SUM(p.quantity * p.rate) AS total_cost
FROM
    purchaseitem p
INNER JOIN
    item i
ON
    i.id = p.itemid
WHERE
    p.purchase_date BETWEEN ? AND ?
GROUP BY
    p.itemid
ORDER BY
    i.disp_priority ASC";

$stmt = $dbconn->prepare($sql);
$stmt->bind_param("ss", $startdate, $enddate);
$stmt->execute();
$result = $stmt->get_result();

$stmt->close();
$dbconn->close();
 $start_timestamp = strtotime($startdate);
 $disp_startdate=date('d-m-Y', $start_timestamp);
 $end_timestamp = strtotime($enddate);
 $disp_enddate=date('d-m-Y', $end_timestamp);

// Generate HTML table
echo "<table border='1' style='border-collapse:collapse;' align='center' cellpadding='5'>";
echo "<thead><tr><th colspan=5 style='text-align:center;font-weight:bold;font-size:25px;'>Consolidated Procurement Report</th></tr>";
echo "<tr><th colspan='5' style='text-align:center;font-weight:bold;font-size:22px;'>Between ".$disp_startdate." and ". $disp_enddate."</th></tr>";
echo '<tr><th>S.No</th><th>Item ID</th><th>Item Name</th><th>Quantity</th><th>Cost</th></tr>';
echo '</thead><tbody>';

$sno = 0;
$cost_total = 0;

while ($row = $result->fetch_assoc()) {
    $cost_total += $row['total_cost'];
    echo "<tr>
        <td style='text-align:center;'>" . ++$sno . "</td>
        <td style='text-align:center;'>" . htmlspecialchars($row['itemid']) . "</td>
        <td style='text-align:left;'>" . htmlspecialchars($row['itemname']) . "</td>
        <td style='text-align:right;'>" . htmlspecialchars($row['total_quantity']) . "</td>
        <td style='text-align:right;'>" . htmlspecialchars(number_format($row['total_cost'], 2)) . "</td>
    </tr>";
}

// Total cost row
echo "<tr><td colspan='4' style='text-align:right; font-weight:bold;'>Total Cost</td>
      <td style='text-align:right; font-weight:bold;'>" . htmlspecialchars(number_format($cost_total, 2)) . "</td></tr>";

echo '</tbody></table>';
?>
