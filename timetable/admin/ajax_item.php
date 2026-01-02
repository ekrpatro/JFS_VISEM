<?php
//include("admindbconn.php");
session_start();
include("admindbconn.php");
// Session validation
if (!isset($_SESSION['user_name']) || $_SESSION["role"] != 'ADMIN') {
    echo "Invalid User";
    exit(0);
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action == 'fetch') {
    $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
    if ($item_id > 0) {
        //$sql = "SELECT * FROM item WHERE id=$item_id";
		$sql = "SELECT id, itemname, brand_name,packing_size, item_category, min_stock_quantity, measurement_unit, disp_priority,gm_id FROM item ORDER BY disp_priority";
    $result = $dbconn->query($sql);
    } else {
        $sql = "SELECT * FROM item ORDER BY disp_priority";
    }
    $result = $dbconn->query($sql);
    if ($result === FALSE) {
        echo json_encode(["error" => "Query failed: " . $dbconn->error]);
        exit();
    }
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    echo json_encode($items);
} elseif ($action == 'add') {
    $itemname = $dbconn->real_escape_string($_POST['itemname']);
    $brand_name = $dbconn->real_escape_string($_POST['brand_name']);
	$packing_size = $dbconn->real_escape_string($_POST['packing_size']);
    $item_category = $dbconn->real_escape_string($_POST['item_category']);
    $measurement_unit = $dbconn->real_escape_string($_POST['measurement_unit']);
    $min_stock_quantity = intval($_POST['min_stock_quantity']);
	
	$result = $dbconn->query("SELECT MAX(id) AS max_id FROM item");
    $row = $result->fetch_assoc();
    $max_id = $row['max_id']; // Use 0 if no rows are found

        // Set disp_priority to max_id or a default value if the table is empty
    $disp_priority = $max_id + 1;
	$gm_id=$max_id + 1;
    
    $sql = "INSERT INTO item (itemname, brand_name,packing_size, item_category, measurement_unit, min_stock_quantity,disp_priority,gm_id) VALUES ('$itemname', '$brand_name','$packing_size', '$item_category', '$measurement_unit', $min_stock_quantity,$disp_priority,$gm_id)";
    if ($dbconn->query($sql) === TRUE) {
        $item_id = $dbconn->insert_id;
        $stk_sql = "INSERT INTO stock (itemid) VALUES ('$item_id')";
        if ($dbconn->query($stk_sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "New Record Inserted"]);
        } else {
            echo json_encode(["error" => "Stock insertion failed: " . $dbconn->error]);
        }
    } else {
        echo json_encode(["error" => "Item insertion failed: " . $dbconn->error]);
    }
} elseif ($action == 'update') {
    $item_id = intval($_POST['item_id']);
    $itemname = $dbconn->real_escape_string($_POST['itemname']);
    $brand_name = $dbconn->real_escape_string($_POST['brand_name']);
	$packing_size = $dbconn->real_escape_string($_POST['packing_size']);
    $item_category = $dbconn->real_escape_string($_POST['item_category']);
    $measurement_unit = $dbconn->real_escape_string($_POST['measurement_unit']);
    $disp_priority = intval($_POST['disp_priority']);
	$gm_id = intval($_POST['gm_id']);
    $min_stock_quantity = intval($_POST['min_stock_quantity']);
    
    $sql = "UPDATE item SET itemname='$itemname', brand_name='$brand_name',packing_size='$packing_size', item_category='$item_category', measurement_unit='$measurement_unit', disp_priority=$disp_priority, min_stock_quantity=$min_stock_quantity , gm_id=$gm_id WHERE id=$item_id";
    if ($dbconn->query($sql) === FALSE) {
        echo json_encode(["error" => "Update failed: " . $dbconn->error]);
        exit();
    }
    echo json_encode(["success" => true]);
} elseif ($action == 'delete') {
    $item_id = intval($_POST['item_id']);
    $sql = "DELETE FROM item WHERE id=$item_id";
    if ($dbconn->query($sql) === FALSE) {
        echo json_encode(["error" => "Delete failed: " . $dbconn->error]);
        exit();
    }
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Invalid action"]);
}

$dbconn->close();
?>
