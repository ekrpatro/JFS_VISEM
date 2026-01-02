<?php
include("admindbconn.php");

if(isset($_POST['action']) && $_POST['action'] == 'ins_new_item')
{
        $itemname = $_POST['itemname'];
		$brand_name = $_POST['brand_name'];
        $item_category = $_POST['item_category'];
        $measurement_unit = $_POST['measurement_unit'];
       
        $stmt = $dbconn->prepare("INSERT INTO item (itemname,brand_name,item_category, measurement_unit) VALUES (?, ?,?, ?)");
        $stmt->bind_param("ssss", $itemname,$brand_name, $item_category, $measurement_unit);

        if ($stmt->execute()) 
		{	
			$itemid=$dbconn->insert_id;
			$stock_st = $dbconn->prepare("INSERT INTO stock (itemid) VALUES (?)");
			$stock_st->bind_param("i", $itemid);
			 if ($stock_st->execute())
			 {
				 echo json_encode(["status" => "success", "message" => "New record inserted in stock and item successfully"]);
			 }
			 else
			 {
				 echo json_encode(["status" => "success", "message" => "New record inserted in  item only.."]);
			 }
			
            
        } 
		else 
		{
            echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
        }
        $stmt->close(); // Close the statement
    
}
else if(isset($_POST['action']) && $_POST['action'] == 'stock_balance')
{		
		// Retrieve POST data
		$item_id = $_POST['item_id'];
		$start_date = $_POST['start_date'];
		//$end_date = $_POST['end_date'];
		$item_cond="";
		// Condition for purchase and issue dates
		$p_date_cond = " p.purchase_date < '$start_date' ";
		$i_date_cond = " q.issue_date < '$start_date' ";
		
		if( $item_id==0)
		{
			$item_cond="";
		}
		else{
			$item_cond=" AND
						p.itemid = ? ";
		}

		
		
		
		$sql  = "SELECT 
    p.itemid,
    it.itemname,
	it.brand_name,
    ROUND(SUM(p.purchase_quantity), 2) AS tot_purchase_qty,
    ROUND(SUM(p.purchase_cost), 2) AS tot_purchase_cost,
    ROUND(SUM(i.issue_quantity), 2) AS tot_issue_qty,
    ROUND(SUM(i.issue_cost), 2) AS tot_issue_cost,
	ROUND(SUM(p.purchase_quantity), 2) - COALESCE(ROUND(SUM(i.issue_quantity), 2), 0) AS ob_qty,
    ROUND(SUM(p.purchase_cost), 2) - COALESCE(ROUND(SUM(i.issue_cost), 2), 0) AS ob_cost

    
FROM (
    SELECT 
        pi.itemid,
        SUM(pi.quantity) AS purchase_quantity,
        SUM(pi.quantity * pi.rate) AS purchase_cost
    FROM 
        purchaseitem pi
    WHERE 
        pi.purchase_date < '$start_date' ".$item_cond." 
    GROUP BY 
        pi.itemid
) p
LEFT JOIN (
    SELECT 
        ii.itemid,
        SUM(ii.quantity) AS issue_quantity,
        SUM(ii.quantity * ii.unit_price) AS issue_cost
    FROM 
        issueitem ii
    WHERE 
        ii.issue_date < '$start_date' ".$item_cond." 
    GROUP BY 
        ii.itemid
) i ON p.itemid = i.itemid
INNER JOIN 
    item it ON it.id = p.itemid
GROUP BY 
    p.itemid, it.itemname  
ORDER BY `p`.`itemid` DESC;";

		//echo $sql;

		// Prepare and bind parameters
		$stmt = $dbconn->prepare($sql);
		if($item_id != 0)
		{
			$stmt->bind_param("ii", $item_id, $item_id);
		}
		$stmt->execute();
		$result = $stmt->get_result();

		// Fetch data
		$data = [];
		$sno = 0;
		$tot_stock_cost=0.00;
		while ($row = $result->fetch_assoc()) {
			$tot_stock_cost += round($row['ob_cost'],2);
			$data[] = [
				'sno' => ++$sno,
				'itemid' => $row['itemid'],
				'itemname' => $row['itemname'],				
				'brand_name' => $row['brand_name'],	
				'tot_purchase_qty' => $row['tot_purchase_qty'],
				'tot_purchase_cost' => $row['tot_purchase_cost'],
				'tot_issue_qty' => $row['tot_issue_qty'],
				'tot_issue_cost' => $row['tot_issue_cost'],
				'ob_qty' => round($row['ob_qty'],2),
				'ob_cost' => $row['ob_qty']==0.00 ? 0.00 : round($row['ob_cost'],2)
			];
		}
		$data[] = [
				'sno' => ++$sno,
				'itemid' => "Total Stock Cost",
				'itemname' => "-",				
				'brand_name' => "-",	
				'tot_purchase_qty' => "-",
				'tot_purchase_cost' => "-",
				'tot_issue_qty' => "-",
				'tot_issue_cost' => "-",
				'ob_qty' => "-",
				'ob_cost' => round($tot_stock_cost,2)
			];

		
		echo json_encode($data);
	
       
       
}
else if (isset($_POST['action']) && $_POST['action'] == 'itemwise_balance') {
    // Retrieve and escape POST data
    $item_id = mysqli_real_escape_string($dbconn, $_POST['item_id']);
    $start_date = mysqli_real_escape_string($dbconn, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($dbconn, $_POST['end_date']);

    // Construct the SQL query with the embedded values
    $sql = "
    WITH RECURSIVE dates AS (
        -- Generate a sequence of dates from start_date to end_date
        SELECT '$start_date' AS trans_date
        UNION ALL
        SELECT DATE_ADD(trans_date, INTERVAL 1 DAY)
        FROM dates
        WHERE trans_date < '$end_date'
    ),
    purchases AS (
        -- Purchases within the specified period
        SELECT
            pi.purchase_date AS trans_date,
            SUM(pi.quantity) AS purchase_quantity
        FROM
            purchaseitem pi
        WHERE
            pi.purchase_date >= '$start_date' AND
            pi.purchase_date <= '$end_date' AND
            pi.itemid = $item_id
        GROUP BY
            pi.purchase_date
    ),
    issues AS (
        -- Issues within the specified period
        SELECT
            ii.issue_date AS trans_date,
            SUM(ii.quantity) AS issue_quantity
        FROM
            issueitem ii
        WHERE
            ii.issue_date >= '$start_date' AND
            ii.issue_date <= '$end_date' AND
            ii.itemid = $item_id
        GROUP BY
            ii.issue_date
    ),
    opening_balance AS (
        -- Opening balance before the specified period
        SELECT
            COALESCE(SUM(pi.quantity), 0) AS total_purchase_qty_before,
            COALESCE((SELECT SUM(ii.quantity) FROM issueitem ii WHERE ii.issue_date < '$start_date' AND ii.itemid = $item_id), 0) AS total_issue_qty_before
        FROM
            purchaseitem pi
        WHERE
            pi.purchase_date < '$start_date' AND
            pi.itemid = $item_id
    )

    SELECT
        ROW_NUMBER() OVER (ORDER BY d.trans_date) AS sno,
        it.id AS itemid,
        it.itemname,
        d.trans_date AS stock_date,
		st.quantity as cur_stock,
        ROUND(
            (
                (SELECT COALESCE(total_purchase_qty_before, 0) - COALESCE(total_issue_qty_before, 0) FROM opening_balance)
                + COALESCE(SUM(p.purchase_quantity) OVER (ORDER BY d.trans_date ROWS BETWEEN UNBOUNDED PRECEDING AND 1 PRECEDING), 0)
                - COALESCE(SUM(i.issue_quantity) OVER (ORDER BY d.trans_date ROWS BETWEEN UNBOUNDED PRECEDING AND 1 PRECEDING), 0)
            ), 2
        ) AS opening_balance,
        ROUND(COALESCE(p.purchase_quantity, 0), 2) AS purchase,
        ROUND(COALESCE(i.issue_quantity, 0), 2) AS issue,
        ROUND(
            (
                (SELECT COALESCE(total_purchase_qty_before, 0) - COALESCE(total_issue_qty_before, 0) FROM opening_balance)
                + COALESCE(SUM(p.purchase_quantity) OVER (ORDER BY d.trans_date), 0)
                - COALESCE(SUM(i.issue_quantity) OVER (ORDER BY d.trans_date), 0)
            ), 2
        ) AS closing_balance
    FROM dates d
    LEFT JOIN purchases p ON d.trans_date = p.trans_date
    LEFT JOIN issues i ON d.trans_date = i.trans_date
    LEFT JOIN item it ON it.id = $item_id
	LEFT JOIN stock st ON st.itemid = it.id
    ORDER BY d.trans_date;
    ";

    // Execute the SQL query
    $result = mysqli_query($dbconn, $sql);

    // Check for errors
    if (!$result) {
        error_log("SQL Error: " . mysqli_error($dbconn));
        exit;
    }

    // Fetch data and prepare the result array
    $data = [];
    $sno = 0;

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = [
            'sno' => ++$sno,
            'itemid' => isset($row['itemid']) ? $row['itemid'] : 'N/A',
            'itemname' => isset($row['itemname']) ? $row['itemname'] : 'N/A',
            'stock_date' => isset($row['stock_date']) ? $row['stock_date'] : 'N/A',
            'opening_balance' => isset($row['opening_balance']) ? $row['opening_balance'] : 'N/A',
            'purchase' => isset($row['purchase']) ? $row['purchase'] : 'N/A',
            'issue' => isset($row['issue']) ? $row['issue'] : 'N/A',
            'closing_balance' => isset($row['closing_balance']) ? $row['closing_balance'] : 'N/A',
			'cur_stock' => isset($row['cur_stock']) ? $row['cur_stock'] : 'N/A'
        ];
    }

    // Return the result as JSON
    header('Content-Type: application/json');
    echo json_encode($data);
}

