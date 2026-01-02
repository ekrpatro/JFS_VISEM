<?php
include("../html/dbconn.php");
session_start(); // Ensure session is started

if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'fetch_students':
            $staying_date = $_POST['staying_date'];
            $query = "SELECT p.id, s.room_no, s.name, p.rollno, s.college_name 
                      FROM staying_college_hours p 
                      INNER JOIN student s ON s.rollno = p.rollno 
                      WHERE p.staying_date = ?";
            $stmt = $dbconn->prepare($query);
            $stmt->bind_param("s", $staying_date);
            $stmt->execute();
            $result = $stmt->get_result();
            echo json_encode($result->fetch_all(MYSQLI_ASSOC));
            exit();

        case 'update_student_data':
            $id = intval($_POST['id']);
            $rollno = $_POST['rollno'];
            
            $stmt = $dbconn->prepare("UPDATE staying_college_hours SET rollno=? WHERE id=?");
            $stmt->bind_param("si", $rollno, $id);
            
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
