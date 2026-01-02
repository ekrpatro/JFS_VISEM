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
    $wastage_date = isset($_POST['wastage_date']) ? $_POST['wastage_date'] : '';
    $sql = "SELECT p.id, p.wastage_date, p.issue_category, p.food_name, p.item_id, q.itemname, p.unit_price, p.quantity
            FROM wastage_food p
            INNER JOIN item q ON p.item_id = q.id
            WHERE p.wastage_date = ?
            ORDER BY p.id";
    
    $stmt = $dbconn->prepare($sql);
    $stmt->bind_param('s', $wastage_date);
    $stmt->execute();
    $result = $stmt->get_result();

    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    echo json_encode($items);
    $stmt->close();
} elseif ($action == 'delete') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $sql = "DELETE FROM wastage_food WHERE id = ?";
    
    $stmt = $dbconn->prepare($sql);
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Delete failed: " . $stmt->error]);
    }
    $stmt->close();
} 
 elseif ($action == 'update') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $wastage_date = isset($_POST['wastage_date']) ? $_POST['wastage_date'] : '';
    $issue_category = isset($_POST['issue_category']) ? $_POST['issue_category'] : '';
    $food_name = isset($_POST['food_name']) ? $_POST['food_name'] : '';
   
    $itemname = isset($_POST['itemname']) ? $_POST['itemname'] : '';
   
    $quantity = isset($_POST['quantity']) ? floatval($_POST['quantity']) : 0;

    $sql = "UPDATE wastage_food 
            SET wastage_date = ?, issue_category = ?, food_name = ?,  quantity = ? 
            WHERE id = ?";

    $stmt = $dbconn->prepare($sql);
    $stmt->bind_param('sssdi', $wastage_date, $issue_category, $food_name,  $quantity, $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Update failed: " . $stmt->error]);
    }
    $stmt->close();
}
else {
    echo json_encode(["error" => "Invalid action"]);
}

$dbconn->close();
?>
