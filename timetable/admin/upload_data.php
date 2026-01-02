<?php
include("../html/dbconn.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST["submit_room"])) {
    if ($_FILES["csv_file"]["error"] == 0) {
        $filename = $_FILES["csv_file"]["tmp_name"];
        $file = fopen($filename, "r");
        fgetcsv($file); // Skip header

        $inserted = 0;
        while (($row = fgetcsv($file)) !== false) {
            $row = array_map('trim', $row);
            list($id, $ay, $sec_id, $day_no, $period, $room_no) = $row;

            $stmt = $dbconn->prepare("INSERT INTO `room_tt`(`ay`, `sec_id`, `day_no`, `period`, `room_no`) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("siiis", $ay, $sec_id, $day_no, $period, $room_no);
            if ($stmt->execute()) {
                $inserted++;
            }
        }

        fclose($file);
        echo "<div class='alert alert-success'>Successfully inserted $inserted room records.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error uploading room CSV file.</div>";
    }
}

if (isset($_POST["submit_predefined"])) {
    if ($_FILES["csv_file"]["error"] == 0) {
        $filename = $_FILES["csv_file"]["tmp_name"];
        $file = fopen($filename, "r");
        fgetcsv($file);

        $inserted = 0;
        while (($row = fgetcsv($file)) !== false) {
            $row = array_map('trim', $row);
            list($id, $ay, $sec_id, $subject_code, $day_no, $st_period, $end_period, $staff_idno, $room_no, $slot_status,$sem_type,$faculty_type) = $row;

            $stmt = $dbconn->prepare("INSERT INTO predefined_timetable (ay, sec_id, subject_code, day_no, st_period, end_period, staff_idno, room_no, slot_status,sem_type,faculty_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)");
            $stmt->bind_param("sisiiississ", $ay, $sec_id, $subject_code, $day_no, $st_period, $end_period, $staff_idno, $room_no, $slot_status,$sem_type,$faculty_type);
            if ($stmt->execute()) {
                $inserted++;
            }
        }

        fclose($file);
        echo "<div class='alert alert-success'>Successfully inserted $inserted predefined timetable records.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error uploading predefined CSV file.</div>";
    }
}

if (isset($_POST["submit_subject_staff"])) {
    if ($_FILES["csv_file"]["error"] == 0) {
        $filename = $_FILES["csv_file"]["tmp_name"];
        $file = fopen($filename, "r");
        fgetcsv($file);

        $inserted = 0;
        while (($row = fgetcsv($file)) !== false) {
            $row = array_map('trim', $row);
            list($id, $ay, $sec_id, $subject_code, $staff_idno, $teaching_periods) = $row;

            $stmt = $dbconn->prepare("INSERT INTO subject_staff_mapping (ay, sec_id, subject_code, staff_idno, teaching_periods) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sissi", $ay, $sec_id, $subject_code, $staff_idno, $teaching_periods);
            if ($stmt->execute()) {
                $inserted++;
            }
        }

        fclose($file);
        echo "<div class='alert alert-success'>Successfully inserted $inserted subject-staff mapping records.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error uploading subject-staff CSV file.</div>";
    }
}

/*
if (isset($_POST["submit_update_data"])) {
	$ay = $_POST['ay'];
    $sec_id = intval($_POST['sec_id']);
    $subject_code = $_POST['subject_code'];
	$staff_idno = $_POST['staff_idno'];
	
	$faculty_type = $_POST['faculty_type'];
	$q = "update subject_staff_mapping set staff_idno=?,subject_code=? WHERE ay=? AND sec_id =? ";
     
	
}*/

if (isset($_POST["submit_delete_data"])) {
    $dept_id = intval($_POST['dept_id']);
    $data_type = $_POST['data_type'];
	$sem = $_POST['sem'];
	$course_id = $_POST['course_id'];
    $ay = $_POST['ay']; // You may change this dynamically if needed
	$delete_password = $_POST['delete_password'];

    // Replace this with your real password logic or use hashed password
    $expected_password = "123";

    if ($delete_password !== $expected_password) 
	{
        echo "<div class='alert alert-danger'>❌ Incorrect delete password. Action aborted.</div>";
        return;
    }

    $q = "";
    if ($data_type == 'subject-staffs')
	{
		if($dept_id > 0)
		{
			$q = "DELETE FROM subject_staff_mapping WHERE ay=? AND sec_id IN (SELECT sec_id FROM sec WHERE dept_id=? and course_id=? and sem=?)";
		}
		else
		{
			$q = "DELETE FROM subject_staff_mapping WHERE  ay=? AND sec_id IN (SELECT sec_id FROM sec WHERE dept_id > ? and course_id=? and sem=?)";
		}
    } 
	elseif ($data_type == 'predefined') 
	{
		if($dept_id > 0)
		{
			$q = "DELETE FROM predefined_timetable WHERE ay=? AND sec_id IN (SELECT sec_id FROM sec WHERE dept_id=? and course_id=? and sem=?)";
		}
		else
		{
			$q = "DELETE FROM predefined_timetable WHERE ay=? AND sec_id IN (SELECT sec_id FROM sec WHERE dept_id > ? and course_id=? and sem=?)";
		}
		
    } 
	elseif ($data_type == 'rooms') 
	{
		if($dept_id > 0)
		{
			$q = "DELETE FROM room_tt WHERE ay=? AND sec_id IN (SELECT sec_id FROM sec WHERE dept_id=? and course_id=? and sem=?)";
		}
		else
		{
			$q = "DELETE FROM room_tt WHERE ay=? AND sec_id IN (SELECT sec_id FROM sec WHERE dept_id > ? and course_id=? and sem=?)";
		}
    }

    if ($q !== "")
	{
        $stmt = $dbconn->prepare($q);
        $stmt->bind_param("siii", $ay, $dept_id, $course_id, $sem);
        if ($stmt->execute()) 
		{
            echo "<div class='alert alert-success'>Data deleted successfully from '$data_type'.</div>";
        } 
		else 
		{
            echo "<div class='alert alert-danger'>Failed to delete data.</div>";
        }
    } 
	else 
	{
        echo "<div class='alert alert-warning'>Invalid data type selected.</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>CSV Upload & Data Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
        }
        h2 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            font-weight: bold;
            color: #343a40;
        }
        .card {
            margin-bottom: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .card-header {
            font-weight: 600;
            font-size: 1.2rem;
            background-color: #007bff;
            color: white;
            border-radius: 10px 10px 0 0;
        }
        .btn {
            width: 100%;
        }
        .form-group {
            margin-bottom: 1.2rem;
        }
    </style>
</head>
<body class="container mt-5">

    <!-- Room Upload -->
    <div class="card">
        <div class="card-header">🏫 Upload Room Timetable</div>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Select CSV File:</label>
                    <input type="file" name="csv_file" accept=".csv" class="form-control" required>
                </div>
                <button type="submit" name="submit_room" class="btn btn-primary">Upload Rooms</button>
            </form>
        </div>
    </div>

    <!-- Predefined Upload -->
    <div class="card">
        <div class="card-header">📅 Upload Predefined Timetable</div>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Select CSV File:</label>
                    <input type="file" name="csv_file" accept=".csv" class="form-control" required>
                </div>
                <button type="submit" name="submit_predefined" class="btn btn-success">Upload Predefined</button>
            </form>
        </div>
    </div>

    <!-- Subject Staff Mapping -->
    <div class="card">
        <div class="card-header">👩‍🏫 Upload Subject-Staff Mapping</div>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Select CSV File:</label>
                    <input type="file" name="csv_file" accept=".csv" class="form-control" required>
                </div>
                <button type="submit" name="submit_subject_staff" class="btn btn-warning text-white">Upload Subject-Staff</button>
            </form>
        </div>
    </div>

    <!-- Delete Data -->
    <div class="card">
        <div class="card-header bg-danger text-white">🗑️ Delete Timetable Data</div>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
				<div class="form-group">
                    <label>Select AY:</label>
					<select name="ay" class="form-control">
                        <option value="2025-26">2025-26</option>
                        
					</select>
				</div>
				<div class="form-group">
                    <label>Select Programme:</label>
                    <select name="course_id" class="form-control">
                        <option value="1">B.Tech</option>
                        <option value="2">M.TECH</option>
						<option value="3">MBA</option>
						
                    </select>
                </div>
                <div class="form-group">
                    <label>Select Department:</label>
                    <select name="dept_id" class="form-control">
                        <option value="1">CIVIL</option>
                        <option value="2">EEE</option>
                        <option value="3">MECH</option>
                        <option value="4">ECE</option>
                        <option value="5">CSE</option>
                        <option value="6">IT</option>
                        <option value="7">AERO</option>
                        <option value="34">AIML</option>
                        <option value="35">Data Science</option>
                        <option value="36">Cyber Security</option>
                        <option value="37">CSIT</option>
						<option value="9">MBA</option>
						<option value="0">ALL</option>
                    </select>
                </div>
				<div class="form-group">
                    <label>Select Semester:</label>
                    <select name="sem" class="form-control">
                        <option value="1">I</option>
                        <option value="2">II</option>
						<option value="3">III</option>
						<option value="4">IV</option>
						<option value="5">V</option>
						<option value="6">VI</option>
						<option value="7">VII</option>
						<option value="8">VIII</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Select Data Type:</label>
                    <select name="data_type" class="form-control">
                        <option value="subject-staffs">Subject Staff Mapping</option>
                        <option value="predefined">Predefined Timetable</option>
                        <option value="rooms">Rooms</option>
                    </select>
                </div>
				<div>
				<label>Enter password:</label>
				  <input type="password" name="delete_password" class="form-control" required>
				</div>
                <button type="submit" name="submit_delete_data" class="btn btn-danger"
    onclick="return confirm('⚠️ Are you sure you want to delete the selected data? This action cannot be undone.');">
    Delete Selected Data
</button>
            </form>
        </div>
    </div>
	
	<!-- updata data -->
	<!--
    <div class="card">
        <div class="card-header bg-danger text-white">🗑️ Update staff mapping Data</div>
        <div class="card-body">
            <form method="post" >
				<div class="form-group">
                    <label>Select AY:</label>
					<select name="ay" class="form-control">
                        <option value="2025-26">2025-26</option>                       
					</select>
				</div>
				<div class="form-group">
                    <label>Enter sec id </label>
					<input type='number' name='sec_id' class="form-control">
                    
                </div>
				<div class="form-group">
                    <label>Enter staff_idno </label>
					<input type='text' name='staff_idno' class="form-control">
                    
                </div>
                <div class="form-group">
                    <label>Enter subject code </label>
					<input type='text' name='subject_code' class="form-control">                    
                </div>
				<div class="form-group">
                    <label>Select faculty Type:</label>
					<select name="faculty_type" class="form-control">
                        <option value="MAIN">MAIN</option>
						<option value="SECOND">SECOND</option>						
					</select>
				</div>
				
				
                <button type="submit" name="submit_update_data" class="btn btn-danger"
					onclick="return confirm('⚠️ Are you sure you want to update the selected data? This action cannot be undone.');">
				   
				</button>
            </form>
        </div>
    </div>
	-->
	
	
	

</body>
</html>
