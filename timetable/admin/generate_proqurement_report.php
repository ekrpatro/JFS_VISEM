<?php
include("admindbconn.php");

// Check connection
if ($dbconn->connect_error) {
    die("Connection failed: " . $dbconn->connect_error);
}

$startdate = $_POST['startdate'];
$enddate = $_POST['enddate'];

// Fetch unique dates
$sql_dates = "SELECT DISTINCT purchase_date FROM purchaseitem WHERE purchase_date BETWEEN ? AND ? ORDER BY purchase_date";
$stmt_dates = $dbconn->prepare($sql_dates);
$stmt_dates->bind_param("ss", $startdate, $enddate);
$stmt_dates->execute();
$result_dates = $stmt_dates->get_result();

$dates = [];
while ($row = $result_dates->fetch_assoc()) {
    $dates[] = $row['purchase_date'];
}

// Fetch unique item ids and names with priority sorting
$sql_items = "
    SELECT itemid, itemname, disp_priority,measurement_unit
    FROM (
        SELECT p.itemid, i.measurement_unit,i.itemname, i.disp_priority
        FROM purchaseitem p
        INNER JOIN item i ON i.id = p.itemid
        WHERE p.purchase_date BETWEEN ? AND ? AND i.disp_priority > 0
        
    ) AS combined_items
    GROUP BY itemid, itemname, disp_priority
    ORDER BY MAX(disp_priority) ASC, itemid
";

$stmt_items = $dbconn->prepare($sql_items);
$stmt_items->bind_param("ss", $startdate, $enddate);
$stmt_items->execute();
$result_items = $stmt_items->get_result();

$items = [];
while ($row = $result_items->fetch_assoc()) {
    $items[$row['itemid']] = ['name' => $row['itemname']."(".$row['measurement_unit'].")", 'priority' => $row['disp_priority']];
}

// Fetch quantities
$sql = "SELECT p.itemid, p.purchase_date, SUM(p.quantity) AS total_quantity 
        FROM purchaseitem p
		
        WHERE purchase_date BETWEEN ? AND ? 
        GROUP BY itemid, purchase_date 
        ORDER BY itemid, purchase_date";

$stmt = $dbconn->prepare($sql);
$stmt->bind_param("ss", $startdate, $enddate);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[$row['itemid']][$row['purchase_date']] = $row['total_quantity'];
}

$stmt->close();
$dbconn->close();

// Generate HTML table
echo "<table border='1' style='border-collapse:collapse;' align='center' cellpadding='10'>";
echo'<thead><tr><th colspan="'.(sizeof($data)+3).'">Purchase Quantity Report between '.$startdate. ' and '.$enddate.' </th></tr>';
echo '<tr><th> S.No</th><th>Item ID</th><th>Item Name</th>';

foreach ($dates as $date) {
    echo '<th>' . htmlspecialchars($date) . '</th>';
}

echo '<th> Total </th></tr></thead><tbody>';
$sno=0;
foreach ($items as $itemid => $itemdetails) {
	$item_row_total=0;
    echo '<tr><td>'.++$sno.'</td><td>' . htmlspecialchars($itemid) . '</td><td>' . htmlspecialchars($itemdetails['name']) . '</td>';
    foreach ($dates as $date) {
		$date_quantity = isset($data[$itemid][$date]) ? $data[$itemid][$date] : 0;
        echo '<td>' . (isset($data[$itemid][$date]) ? htmlspecialchars($data[$itemid][$date]) : '-') . '</td>';
		$item_row_total += $date_quantity;
    }
    echo "<td style='text-align:right; font-weight:bold;'>$item_row_total</td></tr>";
}

echo '</tbody></table>';
?>
