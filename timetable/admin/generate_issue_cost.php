<?php
// generate_wastage_report.php
include("admindbconn.php");
// Fetch data from wastage_food table
$start_date=$_POST['start_date'];

$formatted_start_date = date('d-m-Y', strtotime($start_date));

$print_dates="Date : ".$formatted_start_date;

//$report_type="without_price";$_POST['report_type'];
$sql = "SELECT 
    M.item_category,
    COALESCE(SUM(ii.quantity * ii.unit_price), 0) AS total_issue_cost_today
FROM 
    item M
LEFT JOIN 
    issueitem ii ON ii.itemid = M.id
WHERE 
    ii.issue_date = '$start_date'
GROUP BY 
    M.item_category
ORDER BY 
    M.item_category;
";
$result = mysqli_query($dbconn, $sql);


if (!$result) 
{
		echo "Error: " . mysqli_error($dbconn);
} 
else
{
	$head_cols=3;
	if (mysqli_num_rows($result) > 0) 
	{
		$price_cols="";
		$tbl= "<table border='1' style='border-collapse:collapse;' align='center' cellpadding='10'>
				<tr>
					 <td colspan=".$head_cols." style='text-align:center'><h3>DateWise Mess Expneses</h3></td>                  
                    
                </tr>
				<tr>
					<td colspan=".($head_cols)." style='text-align:right'>".$print_dates."</td> 
					
				</tr>
                <tr>
					<th>S.No</th>                    
                    <th>Item category</th>
					<th>Cost</th>
                   </tr>";
					
		
		echo $tbl;
        // Output data of each row
		$sno=0;
		$grand_tot_exp=0;
		$name_arr=['grocery'=>'Grocery', 'veg'=>'Vegitable','nonveg'=>'Non Veg','milk'=>'Milk Products','bakery' => 'Bakery','single-use'=>'Use and Throw','gas'=>'Gas','firewood'=>'Fire Wood'];
		while ($row = mysqli_fetch_assoc($result)) 
		{
			$grand_tot_exp += $row['total_issue_cost_today'];
				$data_row= "<tr>
					 <td style='text-align:center'>" . ++$sno . "</td>
                    
					<td style='text-align:left'>" . $name_arr[$row['item_category']] . "</td>
                    <td style='text-align:right'>" . number_format($row['total_issue_cost_today'],2) . "</td>
                   </tr>";
					
               echo $data_row;
			
        }
		$data_row= "<tr>
					 <td style='text-align:center'><b>Grand Total</b></td>
                    
					<td style='text-align:right' colspan=2><b>" . number_format($grand_tot_exp,2) . "</b></td>
                    
                   </tr>";
	 echo $data_row;
		
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