<?php
include("admindbconn.php");
$data = [];
if (isset($_POST['action'])) {
    // Get the issue date from the POST request
    $issue_start_date = $_POST['issue_start_date'];   
    $issue_end_date = $_POST['issue_end_date'];  
    $item_category = $_POST['item_category'];   
	
   /* $sql = "SELECT  t1.itemid, t1.issue_date, t2. item_category,
                        SUM(t1.quantity) AS tot_quantity,t2.measurement_unit, t1.unit_price, t2.itemname, 
						t2.brand_name
                FROM issueitem t1
                INNER JOIN item t2 ON t1.itemid = t2.id  
				WHERE t1.issue_date BETWEEN ? AND ?  and t2.item_category=?
                      GROUP BY t1.itemid 
                      ORDER BY t2.disp_priority";*/
	$sql="SELECT  
    t1.itemid, t2.itemname,    
    t2.item_category,
    SUM(t1.quantity) AS tot_quantity,
    t2.measurement_unit, 
    t1.unit_price, 
    t2.itemname, 
    t2.brand_name,
    SUM(t1.quantity * t1.unit_price) AS tot_price
FROM 
    issueitem t1
INNER JOIN 
    item t2 ON t1.itemid = t2.id
WHERE 
    t1.issue_date BETWEEN ? AND ? 
    AND t2.item_category = ?
GROUP BY 
    t1.itemid,    
   t1.itemid
ORDER BY 
    tot_price DESC;";
					  
            $stmt = $dbconn->prepare($sql);
            $stmt->bind_param("sss", $issue_start_date, $issue_end_date,$item_category);
        
    

    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($dbconn->error));
    }

    $stmt->execute();

    if ($stmt->error) {
        die('Execute failed: ' . htmlspecialchars($stmt->error));
    }

    $result = $stmt->get_result();
    
   
    $grand_total = 0.0;
    $i = 1;
    while ($row = $result->fetch_assoc()) {
        $total_price = $row['unit_price'] * (float)$row['tot_quantity'];
        $grand_total += $total_price;
        $data[] = [
            'sno' => $i++,                     
            'itemid' => strtoupper($row['itemid']),
            'itemname' => strtoupper($row['itemname']),
            'brand_name' => strtoupper($row['brand_name']),            
            'quantity' => $row['tot_quantity']." ".$row['measurement_unit'],
            'unit_price' => $row['unit_price'],
            'total_price' => number_format($total_price, 2)
        ];
    }
	$data[] = [
            'sno' => '',                       
            'itemid' => '',
            'itemname' => '',
            'brand_name' => '',            
            'quantity' => '',
            'unit_price' => 'Grand Total',
            'total_price' => number_format($grand_total, 2)
        ];

    $stmt->close();
    $dbconn->close();
} 
echo json_encode($data);


?>
