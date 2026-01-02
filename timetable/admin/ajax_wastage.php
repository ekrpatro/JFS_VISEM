<?php



//include("admindbconn.php");
session_start();
include("admindbconn.php");
// Session validation
if (!isset($_SESSION['user_name']) || $_SESSION["role"] != 'ADMIN') {
    echo "Invalid User";
    exit(0);
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action == 'fetch') {
    //SELECT `id`, `wastage_date`, `issue_category`, `food_name`, `item_id`, `unit_price`, `quantity` FROM `wastage_food` WHERE 1
	if(isset($_POST['wastage_date']))
	{
		$sql = "SELECT p.id,p.`wastage_date`, p.`issue_category`, p.`food_name`, p.`item_id`,q.itemname, p.`unit_price`, p.`quantity` FROM wastage_food p inner join item q on p.item_id=q.id where p.wastage_date ='".$_POST['wastage_date']."' ORDER BY p.id";
	}
	else
	{
		$sql = "SELECT p.id,p.`wastage_date`, p.`issue_category`, p.`food_name`, p.`item_id`,q.itemname, p.`unit_price`, p.`quantity` FROM wastage_food p inner join item q on p.item_id=q.id  ORDER BY p.id";
	
	}
    $result = $dbconn->query($sql);
    if ($result === FALSE) {
        echo json_encode(["error" => "Query failed: " . $dbconn->error]);
        exit();
    }
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    echo json_encode($items);
} 
 elseif ($action == 'delete') {
    $id = intval($_POST['id']);
    $sql = "DELETE FROM wastage_food WHERE id=$id";
    if ($dbconn->query($sql) === FALSE) {
        echo json_encode(["error" => "Delete failed: " . $dbconn->error]);
        exit();
    }
    echo json_encode(["success" => true]);
}
else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $response = ["success" => false, "msg" => "Invalid Input Received"];
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
	//echo "data : ".$data;
    if($data['action']=='insert') 
	{
        $wastage_date = $data['wastage_date'];
        $food_name = $data['food_name'];
		$food_weight = $data['food_weight'];
        $issue_category = $data['issue_category'];
        $items = $data['items'];
		
        $insert_values = [];
        $total_price = 0;
		

        foreach ($items as $item) {
            $item_arr = explode(":", $item['itemName']);
            $item_id = $item_arr[0];
            $quantity = $item['quantity'];
            $unit_price = $item['unitPrice'];
            $total_price += ($unit_price * $quantity);
            $insert_values[] = "('$wastage_date', '$issue_category', '$food_name','$food_weight', '$item_id', '$unit_price', '$quantity')";
        }

        $valuesString = implode(", ", $insert_values);
        $insert_query = "INSERT INTO `wastage_food` (`wastage_date`, `issue_category`, `food_name`,`food_weight`, `item_id`, `unit_price`, `quantity`) VALUES $valuesString";
       

        if (mysqli_query($dbconn, $insert_query)) 
		{
           
            $response = ["success" => true, "msg" => "Wastage Food Name: $food_name inserted with worth $total_price"];
        } 
		else 
		{
           
            $response = ["success" => false, "msg" => "Insertion Error: " . mysqli_error($dbconn)];
        }

       
    }	
	
	else if ($data['action']=='insert_din_waste') 
	{
		$din_date = mysqli_real_escape_string($dbconn, $data['din_date']);
		$din_hall_no = (int) $data['din_hall_no'];
		$breakfast = (float) $data['breakfast'];
		$lunch = (float) $data['lunch'];
		$snacks = (float) $data['snacks'];
		$dinner = (float) $data['dinner'];

		// Prepare SQL statement
		$insert_query = "INSERT INTO `dining_wastage` (`dining_date`, `hall_no`, `breakfast`, `lunch`, `snacks`, `dinner`) VALUES (?, ?, ?, ?, ?, ?)";
		$stmt = $dbconn->prepare($insert_query);

		if ($stmt) 
		{
			// Bind parameters  s-string  i-int  d- decimal values
			$stmt->bind_param("sidddd", $din_date, $din_hall_no, $breakfast, $lunch, $snacks, $dinner);

			// Execute the query
			if ($stmt->execute()) 
			{
				$response = ["success" => true, "msg" => "Dining wastage record inserted successfully"];
			} 
			else 
			{
				$response = ["success" => false, "msg" => "Insertion Error: " . $stmt->error];
			}

			// Close statement
			$stmt->close();
		}
		else 
		{
			$response = ["success" => false, "msg" => "Database Error: " . $dbconn->error];
		}
		
	}

    echo json_encode($response);
}
elseif ($action == 'xinsert') {
    if (!isset($_POST['wastage_date']) || !isset($_POST['issue_category']) || !isset($_POST['food_name']) || !isset($_POST['items'])) {
        echo json_encode(["error" => "Incomplete data provided for insertion"]);
        exit();
    }

    $wastage_date = $dbconn->real_escape_string($_POST['wastage_date']);
    $issue_category = $dbconn->real_escape_string($_POST['issue_category']);
    $food_name = $dbconn->real_escape_string($_POST['food_name']);
    $items = json_decode($_POST['items'], true);

    // Prepare an array to store prepared statement parameters
    $params = [];

    // Build the SQL query for insertion
    $sql = "INSERT INTO wastage_food (wastage_date, issue_category, food_name, item_id, unit_price, quantity) VALUES ";

    // Add placeholders for each item
    $placeholders = [];
    foreach ($items as $index => $item) {
		$arr=explode($item['item_id'],":");
        $item_id = intval($arr[0]);
        $unit_price = floatval($item['unit_price']);
        $quantity = floatval($item['quantity']);
        
        // Add parameters for prepared statement
        $params[] = $wastage_date;
        $params[] = $issue_category;
        $params[] = $food_name;
        $params[] = $item_id;
        $params[] = $unit_price;
        $params[] = $quantity;
        
        // Add placeholder for this item
        $placeholders[] = "(?, ?, ?, ?, ?, ?)";
    }

    // Combine placeholders into the SQL query
    $sql .= implode(", ", $placeholders);

    // Prepare the statement
    $stmt = $dbconn->prepare($sql);

    // Bind parameters dynamically
    $types = str_repeat("sssdds", count($items)); // Assuming s for string, d for double (float)
    $stmt->bind_param($types, ...$params);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "msg" => "Wastage food entries successfully added"]);
    } else {
        echo json_encode(["error" => "Insert failed: " . $stmt->error]);
    }

    // Close the statement
    $stmt->close();
} else {
    echo json_encode(["error" => "Invalid action"]);
}


$dbconn->close();


?>
