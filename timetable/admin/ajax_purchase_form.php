<?php
include("admindbconn.php");

$response = array('success' => false);

if (isset($_POST['submitted'])) {
    $seller_id = $_POST['seller_id'];
    $invoice_no = $_POST['invoice_no'];
    $itemid = $_POST['itemid'];
    $qty = $_POST['qty'];
    $qunit = $_POST['qunit'];
    $rate = $_POST['rate'];
    $runit = $_POST['runit'];
    $date = $_POST['date'];
    $total = $_POST['total'];
    $new_item_name = $_POST['new_item_name'];

    if ($itemid == 'others') {
        $q = "INSERT INTO item (itemname) VALUES ('$new_item_name')";
        mysqli_query($dbconn, $q);
        $itemid = mysqli_insert_id($dbconn);
    }

    $sql = "SELECT id FROM stock WHERE itemid='$itemid'";
    $result = mysqli_query($dbconn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $sqlinsert = "UPDATE stock SET unit_price = ((quantity * unit_price) + ($qty * $rate)) / (quantity + $qty), quantity = quantity + $qty WHERE itemid='$itemid'";
        if (mysqli_query($dbconn, $sqlinsert)) {
            $response['stock_update'] = true;
        } else {
            $response['stock_update'] = false;
        }
    } else {
        $sqlinsert = "INSERT INTO stock (itemid, quantity, qunit,unit_price) VALUES('$itemid','$qty','$qunit','$rate')";
        if (mysqli_query($dbconn, $sqlinsert)) {
            $response['stock_insert'] = true;
        } else {
            $response['stock_insert'] = false;
        }
    }

    $sqlinsert = "INSERT INTO purchaseitem (invoice_no, seller_id, itemid, quantity, qunit, rate, runit, purchase_date) VALUES('$invoice_no','$seller_id','$itemid','$qty','$qunit','$rate','$runit','$date')";
    if (mysqli_query($dbconn, $sqlinsert)) {
        $response['success'] = true;
    }
}

mysqli_close($dbconn);
echo json_encode($response);
?>
