<?php
include("admindbconn.php");
if(isset($_POST['action']) && $_POST['action'] == 'print_pricelist') // get_invoice_details
{
    $item_category = $_POST['item_category'];  

	
	$sql="SELECT distinct pi.itemid, itemname,brand_name,packing_size,measurement_unit, item_category,pi.rate AS unit_price FROM purchaseitem pi 
			INNER JOIN (  SELECT itemid, MAX(purchase_date) AS latest_purchase_date FROM purchaseitem 
			GROUP BY itemid ) AS latest_purchase ON pi.itemid = latest_purchase.itemid AND pi.purchase_date = latest_purchase.latest_purchase_date 
			inner join item j on j.id=pi.itemid ";
	if ($item_category != '0')
	{
		$sql = $sql . " and j.item_category='" . $item_category . "' and disp_priority > 0 order by item_category, j.gm_id,pi.itemid"; 
	}
	else{
	//$sql=" order by item_category,gm_id,pi.itemid;";
	$sql = $sql ." and  disp_priority > 0 and j.item_category not in ('housekeeping','equipments','services_maintenance','utensils') order by item_category,j.gm_id, pi.itemid";
    	
	}
	$result = mysqli_query($dbconn, $sql);

    if ($result) 
    {
		
		
        $sno = 0;
        $grand_tot_cost = 0;
        
        echo "
        <table style='width: 80%; margin: 0 auto; border:none;'> <!-- Centering the introductory table -->
            <tr>
                <td style='color:red; text-align:center; border:none;' colspan='5'>
                    <h2>MLR INFRASTRUCTURE PRIVATE LIMITED</h2>
                    <br><h2>PRICE LIST OF ITEMS IN STORE (HOSTEL)</h2>
                </td>
            </tr>
        </table>
        
        <table border='1' style='border-collapse:collapse; width: 80%; margin: 20px auto;' cellpadding='5'> <!-- Centering the price list table -->
            <thead>
                <tr>
                    <th>S.No</th>   
                    <th>Item Id.</th>
                    <th>Item Name</th>
					<th>Pack Size</th>
                    <th>Brand Name</th>
                    <th>Price Per Unit<BR>(Rs.)</th>                    
					<th>Total Pack Cost<br>(Rs.)</th>   
                </tr>
            </thead>
            <tbody>";
		$category_names=array("veg" => "Vegetables", "grocery" => "Grocery","nonveg" => "Non-Vegetarian","milk" => "Milk Products","bakery" => "Bakery Items","fruits"=>"Fruits","single-use"=>"Disposable Items","gas" =>"Gas","firewoods"=>"FireWoods","equipments"=>"Equipments");
               
        $item_category = "";
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
				 
				
				 echo "<tr style='height:20px;background-color:skyblue;'>
                <td style='text-align:left' colspan='7'>Item Category: " . $category_display_name. "</td> 
                </tr>";
                $item_category = $row['item_category'];
                $sno = 0;
            }
			$packing_size = floatval($row['packing_size']);
            $unit_price = floatval($row['unit_price']);
            
            // Calculate total pack cost and format it to 2 decimal places
            $total_pack_cost = $packing_size * $unit_price;
            echo "<tr>
                <td style='text-align:center'>" . ++$sno . "</td>     
                <td style='text-align:center'>" . $row['itemid'] . "</td>                    
                <td style='text-align:left'>" . $row['itemname'] . "</td>
				<td style='text-align:center'>" . $row['packing_size']." ".$row['measurement_unit'] . "</td>
                <td style='text-align:center'>" . $row['brand_name'] . "</td>                    
                <td style='text-align:right'>" . $row['unit_price'] . "</td>                    
				<td style='text-align:right'>" . number_format($total_pack_cost,2) . "</td> 
            </tr>";
			
			
        }    
        
        echo "</tbody></table>";
		
    }
    else 
    {
        echo "Error: " . mysqli_error($dbconn);
    }

     
}

$dbconn->close();
?>
