<?php
//include("admindbconn.php");
session_start();
include("admindbconn.php");
// Session validation
if (!isset($_SESSION['user_name']) || $_SESSION["role"] != 'ADMIN') {
    echo "Invalid User";
    exit(0);
}


// Fetch Sellers
if (isset($_POST['action']) && $_POST['action'] == 'fetch_seller') {
    $query = "SELECT `seller_id`, `shopname`, `contact`, `address`,`item_category` FROM `seller` ORDER BY seller_id";
    $result = $dbconn->query($query);

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    // Return data in the format expected by DataTables
    echo json_encode(["data" => $data]);
}

// Update Seller
else if (isset($_POST['action']) && $_POST['action'] == 'update_seller') {
    $seller_id = intval($_POST['seller_id']);
    $shopname = $dbconn->real_escape_string($_POST['shopname']);
    $contact = $dbconn->real_escape_string($_POST['contact']);
    $address = $dbconn->real_escape_string($_POST['address']);
	$item_category = $dbconn->real_escape_string($_POST['item_category']);

    $sql = "UPDATE `seller` SET `shopname`='$shopname', `contact`='$contact', `address`='$address', `item_category`='$item_category' WHERE `seller_id`=$seller_id";

    if ($dbconn->query($sql)) {
        echo json_encode(["status" => "success", "message" => "Updated successfully"]);
    } else {
        if ($dbconn->errno === 1062) {
            echo json_encode(["status" => "error", "message" => "Duplicate not allowed"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error: " . $dbconn->error]);
        }
    }
}

// Add Seller
else if (isset($_POST['action']) && $_POST['action'] == 'add_seller') {
    $shopname = $dbconn->real_escape_string($_POST['shopname']);
    $contact = $dbconn->real_escape_string($_POST['contact']);
    $address = $dbconn->real_escape_string($_POST['address']);
	$item_category = $dbconn->real_escape_string($_POST['add_item_category']);

    $sql = "INSERT INTO `seller` (`shopname`, `contact`, `address`,`item_category`) VALUES ('$shopname', '$contact', '$address','$item_category')";

    if ($dbconn->query($sql)) {
        echo json_encode(["status" => "success", "message" => "New record inserted successfully"]);
    } else {
        if ($dbconn->errno === 1062) {
            echo json_encode(["status" => "error", "message" => "Duplicate number not allowed"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error: " . $dbconn->error]);
        }
    }
}

// Fetch Single Seller for Editing
else if (isset($_POST['action']) && $_POST['action'] == 'get_seller') {
    $seller_id = intval($_POST['seller_id']);
    $query = "SELECT `seller_id`, `shopname`, `contact`, `address`,`item_category` FROM `seller` WHERE `seller_id`=$seller_id";
    $result = $dbconn->query($query);

    if ($result) {
        $data = $result->fetch_assoc();
        echo json_encode(["data" => $data]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $dbconn->error]);
    }
}

$dbconn->close();
?>
