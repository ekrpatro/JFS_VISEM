

<?php
include("admindbconn.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
	$response = ["success" => false, "msg" => "Invalid Input Received"];
    if ($data) 
	{
        $seller_id = $data['supplierName'];
        $invoice_number = $data['invoice_number'];
        $purchase_date = $data['purchase_date'];
		//$total_bill_amt=$data['total_bill_amt'];
        $items = $data['items'];
        $ins_count = 0;
        $msg_str = "";

        $insert_values = [];
        $update_cases_quantity = [];
        $update_cases_price = [];
        $item_ids = [];

        foreach ($items as $item) {
            $itemId = $item['itemName'];
            $quantity = $item['quantity'];
            $rate = $item['unitPrice'];
            $total = $item['totalPrice'];

            // Add to insert values array
            $insert_values[] = "('$invoice_number', '$seller_id', '$itemId', '$quantity', '$rate', '$purchase_date')";

            // Prepare CASE statements for quantity and unit price
            $update_cases_quantity[] = "WHEN itemid = '$itemId' THEN quantity + $quantity";
            $update_cases_price[] = "WHEN itemid = '$itemId' THEN ((quantity * unit_price) + ($quantity * $rate)) / (quantity + $quantity)";
            $item_ids[] = "'$itemId'";
        }

        $valuesString = implode(", ", $insert_values);
        $insert_query = "INSERT INTO `purchaseitem` (`invoice_no`, `seller_id`, `itemid`, `quantity`, `rate`, `purchase_date`) VALUES $valuesString";

        // Create the single update query using CASE statements
        $item_ids_string = implode(", ", $item_ids);
        $update_query = "
            UPDATE stock 
            SET 
                unit_price = CASE " . implode(" ", $update_cases_price) . " END,
                quantity = CASE " . implode(" ", $update_cases_quantity) . " END
                
            WHERE itemid IN ($item_ids_string)
        ";
		
        mysqli_autocommit($dbconn, false); // Start transaction

        if (mysqli_query($dbconn, $insert_query)) 
		{
            $ins_count = count($items);

            if (mysqli_query($dbconn, $update_query)) 
			{
                mysqli_commit($dbconn); // Commit transaction
                //echo "Success";
				$response["success"] = true;
				$response["msg"] = "Purchase record added successfully.";
            } 
			else 
			{
                $msg_str = "Stock updation Error: " . mysqli_error($dbconn);
                //echo "Error" . $msg_str;
				$response["success"] = false;
				$response["msg"] = $msg_str ; //"Error: " . mysqli_error($dbconn);
                mysqli_rollback($dbconn); // Rollback transaction
            }
        } else {
            $msg_str = "Insertion Error: " . mysqli_error($dbconn);
            //echo "Error ";
			$response["success"] = false;
			$response["msg"] = $msg_str; //"Error: " . mysqli_error($dbconn);
			
            mysqli_rollback($dbconn); // Rollback transaction
        }

        mysqli_autocommit($dbconn, true); // End transaction
    }
	echo json_encode($response);
}

?>



