<?php
//include("admindbconn.php");
session_start();
include("admindbconn.php");
// Session validation
if (!isset($_SESSION['user_name']) || $_SESSION["role"] != 'ADMIN') {
    echo "Invalid User";
    exit(0);
}

$data = [];
if (isset($_POST['action'])) {
    // Get the issue date from the POST request
    $issue_start_date = $_POST['issue_start_date'];   
    $issue_end_date = $_POST['issue_end_date'];  
    $item_id = $_POST['item_id'];

    if ($_POST['action'] == 'all_item') {
        $sql = "SELECT t1.id, t1.itemid, t1.issue_date, t1.issue_category AS issue_category, 
                        t1.quantity AS tot_quantity, t1.unit_price, t2.itemname, t2.brand_name
                FROM issueitem t1
                INNER JOIN item t2 ON t1.itemid = t2.id";
        if ($item_id > 0) {
            $sql .= " WHERE t1.issue_date BETWEEN ? AND ? AND t1.itemid = ? 
                      ORDER BY t1.id DESC";
            $stmt = $dbconn->prepare($sql);
            $stmt->bind_param("ssi", $issue_start_date, $issue_end_date, $item_id);
        } else {
            $sql .= " WHERE t1.issue_date BETWEEN ? AND ? 
                      ORDER BY t1.id DESC";
            $stmt = $dbconn->prepare($sql);
            $stmt->bind_param("ss", $issue_start_date, $issue_end_date);
        }
    } else {
        $sql = "SELECT t1.id, t1.itemid, t1.issue_date, 'ALL' AS issue_category,
                        SUM(t1.quantity) AS tot_quantity, t1.unit_price, t2.itemname, t2.brand_name
                FROM issueitem t1
                INNER JOIN item t2 ON t1.itemid = t2.id";
        if ($item_id > 0) {
            $sql .= " WHERE t1.issue_date BETWEEN ? AND ? AND t1.itemid = ? 
                      GROUP BY t1.itemid 
                      ORDER BY t1.id DESC";
            $stmt = $dbconn->prepare($sql);
            $stmt->bind_param("ssi", $issue_start_date, $issue_end_date, $item_id);
        } else {
            $sql .= " WHERE t1.issue_date BETWEEN ? AND ?  
                      GROUP BY t1.itemid 
                      ORDER BY t1.id DESC";
            $stmt = $dbconn->prepare($sql);
            $stmt->bind_param("ss", $issue_start_date, $issue_end_date);
        }
    }

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
            'id' => $row['id'],
            'issue_date' => strtoupper($row['issue_date']),
            'itemid' => strtoupper($row['itemid']),
            'itemname' => strtoupper($row['itemname']),
            'brand_name' => strtoupper($row['brand_name']),
            'issue_category' => strtoupper($row['issue_category']),
            'quantity' => $row['tot_quantity'],
            'unit_price' => $row['unit_price'],
            'total_price' => number_format($total_price, 2)
        ];
    }
	$data[] = [
            'sno' => $i++,
            'id' => '' ,
            'issue_date' => '',
            'itemid' => '',
            'itemname' => '',
            'brand_name' => '',
            'issue_category' => '',
            'quantity' => '',
            'unit_price' => 'Grand Total',
            'total_price' => number_format($grand_total, 2)
        ];

    $stmt->close();
    $dbconn->close();
} 
echo json_encode($data);


?>
