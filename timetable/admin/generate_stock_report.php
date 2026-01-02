<?php
// generate_wastage_report.php
include("admindbconn.php");
// Fetch data from wastage_food table
$start_date=$_POST['start_date'];
$din_q ="select (boys+girls) as tot_din from dining_count_status where dining_date='".$start_date."'";

$din_result = mysqli_query($dbconn, $din_q);
$tot_din=" Data Notfound";

if($din_result) 
{
	if ($row = mysqli_fetch_assoc($din_result)) 
	{
		$tot_din=$row['tot_din'];
	}
		
} 
$formatted_start_date = date('d-m-Y', strtotime($start_date));

$print_dates="Date : ".$formatted_start_date;

//$report_type="without_price";$_POST['report_type'];
$sql = "SELECT 
    M.id,
    M.itemname,
	M.brand_name,
	M.item_category,
	M.measurement_unit,
    ROUND(COALESCE(SUM(p.purchase_quantity), 0) - COALESCE(SUM(i.issue_quantity), 0), 2) AS opening_balance,
    COALESCE(k.day_issue, 0) AS day_issue,
	COALESCE(ps.day_purchase, 0) AS day_purchase,
    ROUND(
        COALESCE(SUM(p.purchase_quantity), 0) - 
        COALESCE(SUM(i.issue_quantity), 0) - 
        COALESCE(k.day_issue, 0) + COALESCE(ps.day_purchase, 0), 
        2
    ) AS closing_balance
FROM 
    item M
LEFT JOIN (
    SELECT 
        pi.itemid,
        SUM(pi.quantity) AS purchase_quantity,
        SUM(pi.quantity * pi.rate) AS purchase_cost
    FROM 
        purchaseitem pi
    WHERE 
        pi.purchase_date < '$start_date'
    GROUP BY 
        pi.itemid
) p ON p.itemid = M.id
LEFT JOIN (
    SELECT 
        ii.itemid,
        SUM(ii.quantity) AS issue_quantity,
        SUM(ii.quantity * ii.unit_price) AS issue_cost
    FROM 
        issueitem ii
    WHERE 
        ii.issue_date < '$start_date'
    GROUP BY 
        ii.itemid
) i ON i.itemid = M.id
LEFT JOIN (
    SELECT 
        kk.itemid,
        SUM(kk.quantity) AS day_issue
    FROM 
        issueitem kk
    WHERE 
        kk.issue_date = '$start_date'
    GROUP BY 
        kk.itemid
) k ON M.id = k.itemid
LEFT JOIN (
    SELECT 
        pi.itemid,
        SUM(pi.quantity) AS day_purchase
    FROM 
        purchaseitem pi
    WHERE 
        pi.purchase_date = '$start_date'
    GROUP BY 
        pi.itemid
) ps ON M.id = ps.itemid
WHERE 
    M.disp_priority > 0 and M.item_category not in ('housekeeping','equipments','services_maintenance','utensils')
GROUP BY 
    M.id, M.itemname

ORDER BY 
    M.item_category,M.gm_id,M.id ASC;
";
$result = mysqli_query($dbconn, $sql);


if (!$result) 
{
		echo "Error: " . mysqli_error($dbconn);
} 
else
{
	$head_cols=8;
	if (mysqli_num_rows($result) > 0) 
	{
		$price_cols="";
		$tbl= "<table border='1' style='border-collapse:collapse;' align='center' cellpadding='10'>
				<tr>
					 <td colspan=".$head_cols." style='text-align:center'><h3>Stock Issue Report</h3></td>                  
                    
                </tr>
				<tr>
					<td colspan=".($head_cols)." style='text-align:right'><b>".$print_dates."</b></td> 
					
				</tr>
                <tr>
				<th>S.No</th>                    
                    <th>ItemId</th>
					<th>Item Name</th>
					<th>Brand</th>
                    <th>Opening<br>Stock</th>
                    <th>Purchase<br>Quantity</th>
					<th>Issue<br>Quantity</th>                    
                    <th>Closing<br>Stock</th></tr>
					
					<tr>
				<th colspan=4 style='text-align:left'>Total Students Dined : ".$tot_din." </th><th colspan=4 style='text-align:right'>Item Units( Kgs/Ltrs/Pieces)</th></tr>";
					
		
		echo $tbl;
        // Output data of each row
		$sno=0;
		$tot_cost=0;
		$item_category="";
		$category_names=array("veg" => "Vegetables", "grocery" => "Grocery","nonveg" => "Non-Vegetarian","milk" => "Milk Products","bakery" => "Bakery Items","fruits"=>"Fruits","single-use"=>"Disposable Items","gas" =>"Gas","firewoods"=>"FireWoods","equipments"=>"Equipments");
       
		while ($row = mysqli_fetch_assoc($result)) 
		{
			if($item_category == "" || $item_category != $row['item_category'])
            {
				if(array_key_exists($row['item_category'], $category_names)) 
				{
					$category_display_name = $category_names[$row['item_category']];  // Get the existing name
				} 
				else 
				{
					$category_display_name = $row['item_category'];  // Use the same name if key is not found
				}
				 
				//<td colspan=4><span style='color:red'>   Note: Measurement Units : (Kgs/Ltrs/Pieces)</span></td>
				
				 echo "<tr style='height:20px;background-color:skyblue;'>
                <td style='text-align:left' colspan='8'>Item Category: " . $category_display_name. " </td> 
                </tr>";
                $item_category = $row['item_category'];
                $sno = 0;
            }
			if($row['opening_balance'] > 0  || $row['day_purchase'] >0)
			{
				$data_row= "<tr>
					 <td style='text-align:center'>" . ++$sno . "</td>
                    
					<td style='text-align:center'>" . $row['id'] . "</td>
                    <td>" . $row['itemname'] . "</td>
					<td>" . $row['brand_name'] . "</td>
					
                    <td style='text-align:center'>" . $row['opening_balance']. "</td>
					<td style='text-align:center'>" . $row['day_purchase'] . "</td>
                    
                    <td style='text-align:center'>" . $row['day_issue'] . "</td>
					<td style='text-align:center'>" . $row['closing_balance'] . "</td></tr>";
					
               echo $data_row;
			}
        }
        echo "</table>";
    } 
	else 
	{
        echo "No records found";
    }
}

// Close database connection
mysqli_close($dbconn);
?>