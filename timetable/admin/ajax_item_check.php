<?php
include("admindbconn.php");
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action == 'fetch') {
    //SELECT `id`, `itemname`, `item_category`, `measurement_unit`, `disp_priority` FROM `item` WHERE 1
    $sql = "SELECT * FROM item ORDER BY disp_priority";
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
    $item_category = $dbconn->real_escape_string($_POST['item_category']);
    $measurement_unit = $dbconn->real_escape_string($_POST['measurement_unit']);
	$min_stock_quantity=$dbconn->real_escape_string($_POST['min_stock_quantity']);
    
    $sql = "INSERT INTO item (itemname,brand_name, item_category,min_stock_quantity, measurement_unit) VALUES
	('$itemname','$brand_name', '$item_category','$min_stock_quantity', '$measurement_unit')";
    if ($dbconn->query($sql) === TRUE) {
        $item_id = $dbconn->insert_id;
        
        $stk_sql = "INSERT INTO stock (itemid) VALUES ('$item_id')";
        
        if ($dbconn->query($stk_sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "New Record Inserted"]);
        } else {
            echo json_encode(["error" => "Stock insertion failed: " . $dbconn->error]);
        }
    } else {
        echo json_encode(["error" => "Same Item name already available/Item insertion failed: " . $dbconn->error]);
    }
	
	
    
} elseif ($action == 'update') {
    $item_id = intval($_POST['item_id']);
    $itemname = $dbconn->real_escape_string($_POST['itemname']);
	$brand_name = $dbconn->real_escape_string($_POST['brand_name']);
    $item_category = $dbconn->real_escape_string($_POST['item_category']);
    $measurement_unit = $dbconn->real_escape_string($_POST['measurement_unit']);
	$min_stock_quantity=$dbconn->real_escape_string($_POST['min_stock_quantity']);
    $disp_priority = intval($_POST['disp_priority']);
    $sql = "UPDATE item SET itemname='$itemname',brand_name='$brand_name', item_category='$item_category',min_stock_quantity='$min_stock_quantity', measurement_unit='$measurement_unit', disp_priority=$disp_priority WHERE id=$item_id";
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
