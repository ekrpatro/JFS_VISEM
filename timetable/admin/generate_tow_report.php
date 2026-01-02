<?php
// generate_wastage_report.php
//include("admindbconn.php");

session_start();
include("admindbconn.php");
// Session validation
if (!isset($_SESSION['user_name']) || $_SESSION["role"] != 'ADMIN') {
    echo "Invalid User";
    exit(0);
}

// Fetch data from wastage_food table
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$formatted_start_date = date('d-m-Y', strtotime($start_date));
$formatted_end_date = date('d-m-Y', strtotime($end_date));
$print_dates = "Date : " . $formatted_start_date;
if ($start_date != $end_date) {
    $print_dates = " Date: " . $formatted_start_date . " to " . $formatted_end_date;
}
$report_type = $_POST['report_type'];

/*$sql = "SELECT wastage_date, w.issue_category, food_name, itemname, w.unit_price, w.quantity as wastage_quantity, j.quantity as issue_quantity 
        FROM wastage_food w 
        INNER JOIN item i ON i.id = w.item_id 
        INNER JOIN issueitem j ON j.itemid = w.item_id AND j.issue_category = w.issue_category AND j.issue_date = w.wastage_date
        WHERE wastage_date BETWEEN '$start_date' AND '$end_date'
        ORDER BY wastage_date";
*/
$sql = "SELECT 
    i.gm_id,
    gm.name,
    SUM(j.quantity) AS total_issue,
    SUM(w.quantity) AS total_quantity
FROM 
    wastage_food w
INNER JOIN 
    item i ON i.id = w.item_id
INNER JOIN 
    group_item_master gm ON gm.gm_id = i.gm_id
INNER JOIN 
    issueitem j ON j.itemid = w.item_id 
    AND j.issue_category = w.issue_category 
    AND j.issue_date = w.wastage_date
WHERE 
    w.wastage_date BETWEEN '$start_date' AND '$end_date'
GROUP BY 
    i.gm_id, gm.name;
";

$result = mysqli_query($dbconn, $sql);


if (!$result) {
    echo "Error: " . mysqli_error($dbconn);
} else {
    $head_cols =  5;
   // $price_cols = ($report_type == 'with_price') ? "<th>Unit Price</th><th>Total Price</th>" : "";

    $tbl = "<table border='1' style='border-collapse:collapse;' align='center' cellpadding='10'>
			<tr><td colspan='$head_cols' style='text-align:center'><b>Cooked Food Unutilized Report</b></td></tr>
            <tr><td colspan='$head_cols' style='text-align:right'>$print_dates</td></tr>
            <tr>
                <th>S.No</th>
				<th>Item Id.</th> 				
                <th>Item Name</th>
				<th>Issue<br>Quantity</th>
                <th>Unutilized<br>Quantity</th>
               
                
            </tr>";

    echo $tbl;

    $sno = 0;
    $tot_cost = 0;
   

      

        while ($row = mysqli_fetch_assoc($result)) 
		{               
                $data_row = "<tr>
                                <td style='text-align:center'>" . ++$sno . "</td>
								<td>" . $row['gm_id'] . "</td>
                                
								 
                                <td>" . $row['name'] . "</td>
								<td style='text-align:rigth'>" . $row['total_issue'] . "</td>
                                
                                <td style='text-align:rigth'>" . $row['total_quantity'] . "</td></tr>";
                                

                echo $data_row ;
            
        }

       

       

   
    echo "</table>";

   
}

// Close database connection
mysqli_close($dbconn);
?>
