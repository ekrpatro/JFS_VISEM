<?php

//include("admindbconn.php");

session_start();
include("admindbconn.php");
// Session validation
if (!isset($_SESSION['user_name']) || $_SESSION["role"] != 'ADMIN') {
    echo "Invalid User";
    exit(0);
}

header('Content-Type: application/json');
//if(isset($_POST['searchitem']) || isset($_POST['invoiceno']) || isset($_POST['purchase_start_date']))
if( isset($_POST['action']) && $_POST['action']=='fetch_purchase')
{

	$searchItem = isset($_POST['searchitem']) ? $_POST['searchitem'] : '';
	$invoice_no = isset($_POST['invoiceno']) ? $_POST['invoiceno'] : '';
	$purchase_start_date =isset( $_POST['purchase_start_date']) ?  $_POST['purchase_start_date'] : '';
	$purchase_end_date =isset( $_POST['purchase_end_date']) ?  $_POST['purchase_end_date'] : '';


	$sql = "SELECT c.id as cid,c.seller_id,c.purchase_date,c.rate,c.quantity,c.invoice_no,c.itemid,c.seller_id, p.*, s.shopname FROM purchaseitem c
	INNER JOIN item p ON p.id = c.itemid
	INNER JOIN seller s ON s.seller_id = c.seller_id
	WHERE c.itemid = p.id";

	if (!empty($searchItem)) {
	$sql .= " AND p.itemname LIKE '%".mysqli_real_escape_string($dbconn, $searchItem)."%'";
	}
	else if( !empty($invoice_no) &&  !empty($purchase_start_date))
	{
	$sql .= " AND c.invoice_no LIKE '%" . mysqli_real_escape_string($dbconn, $invoice_no) . "%' AND c.purchase_date between  '$purchase_start_date' and  '$purchase_end_date'";

	}
	else if(!empty($invoice_no))
	{
	$sql .= " AND c.invoice_no LIKE '%" . mysqli_real_escape_string($dbconn, $invoice_no) . "%'";
	}
	else if (!empty($purchase_start_date) )
	{
	//$sql .= " AND c.purchase_date between '%" . mysqli_real_escape_string($dbconn, $purchase_start_date) . "%'";
	$sql .= " AND c.purchase_date between  '$purchase_start_date' and  '$purchase_end_date'";
	}
	$sql .= " ORDER BY c.invoice_no,c.id desc";



	$result = mysqli_query($dbconn, $sql);

	if (!$result) {
	echo json_encode(['error' => 'Error getting data: ' . mysqli_error($dbconn)]);
	exit;
	}

	$data = [];
	$grand_total = 0.0;
	$sno = 0;

	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	$total_price = $row['rate'] * (float)$row['quantity'];
	$grand_total += $total_price;

	$data[] = [
	'sno' => ++$sno,
	'purchase_date' => $row['purchase_date'],
	'invoice_no' => $row['invoice_no'],
	'seller_id' => $row['seller_id'],
	'shopname' => strtoupper($row['shopname']),
	'itemid' => $row['itemid'],
	'itemname' => strtoupper($row['itemname']),
	'brand_name' => strtoupper($row['brand_name']),
	'unit_price' => strtoupper($row['rate']),
	'quantity' => $row['quantity'],
	'total_price' => number_format($total_price, 2),
	'id' => $row['cid']
	];
	}

	$data[] = [
	'sno' => ++$sno,
	'purchase_date' => '',
	'invoice_no' => '',
	'seller_id' => '',
	'shopname' => '',
	'itemid' => '',
	'itemname' => 'Grand Total',
	'brand_name' => '',
	'unit_price' => '',
	'quantity' => '',
	'total_price' => number_format($grand_total, 2),
	'id' => ''
	];

	echo json_encode($data);
}
else if(isset($_POST['action']) && $_POST['action']=='update_purchase')
{
	$id = intval($_POST['id']); // Ensure ID is an integer
    $invoice_no = mysqli_real_escape_string($dbconn, $_POST['invoice_no']);
    $purchase_date = mysqli_real_escape_string($dbconn, $_POST['purchase_date']);
    $rate = floatval($_POST['rate']); // Ensure rate is a float
    $itemid = intval($_POST['itemid']); // Ensure quantity is an integer
	$quantity = intval($_POST['quantity']); // Ensure quantity is an integer
	
	$old_rate = floatval($_POST['old_rate']); // Ensure rate is a float
    $old_quantity = intval($_POST['old_quantity']); // Ensure quantity is an integer
	
	$seller_id = intval($_POST['seller_id']); // Ensure quantity is an integer

    // Construct SQL update query
     $sql = "UPDATE purchaseitem
				SET invoice_no = '$invoice_no',
                purchase_date = '$purchase_date',
                seller_id = $seller_id,
				rate = $rate,
                quantity = $quantity
            WHERE id = $id";

    // Execute the query  SELECT `id`, `itemid`, `quantity`, `qunit`, `unit_price` FROM `stock` WHERE 1
    if (mysqli_query($dbconn, $sql)) {
		  $stk_sql = "UPDATE stock
				SET unit_price = ((unit_price*quantity)-($old_rate*$old_quantity)+($quantity*$rate))/(quantity-$old_quantity+$quantity),
                quantity = quantity - $old_quantity+$quantity
            WHERE itemid = $itemid";
			$stk_msg="";
			if (mysqli_query($dbconn, $stk_sql))
			{
				$stk_msg=" and stock updated...";
			}
        
        echo json_encode(['status' => 'success', 'message' => 'Update successful  '.$stk_msg]);
    } else {
        // Send a JSON response indicating failure
        echo json_encode(['status' => 'error', 'message' => 'Update failed: ' . mysqli_error($dbconn)]);
    }


}
else if (isset($_POST['action']) && $_POST['action'] == 'delete_purchase') {
    // Retrieve and sanitize input
  /*  $id = intval($_POST['id']); // Ensure ID is an integer
    $quantity = floatval($_POST['quantity']); // Ensure quantity is a float
    $item_id = intval($_POST['item_id']); // Ensure item_id is an integer    
    $deleteSql = "DELETE FROM purchaseitem WHERE id = ?";
    $updateSql = "UPDATE stock SET quantity = quantity - ? WHERE itemid = ?";

    // Start transaction
    mysqli_begin_transaction($dbconn);
    try {
        // Prepare and execute delete statement
        $stmt = mysqli_prepare($dbconn, $deleteSql);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);

        if (mysqli_affected_rows($dbconn) > 0) {
            // Prepare and execute update statement
            $stmt = mysqli_prepare($dbconn, $updateSql);
            mysqli_stmt_bind_param($stmt, 'di', $quantity, $item_id);
            mysqli_stmt_execute($stmt);

            // Commit transaction
            mysqli_commit($dbconn);

            // Send success response
            echo json_encode(['status' => 'success', 'message' => 'Deleted successfully and stock updated']);
        } else {
            // Rollback transaction if no rows were affected
            mysqli_rollback($dbconn);
            echo json_encode(['status' => 'error', 'message' => 'Deletion failed or no record found']);
        }
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($dbconn);
        echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
    }

    // Close statements
    mysqli_stmt_close($stmt);*/
}
