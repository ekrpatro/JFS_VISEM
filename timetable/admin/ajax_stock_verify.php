<?php
include("admindbconn.php");

$sql = "
SELECT 
    i.id,
    itemname,
    COALESCE(p.tot_purchase, 0) AS tot_purchase,
    COALESCE(q.tot_issue, 0) AS tot_issue,
    COALESCE(p.tot_purchase, 0) - COALESCE(q.tot_issue, 0) AS balance,
    COALESCE(s.quantity, 0) AS quantity,
    COALESCE(s.quantity, 0) - (COALESCE(p.tot_purchase, 0) - COALESCE(q.tot_issue, 0)) AS diff_data
FROM 
    item i
LEFT JOIN 
    (SELECT itemid, SUM(quantity) AS tot_purchase FROM purchaseitem GROUP BY itemid) p 
    ON p.itemid = i.id
LEFT JOIN 
    (SELECT itemid, SUM(quantity) AS tot_issue FROM issueitem GROUP BY itemid) q 
    ON q.itemid = i.id
LEFT JOIN  
    stock s 
    ON s.itemid = i.id  
ORDER BY 
    i.id ASC
";

$result = mysqli_query($dbconn, $sql) or die('Error getting data');

$data = [];
$sno = 0;

while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $data[] = [
        'sno' => ++$sno,
        'itemid' => $row['id'],
        'itemname' => strtoupper($row['itemname']),
        'total_purchase' => $row['tot_purchase'],
        'total_issue' => $row['tot_issue'],
        'balance' => $row['balance'],
        'current_stock' => $row['quantity'],
        'diff_data' => $row['diff_data']
    ];
}

echo json_encode($data);

$dbconn->close();
?>
