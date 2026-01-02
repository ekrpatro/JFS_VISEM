<?php
session_start();
include("../html/dbconn.php");


// Check if the user is logged in
if (!isset($_SESSION['user_name']) || $_SESSION["role"] != 'STUDENT') {
    echo "Invalid User";
    exit(0);
}

// Validate form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rollno = $_SESSION['user_name']; // Get roll number from session
    $name = $_SESSION['name']; // Get name from session
    $room_no = $_SESSION['room_no']; // Get room number from session
    $out_date = $_POST['out_date'];
    $out_time = $_POST['out_time'];
    $reason = $_POST['reason'];
    $in_date = $_POST['in_date'];
    $in_time = $_POST['in_time'];
    
   

    // Insert into `gatepass_tbl`
    $sql = "INSERT INTO gatepass_tbl (rollno, name, room_no, out_date, out_time, reason, in_date, in_time, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')";

    $stmt = $dbconn->prepare($sql);
    $stmt->bind_param("ssssssss", $rollno, $name, $room_no, $out_date, $out_time, $reason, $in_date, $in_time);

    if ($stmt->execute()) {
        echo "<script>alert('Gate pass request submitted successfully!'); window.location.href='home_student.php';</script>";
    } else {
        echo "<script>alert('Error submitting gate pass request. Try again!'); window.history.back();</script>";
    }
//student/home_student.php
    $stmt->close();
    $dbconn->close();
} else {
    echo "Invalid request!";
}
?>
