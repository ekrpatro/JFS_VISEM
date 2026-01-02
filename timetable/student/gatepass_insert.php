<?php
session_start();
include("../html/dbconn.php");

// Check if the user is logged in
if (!isset($_SESSION['user_name']) || $_SESSION["role"] != 'STUDENT') {
    echo json_encode(["status" => "error", "message" => "Invalid User"]);
    exit;
}

// Validate form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $rollno = $_SESSION['user_name']; // Get roll number from session
    $name = $_SESSION['name']; // Get name from session
    $room_no = $_SESSION['room_no']; // Get room number from session
   
/*   $out_date = $_POST['out_date'];
    $out_time = $_POST['out_time'];
    $reason = $_POST['reason'];
    $in_date = $_POST['in_date'];
    $in_time = $_POST['in_time'];*/
	$out_date = filter_var($_POST['out_date'], FILTER_SANITIZE_STRING);
	$out_time = filter_var($_POST['out_time'], FILTER_SANITIZE_STRING);
	$reason = filter_var($_POST['reason'], FILTER_SANITIZE_STRING);
	$in_date = filter_var($_POST['in_date'], FILTER_SANITIZE_STRING);
	$in_time = filter_var($_POST['in_time'], FILTER_SANITIZE_STRING);
	
	// Validate date logic
    if (strtotime($in_date) < strtotime($out_date)) {
        echo json_encode(["status" => "error", "message" => "Return date cannot be before out date."]);
        exit;
    }

    if (strtotime($out_date) < strtotime(date("Y-m-d"))) {
        echo json_encode(["status" => "error", "message" => "Out date cannot be in the past."]);
        exit;
    }

    // Check for duplicate gate pass request
    /*$check_sql = "SELECT COUNT(*) FROM gatepass_tbl WHERE rollno = ? AND out_date = ?";
    $check_stmt = $dbconn->prepare($check_sql);
    $check_stmt->bind_param("ss", $rollno, $out_date);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count > 0) {
        echo json_encode(["status" => "error", "message" => "You have already submitted a request for this date."]);
        exit;
    }*/
	// Check for overlapping requests
    $check_sql = "SELECT COUNT(*) FROM gatepass_tbl 
                  WHERE rollno = ? 
                  AND (
                        (out_date BETWEEN ? AND ?) 
                        OR 
                        (in_date BETWEEN ? AND ?)
                        OR
                        (? BETWEEN out_date AND in_date)
                    )";

    $check_stmt = $dbconn->prepare($check_sql);
    $check_stmt->bind_param("ssssss", $rollno, $out_date, $in_date, $out_date, $in_date, $out_date);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count > 0) {
        echo json_encode(["status" => "error", "message" => "You already have a request that overlaps with this date range."]);
        exit;
    }

    
    // Insert into `gatepass_tbl`
    $sql = "INSERT INTO gatepass_tbl (rollno,room_no, out_date, out_time, reason, in_date, in_time, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')";

    $stmt = $dbconn->prepare($sql);
    $stmt->bind_param("sssssss", $rollno,  $room_no, $out_date, $out_time, $reason, $in_date, $in_time);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Gate pass request submitted successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gate pass request already submitted for this date!"]);
    }
    
    $stmt->close();
    $dbconn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request!"]);
}
