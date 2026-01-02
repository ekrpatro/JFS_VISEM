<?php
include("../html/dbconn.php");
session_start(); // Ensure session is started

if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'fetch_students':
            $gatepass_date = $_POST['gatepass_date'];
		$query="SELECT p.id, s.room_no, s.name, p.rollno, s.college_name,p.status,p.reason,p.out_date,p.out_time,p.in_date,p.in_time 
                      FROM gatepass_tbl p 
                      INNER JOIN student s ON s.rollno = p.rollno ";

	    if($_SESSION['role']=='MANAGER')		
		{
            		$wh_query = " WHERE p.out_date = ?";
		}
		else
		{
			$wh_query = " WHERE p.out_date = ? and s.block_name='".$_SESSION['block_name']."' and s.gender='".$_SESSION['gender']."'";
		}
		$query=$query .$wh_query;

            $stmt = $dbconn->prepare($query);
            $stmt->bind_param("s", $gatepass_date);
            $stmt->execute();
            $result = $stmt->get_result();
            echo json_encode($result->fetch_all(MYSQLI_ASSOC));
            exit();

        case 'update_gatepass':
            $id = intval($_POST['id']);
            $status = $_POST['status'];
            
            $stmt = $dbconn->prepare("UPDATE gatepass_tbl SET status=? WHERE id=?");
            $stmt->bind_param("si", $status, $id);
            
            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "Updated successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Update failed: " . $stmt->error]);
            }
            $stmt->close();
            exit();

        case 'delete_student_data':
            $id = intval($_POST['id']);
            $stmt = $dbconn->prepare("DELETE FROM staying_college_hours WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "Deleted successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Delete failed: " . $stmt->error]);
            }
            $stmt->close();
            exit();

        case 'ins_student':
            $rollno = $_POST['rollno'];
            $staying_date = date('Y-m-d');
            $inserted_by = $_SESSION['user_name'] ?? 'Unknown'; // Default if session is not set
            
            $stmt = $dbconn->prepare("INSERT INTO staying_college_hours (rollno, staying_date, inserted_by) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $rollno, $staying_date, $inserted_by);
            
            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "New record inserted successfully"]);
            } else {
                if ($dbconn->errno === 1062) {
                    echo json_encode(["status" => "error", "message" => "Duplicate number not allowed"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
                }
            }
            $stmt->close();
            exit();
    }
}

$dbconn->close();
?>
