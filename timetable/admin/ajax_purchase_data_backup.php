<?php

include("admindbconn.php");

header('Content-Type: application/json');
if(isset($_POST['searchitem']) || isset($_POST['invoiceno']) || isset($_POST['purchase_start_date'])) 
{

	$searchItem = isset($_POST['searchitem']) ? $_POST['searchitem'] : '';
	$invoice_no = isset($_POST['invoiceno']) ? $_POST['invoiceno'] : '';
	$purchase_start_date =isset( $_POST['purchase_start_date']) ?  $_POST['purchase_start_date'] : '';
	$purchase_end_date =isset( $_POST['purchase_end_date']) ?  $_POST['purchase_end_date'] : '';


	$sql = "SELECT c.*, p.*, s.shopname FROM purchaseitem c 
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
			'shopname' => strtoupper($row['shopname']),
			'itemid' => strtoupper($row['itemid']),
			'itemname' => strtoupper($row['itemname']),
			'brand_name' => strtoupper($row['brand_name']),
			'unit_price' => strtoupper($row['rate']),
			'quantity' => $row['quantity'],
			'total_price' => number_format($total_price, 2)
		];
	}

	$data[] = [
		'sno' => ++$sno,
		'purchase_date' => '',
		'invoice_no' => '',
		'shopname' => '',
		'itemid' => '',
		'itemname' => 'Grand Total',
		'brand_name' => '',
		'unit_price' => '',
		'quantity' => '',
		'total_price' => number_format($grand_total, 2)
	];

	echo json_encode($data);
}




?>