else if(isset($_POST['action']) && $_POST['action'] == 'gen_po') // generate po
{
	$po_date = mysqli_real_escape_string($dbconn, $_POST['po_date']);       
    $sql = "SELECT  itemid,itemname,quantity
            FROM `stock` s inner join item i on i.id=s.itemid            
            WHERE s.`quantity` <= i.min_stock_quantity order by i.disp_priority ";  
    
    $result = mysqli_query($dbconn, $sql);
	$tbl_head="<table border='1' style='border-collapse:collapse;' align='center' cellpadding='20'>
			<thead>
            <tr>
                <td colspan='4' style='text-align:center'><h2>Purchase Order </h2></td>                  
            </tr>
            <tr>
                <td colspan='4' style='text-align:center'>Date : " . $po_date.  "</td>                  
            </tr>
            <tr>
                <th>S.No</th>               
				<th>ItemName</th>
                <th>Available<br>Quantity</th>
				<th>Required<br>Quantity</th>
				
            </tr></thead><tbody>";
    if ($result) 
	{
        $sno = 0;
        $grand_tot_cost = 0;
		
        echo $tbl_head;
        while ($row = mysqli_fetch_assoc($result)) {
           
            echo "<tr>
                    <td style='text-align:center'>" . ++$sno . "</td>                   
                    
					<td style='text-align:left'>" . $row['itemname'] . "</td>
                    <td style='text-align:right'>" . number_format($row['quantity'], 2) . "</td>
					<td style='text-align:right'></td>
                </tr>";
        }
        
        echo "</tbody></table>";
    }
	else 
	{
         echo $tbl_head.""."</tbody></table>";
    }

   // $dbconn->close();	
}
else if(isset($_POST['action']) && $_POST['action'] == 'get_purchase_details') {
    $item_id = $_POST['item_id'];
    $sql = "SELECT * FROM purchaseitem WHERE itemid = ?";
    $stmt = $dbconn->prepare($sql);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $output = "<table border='1' style='border-collapse:collapse;' cellpadding='10'>
               <thead>
               <tr>
                   <th>S.No</th>
                   <th>Item ID</th>
                   <th>Quantity</th>
                   <th>Rate</th>
                   <th>Purchase Date</th>
               </tr></thead><tbody>";
	$sno=0;
	$tot_pur_qty=0;
    while ($row = $result->fetch_assoc()) {
		++$sno;
		$tot_pur_qty += $row['quantity'];
        $output .= "<tr>
                    <td class='center-align'>{$sno}</td>
                    <td class='center-align'>{$row['itemid']}</td>
                    <td>{$row['quantity']}</td>
                    <td>{$row['rate']}</td>
                    <td>{$row['purchase_date']}</td>
                    </tr>";
    }
	

    $output .= "<tr><td colspan=2> Total  Qty :</td><td colspan=3>$tot_pur_qty</td></tr></tbody></table>";
    echo $output;
}
else if(isset($_POST['action']) && $_POST['action'] == 'get_issue_details') {
    $item_id = $_POST['item_id'];
    $sql = "SELECT * FROM issueitem WHERE itemid = ?";
    $stmt = $dbconn->prepare($sql);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $output = "<table border='1' style='border-collapse:collapse;' cellpadding='10'>
               <thead>
               <tr>
                   <th>S.No</th>
                   <th class='center-align'>Item ID</th>
                   <th class='center-align'>Quantity</th>
                   <th class='center-align'>Unit Price</th>
                   <th class='center-align'>Issue Date</th>
               </tr></thead><tbody>";
	$sno=0;
	$tot_isu_qty=0;
    while ($row = $result->fetch_assoc()) {
		++$sno;
		$tot_isu_qty += $row['quantity'];
        $output .= "<tr>
                    <td class='center-align'>{$sno}</td>
                    <td class='center-align'>{$row['itemid']}</td>
                    <td class='center-align'>{$row['quantity']}</td>
                    <td class='center-align'>{$row['unit_price']}</td>
                    <td class='center-align'>{$row['issue_date']}</td>
                    </tr>";
    }

    $output .= "<tr><td colspan=2> Total  Qty :</td><td colspan=3>$tot_isu_qty</td></tr></tbody></table>";
    echo $output;
}
else 
{
	$searchItem = isset($_GET['searchitem']) ? $_GET['searchitem'] : '';
    //echo json_encode(["status" => "error", "message" => "Invalid request"]);
	$sql = "SELECT c.*, p.* FROM stock c, item p WHERE c.itemid = p.id";
	if ($searchItem) {
			$sql .= " AND p.itemname LIKE '%$searchItem%'";
		}
	$sql .= " ORDER BY p.id";

	$result = mysqli_query($dbconn, $sql) or die('error getting data');

	$data = [];
	$grand_total = 0.0;
	$sno=0;

	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) 
	{
		$total_price = $row['unit_price'] * (float)$row['quantity'];
		$grand_total += $total_price;
		$min_stock_warning='';
		if($row['quantity'] <=  $row['min_stock_quantity'])
		{
			$min_stock_warning='MinimumStock';
		}

		$data[] = [
				'sno' => ++$sno,
				'itemid' => strtoupper($row['itemid']),
				'itemname' => strtoupper($row['itemname']),
				'unit_price' => strtoupper($row['unit_price']),
				'quantity' => $row['quantity'] . ' ' . $row['measurement_unit'],
				'total_price' => number_format($total_price, 2),
				'min_stock_quantity' => $row['min_stock_quantity'],
			    'min_stock_warning' => $min_stock_warning
			];
	}

	$data[] = [
			'sno' => ++$sno,
			'itemid' => '',
			'itemname' => 'Grand Total',
			'unit_price' => '',
			'quantity' => '',
			'total_price' => number_format($grand_total, 2),
			'min_stock_quantity' => '',
			'min_stock_warning' => ''
		];

	echo json_encode($data);
}
$dbconn->close(); 
?>
