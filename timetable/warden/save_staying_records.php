<?php
include("../html/dbconn.php");
session_start();

header("Content-Type: application/json");

// Check user authentication
if (!isset($_SESSION['user_name']) || $_SESSION['role'] != 'WARDEN') {
    echo json_encode(["status" => false, "message" => "Unauthorized access"]);
    exit;
}

$inserted_by = $_SESSION['user_name'];
$data = json_decode(file_get_contents("php://input"), true);


if(isset($data['staying_count']) && $data['staying_count']=='Nil')
{
	
	$query="INSERT INTO `staying_count`( `staying_date`, `block_name`, `gender`, `stay_cnt`,`inserted_by`) VALUES (?,?,?,?,?)";
	$staying_date=$data['staying_date'];
	$stay_cnt=0;
	$block_name=$data['block_name'];
	$gender=$data['gender'];
	$inserted_by=S_SESSION['user_name'];
	
	
	$stmt = $dbconn->prepare($query);
    $stmt->bind_param("sssi", $staying_date, $block_name, $gender, $stay_cnt,$inserted_by);
	if ($stmt->execute()) {
        echo json_encode(["status" => true, "message" => "No students staying during college hour Record Inserted..."]);
    }
	else 
	{
       echo json_encode(["status" => true, "message" => "Already  Record Inserted..."]);
    }
	exit;
}
// Validate input
if (!isset($data['staying_date']) || !is_array($data['rollnos']) || empty($data['rollnos'])) {
    echo json_encode(["status" => false, "message" => "Invalid input data"]);
    exit;
}
else
{
	$staying_date = filter_var($data['staying_date'], FILTER_SANITIZE_STRING);
	$rollnos = array_map(fn($r) => filter_var($r, FILTER_SANITIZE_STRING), $data['rollnos']);
	$block_name=$data['block_name'];
	$gender=$data['gender'];

	// Constructing single query with multiple rows
	$values = [];
	$placeholders = [];
	foreach ($rollnos as $rollno) {
		if (!empty($rollno)) {
			$values[] = $rollno;
			$values[] = $staying_date;
			$values[] = $inserted_by;
			$placeholders[] = "(?, ?, ?)";
		}
	}

	if (empty($placeholders)) {
		echo json_encode(["status" => false, "message" => "No valid roll numbers found"]);
		exit;
	}
	
	$stay_cnt=count($rollnos);	
	$inserted_by=$_SESSION['user_name'];
	/*$cnt_query="INSERT INTO `staying_count`( `staying_date`, `block_name`, `gender`, `stay_cnt`,inserted_by) VALUES (?,?,?,?,?)";	
	$cnt_stmt = $dbconn->prepare($cnt_query);
    $cnt_stmt->bind_param("sssis", $staying_date, $block_name, $gender, $stay_cnt,$inserted_by);
	if ($cnt_stmt->execute()) {
        $message="Number students staying during college hour =".$stay_cnt;
    }
	else 
	{
        $message="Already  Record Inserted...";
    }*/
	
	$query = "INSERT INTO `staying_college_hours` (`rollno`, `staying_date`, `inserted_by`) VALUES " . implode(", ", $placeholders);
	$stmt = mysqli_prepare($dbconn, $query);

	// Dynamically bind parameters
	$types = str_repeat("sss", count($rollnos)); // "sss" repeated for each roll number
	mysqli_stmt_bind_param($stmt, $types, ...$values);

	// Execute single query
	if (mysqli_stmt_execute($stmt)) {
		echo json_encode(["status" => true, "message" => "Records saved successfully! Count=".$stay_cnt]);
	} else {
		echo json_encode(["status" => false, "message" => "Database error: " . mysqli_error($dbconn)]);
	}

	mysqli_stmt_close($stmt);
}
?>
