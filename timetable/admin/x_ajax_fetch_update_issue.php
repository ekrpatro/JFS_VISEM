<?php
include("admindbconn.php");

if (isset($_POST['action'])) 
{
	if($_POST['action']=='fetch_issue')
	{
	
		$id = $_POST['id'];
	//SELECT `id`, `itemid`, `issue_date`, `issue_category`, `quantity`, `unit_price`, `inserted_date` FROM `issueitem` WHERE 1
		$query = "SELECT * FROM issueitem WHERE id = '$id'";
		$result = mysqli_query($dbconn, $query);

		if ($result) {
			$data = mysqli_fetch_assoc($result);
			echo json_encode($data);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'Failed to fetch data']);
		}
	}

	if($_POST['action']=='update_issue')
	{

		
		$id = $_POST['id'];
		$issue_date = $_POST['issue_date'];
		$item_id = $_POST['item_id'];
		$return_quantity = $_POST['return_quantity'];
		$unit_price = $_POST['unit_price'];
		$issue_category = $_POST['issue_category'];

		$issue_query = "UPDATE issueitem SET  quantity = quantity - '$return_quantity', unit_price = '$unit_price', issue_category = '$issue_category' WHERE id = '$id' and quantity >= $return_quantity";
		$issue_result = mysqli_query($dbconn, $issue_query);		

		if ($issue_result) 
		{
			$stock_query = "UPDATE stock SET  quantity = quantity + '$return_quantity' WHERE itemid = '$item_id'";
			$stock_result = mysqli_query($dbconn, $stock_query);
			if ($stock_result) 
				echo json_encode(['status' => 'success', 'message' => 'Issue item updated in stock successfully']);
			else
				echo json_encode(['status' => 'success', 'message' => 'Issue item not updated in stock ']);
				
		} 
		else 
		{
			echo json_encode(['status' => 'error', 'message' => 'Failed to update issue item']);
		}
	}
}
?>


