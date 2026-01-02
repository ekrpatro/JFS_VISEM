<?php
include("../html/dbconn.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $staying_date = $_POST['staying_date'];
	$block_name = $_POST['block_name'];
	$gender = $_POST['gender'];

    $sql = "SELECT COUNT(*) as count FROM staying_count WHERE staying_date = ? and gender=? and block_name=?";
    $stmt = $dbconn->prepare($sql);
    $stmt->bind_param("sss", $staying_date,$gender,$block_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    echo json_encode(["count" => $data["count"]]);
}
?>
