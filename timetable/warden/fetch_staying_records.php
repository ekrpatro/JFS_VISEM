<?php
session_start();
include("../html/dbconn.php");


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION["user_name"])) 
{
    // Validate & Sanitize Input
    $staying_date = isset($_POST['staying_date']) ? trim($_POST['staying_date']) : null;
    $college_name = isset($_POST['college_name']) ? trim($_POST['college_name']) : null;
	$gender = isset($_POST['gender']) ? trim($_POST['gender']) : null;
	$block_name = isset($_POST['block_name']) ? trim($_POST['block_name']) : null;
	
	$inserted_by=$_SESSION['user_name'];

    if (!$staying_date) {
        echo json_encode(["error" => "Staying date is required"]);
        exit;
    }

    // Prepare Query Based on College Name
	
    if ($college_name === 'ALL') {
		if($_SESSION['role']=='MANAGER')
		{
			if($gender=='B')
			{
			$query = "SELECT a.rollno, a.staying_date, room_no, UPPER(name) AS name, gender, college_name, branch, studying_year  
                  FROM staying_college_hours a 
                  INNER JOIN student b ON a.rollno = b.rollno  
                  WHERE staying_date = ? ";
				  $stmt = $dbconn->prepare($query);
		
				$stmt->bind_param("s", $staying_date);
			}
			else
			{
				$query = "SELECT a.rollno, a.staying_date, room_no, UPPER(name) AS name, gender, college_name, branch, studying_year  
                  FROM staying_college_hours a 
                  INNER JOIN student b ON a.rollno = b.rollno  
                  WHERE staying_date = ?  and gender=?";
				  $stmt = $dbconn->prepare($query);
		
				$stmt->bind_param("ss", $staying_date,$gender);
			}
		}
		else{ // warden
        $query = "SELECT a.rollno, a.staying_date, room_no, UPPER(name) AS name, gender, college_name, branch, studying_year  
                  FROM staying_college_hours a 
                  INNER JOIN student b ON a.rollno = b.rollno  
                  WHERE staying_date = ? and inserted_by=?";
				  $stmt = $dbconn->prepare($query);
		
        $stmt->bind_param("ss", $staying_date,$inserted_by);
		}
        
    } 
	else 
	{
		if($_SESSION['role']=='MANAGER')
		{
			if($gender=='B')
			{
				$query = "SELECT a.rollno, a.staying_date, room_no, UPPER(name) AS name, gender, college_name, branch, studying_year  
                  FROM staying_college_hours a 
                  INNER JOIN student b ON a.rollno = b.rollno  
                  WHERE staying_date = ? AND college_name = ? ";
				$stmt = $dbconn->prepare($query);
				$stmt->bind_param("ss", $staying_date, $college_name);
			}
			else
			{
				
					$query = "SELECT a.rollno, a.staying_date, room_no, UPPER(name) AS name, gender, college_name, branch, studying_year  
					  FROM staying_college_hours a 
					  INNER JOIN student b ON a.rollno = b.rollno  
					  WHERE staying_date = ? AND college_name = ?  and gender=?";
					$stmt = $dbconn->prepare($query);
					$stmt->bind_param("sss", $staying_date, $college_name,$gender);
				
			}
		}
		else
		{ //warden
        $query = "SELECT a.rollno, a.staying_date, room_no, UPPER(name) AS name, gender, college_name, branch, studying_year  
                  FROM staying_college_hours a 
                  INNER JOIN student b ON a.rollno = b.rollno  
                  WHERE staying_date = ? AND college_name = ? and inserted_by=?";
        $stmt = $dbconn->prepare($query);
        $stmt->bind_param("sss", $staying_date, $college_name,$inserted_by);
		}
    }
	

   

    // Execute Query
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $records = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($records); // Return JSON response
		
    } else {
        echo json_encode(["error" => "Database query failed"]);
    }

    // Close Statement
    $stmt->close();
} else {
    echo json_encode(["error" => "Unauthorized request"]);
}
?>
