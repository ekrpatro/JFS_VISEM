<?php
//include("admindbconn.php");
session_start();
include("admindbconn.php");
// Session validation
if (!isset($_SESSION['user_name']) || $_SESSION["role"] != 'ADMIN') {
    echo "Invalid User";
    exit(0);
}

if (isset($_POST['action']) && $_POST['action'] == 'fetch_bills') {
  
	$query = "SELECT p.id, p.purchase_date, p.seller_id,s.shopname, p.item_category, p.invoice_no, p.total_amount,p.bill FROM purchase_bills p inner join seller s on s.seller_id=p.seller_id";
	$result = $dbconn->query($query);

	$data = [];
	while ($row = $result->fetch_assoc()) 
	{
			$data[] = $row;
	}
	echo json_encode($data);    
    exit();
}
else if (isset($_POST['action']) && $_POST['action'] == 'update_purchase_bills') {//update_purchase_bills
    $id = intval($_POST['id']);
	$seller_id = intval($_POST['seller_id']);
	$total_amount = floatval($_POST['total_amount']);    
    $invoice_no = $dbconn->real_escape_string($_POST['invoice_no']);
	$old_seller_id = intval($_POST['old_seller_id']);
	$old_invoice_no = $dbconn->real_escape_string($_POST['old_invoice_no']);
    $item_category = $dbconn->real_escape_string($_POST['item_category']);
    $purchase_date = $dbconn->real_escape_string($_POST['purchase_date']);
	$old_purchase_date = $dbconn->real_escape_string($_POST['old_purchase_date']);

    $sql = "UPDATE `purchase_bills` SET `purchase_date`='$purchase_date',`seller_id`='$seller_id',`item_category`='$item_category',
	`invoice_no`='$invoice_no',`total_amount`='$total_amount' WHERE id=".$id;

    if (mysqli_query($dbconn, $sql)) {
		$itm_msg="";
		$itmsql="UPDATE `purchaseitem` SET 
		`invoice_no`='$invoice_no',`seller_id`='$seller_id',`purchase_date`='$purchase_date' WHERE invoice_no='$old_invoice_no' and seller_id=$old_seller_id and purchase_date='$old_purchase_date'";
		 if(mysqli_query($dbconn, $itmsql))
		 {
			 $itm_msg="  Items invoice number updated...  ";
		 }
		
        echo json_encode(["status" => "success", "message" => "updated successfully ".$itm_msg.$itmsql]);
    } else {
        if ($dbconn->errno === 1062) {
            echo json_encode(["status" => "error", "message" => "Duplicate invoice number not allowed"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error: " . $dbconn->error]);
        }
    }
}
else if (isset($_POST['action']) && $_POST['action'] == 'ins_new_bill') {
	$seller_itemcat=explode(":",$_POST['seller_id']);
	
    $seller_id = intval($seller_itemcat[0]);
	$item_category = $seller_itemcat[1]; //$dbconn->real_escape_string($_POST['item_category']);
    $purchase_date = $dbconn->real_escape_string($_POST['purchase_date']);
    $invoice_no = $dbconn->real_escape_string($_POST['invoice_no']);
    
    $tot_amount = floatval($_POST['tot_amount']);

    $sql = "INSERT INTO purchase_bills (seller_id, item_category, purchase_date, invoice_no, total_amount)
            VALUES ($seller_id, '$item_category', '$purchase_date', '$invoice_no', $tot_amount)";

    if ($dbconn->query($sql)) {
        echo json_encode(["status" => "success", "message" => "New record inserted successfully"]);
    } else {
        if ($dbconn->errno === 1062) {
            echo json_encode(["status" => "error", "message" => "Duplicate invoice number not allowed"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error: " . $dbconn->error]);
        }
    }
}
else if (isset($_POST['action']) && $_POST['action'] == 'purchase_bills') {
    $seller_id = intval($_POST['seller_id']);
    $start_date = $dbconn->real_escape_string($_POST['start_date']);
    $end_date = $dbconn->real_escape_string($_POST['end_date']);
    $data = [];

    if ($seller_id == 0) {
        $sql = "SELECT `purchase_date`, p.`seller_id`, shopname, p.`item_category`, `invoice_no`,
                SUM(`total_amount`) AS tot_bill
                FROM `purchase_bills` p
                INNER JOIN seller s ON s.seller_id = p.seller_id  
                WHERE purchase_date BETWEEN '$start_date' AND '$end_date'
                GROUP BY p.seller_id";
    } else {
        $sql = "SELECT `purchase_date`, p.`seller_id`, shopname, p.`item_category`, `invoice_no`,
                SUM(`total_amount`) AS tot_bill
                FROM `purchase_bills` p
                INNER JOIN seller s ON s.seller_id = p.seller_id  
                WHERE p.seller_id = $seller_id AND purchase_date BETWEEN '$start_date' AND '$end_date'
                GROUP BY p.seller_id";
    }

    $result = $dbconn->query($sql);

    if ($result === FALSE) {
        echo json_encode(["error" => "Query failed: " . $dbconn->error]);
        exit();
    }

    $grand_tot = 0.0;
    $sno = 0;

    while ($row = $result->fetch_assoc()) {
        $grand_tot += round($row['tot_bill'], 2);
        $data[] = [
            'sno' => ++$sno,
            'seller_id' => $row['seller_id'],
            'seller_name' => $row['shopname'],
            'start_date' => $start_date,
            'end_date' => $end_date,
            'tot_bill' => $row['tot_bill'],
        ];
    }

    $data[] = [
        'sno' => ++$sno,
        'seller_id' => 'Grand Total',
        'seller_name' => '',
        'start_date' => '',
        'end_date' => '',
        'tot_bill' => round($grand_tot,2)
    ];

    echo json_encode($data);
    exit();
}
else if (isset($_POST['action']) && $_POST['action'] == 'print_datewise') {
    $start_date = $dbconn->real_escape_string($_POST['start_date']);
    $end_date = $dbconn->real_escape_string($_POST['end_date']);
    $seller_id = intval($_POST['seller_id']);

    $sql = "SELECT purchase_date, p.item_category, invoice_no, total_amount, p.seller_id, shopname
            FROM `purchase_bills` p
            INNER JOIN seller s ON p.seller_id = s.seller_id
            WHERE p.`purchase_date` BETWEEN '$start_date' AND '$end_date'";

    if ($seller_id != 0) {
        $sql .= " AND p.seller_id = $seller_id";
    }

    $sql .= " ORDER BY p.seller_id, p.`purchase_date`";

    $result = $dbconn->query($sql);

    if ($result) {
        $sno = 0;
        $grand_tot_cost = 0;

        echo "<table border='1' style='border-collapse:collapse;' align='center' cellpadding='20'>
            <thead>
            <tr>
                <td colspan='6' style='text-align:center'><h2>Purchase Bill Report</h2></td>                  
            </tr>
            <tr>
                <td colspan='6' style='text-align:center'>Date: " . htmlspecialchars($start_date) . " to " . htmlspecialchars($end_date) . "</td>                  
            </tr>
            <tr>
                <th>S.No</th>
                <th>Seller Name</th>
                <th>Purchase Date</th>
                <th>Invoice No</th>
                <th>Item Category</th>
                <th>Total Cost</th>
            </tr></thead><tbody>";

        while ($row = $result->fetch_assoc()) {
            $grand_tot_cost += $row['total_amount'];
            echo "<tr>
                <td style='text-align:center'>" . ++$sno . "</td>
                <td style='text-align:left'>" . htmlspecialchars($row['shopname']) . "</td>
                <td style='text-align:center'>" . htmlspecialchars($row['purchase_date']) . "</td>
                <td style='text-align:center'>" . htmlspecialchars($row['invoice_no']) . "</td>
                <td style='text-align:left'>" . htmlspecialchars($row['item_category']) . "</td>
                <td style='text-align:right'>" . number_format($row['total_amount'], 2) . "</td>
            </tr>";
        }

        echo "<tr>
            <td colspan='5' style='text-align:center;font-weight:bold;'>Grand Total</td>
            <td style='text-align:right;font-weight:bold;'>" . number_format($grand_tot_cost, 2) . "</td>
        </tr>";
        echo "</tbody></table>";
    } else {
        echo "Error: " . $dbconn->error;
    }
}
else if(isset($_POST['action']) && $_POST['action'] == 'print_catwise') // categorywise
{
	$start_date = mysqli_real_escape_string($dbconn, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($dbconn, $_POST['end_date']);
    $seller_id = mysqli_real_escape_string($dbconn, $_POST['seller_id']);    
    $sql = "SELECT  item_category,  sum(total_amount) as tot_amt
            FROM `purchase_bills` 
            
            WHERE `purchase_date` BETWEEN '$start_date' AND '$end_date'  ";
    if ($seller_id != '0') {
        $sql .= " AND seller_id = '$seller_id'";
    }

    $sql .= " group by item_category ORDER BY item_category";
    
    $result = mysqli_query($dbconn, $sql);

    if ($result) 
	{
        $sno = 0;
        $grand_tot_cost = 0;
		
        echo "<table border='1' style='border-collapse:collapse;' align='center' cellpadding='20'>
			<thead>
            <tr>
                <td colspan='3' style='text-align:center'><h2>Purchase Bill Report</h2></td>                  
            </tr>
            <tr>
                <td colspan='3' style='text-align:center'>Date : " . $start_date . " to " . $end_date . "</td>                  
            </tr>
            <tr>
                <th>S.No</th>               
				<th>Item Category No</th>
                <th>Total Cost</th>
            </tr></thead><tbody>";
        while ($row = mysqli_fetch_assoc($result)) {
            $grand_tot_cost += $row['tot_amt'];
            echo "<tr>
                    <td style='text-align:center'>" . ++$sno . "</td>               
                    
                    
					<td style='text-align:left'>" . $row['item_category'] . "</td>
                    <td style='text-align:right'>" . number_format($row['tot_amt'], 2) . "</td>
                </tr>";
        }
        echo "<tr>
                <td colspan='2' style='text-align:center;font-weight:bold;'>Grand Total</td>
                <td style='text-align:right;font-weight:bold;'>" . number_format($grand_tot_cost, 2) . "</td>
            </tr>";
        echo "</tbody></table>";
    }
	else 
	{
        echo "Error: " . mysqli_error($dbconn);
    }

   // $dbconn->close();	
}
else if(isset($_POST['action']) && $_POST['action'] == 'get_invoice_details') // get_invoice_details
{
	$invoice_no = mysqli_real_escape_string($dbconn, $_POST['invoice_no']);
	$shop_name = mysqli_real_escape_string($dbconn, $_POST['shop_name']);
    $purchase_date = mysqli_real_escape_string($dbconn, $_POST['purchase_date']);
    $seller_id = intVal($_POST['seller_id']);    
    $sql = "SELECT p.`invoice_no`, p.`seller_id`, p.`itemid`, p.`quantity`,  p.`rate`,i.itemname,p.`purchase_date` 
		FROM `purchaseitem` p inner join item i on p.itemid=i.id WHERE p.invoice_no='".$invoice_no."' 
		and p.purchase_date='".$purchase_date."' and p.seller_id=".$seller_id. " order by p.id ";
   
    
    $result = mysqli_query($dbconn, $sql);

    if ($result) 
	{
        $sno = 0;
        $grand_tot_cost = 0;
		
        echo "<table border='1' style='border-collapse:collapse;' align='center' cellpadding='5'>
			<thead>
				<tr>
					<td colspan='6' style='text-align:center'><h2>Goods Received Note</h2></td>                  
				</tr>
				<tr>
					<td colspan='1' style='text-align:left'>Invoice<br>Number</td>
					<td colspan=1> <b>" . $invoice_no . "</b></td>   
					<td colspan='1' style='text-align:left'>Date</td>
					<td colspan='3' style='text-align:left'> <b>" . date("d-m-Y", strtotime($purchase_date)) . "</b></td>
				</tr>
				<tr>
					<td colspan='2' style='text-align:left'>Shop Name :</td><td colspan=4> <b>" . $shop_name . "</b></td>                  
				</tr>
				<tr>
					                  
				</tr>
				
				<tr>
					<th>S.No</th>               
					<th>Item Name No</th>
					<th>Quantity</th>
					<th>Rate</th>
					<th colspan=4>Total</th>
				</tr>
			</thead>
			<tbody>";
			while ($row = mysqli_fetch_assoc($result)) 
			{
				$grand_tot_cost += ($row['quantity']*$row['rate']);
				echo "<tr>
                    <td style='text-align:center'>" . ++$sno . "</td>                    
					<td style='text-align:left'>" . $row['itemname'] . "</td>
					<td style='text-align:left'>" . $row['quantity'] . "</td>
					<td style='text-align:left'>" . $row['rate'] . "</td>
                    <td colspan=2 style='text-align:right'>" . number_format($row['quantity']*$row['rate'], 2) . "</td>
                </tr>";
        }
        echo "<tr>
                <td colspan='4' style='text-align:center;font-weight:bold;'>Grand Total</td>
                <td  colspan='2' style='text-align:right;font-weight:bold;'>" . number_format($grand_tot_cost, 2) . "</td>
            </tr>";
        echo "</tbody></table>";
    }
	else 
	{
        echo "Error: " . mysqli_error($dbconn);
    }

   // $dbconn->close();	
}

$dbconn->close();
?>