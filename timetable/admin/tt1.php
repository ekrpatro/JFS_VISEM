<?php
session_start();
include("../html/dbconn.php");
$ay = "2024-25";
$odd_even = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['sec_ok'])) {
    $sec_data = explode(":", $_POST['sec_data']);
    $sec_id = $sec_data[0];

    $occupied = [];
    $subject_days = [];

    // Get predefined timetable
    $predefined_q = "SELECT day_no, st_period, end_period, subject_code FROM predefined_timetable WHERE sec_id='$sec_id' AND ay='$ay'";
    $predefined_result = $dbconn->query($predefined_q);
    foreach ($predefined_result as $row) {
        for ($p = $row['st_period']; $p <= $row['end_period']; $p++) {
            $occupied[$row['day_no']][$p] = true;
            $subject_days[$row['subject_code']][$row['day_no']] = true;
        }
    }

    // Get subject mappings
    $subjects_q = "SELECT subject_code, staff_idno, teaching_periods FROM subject_staff_mapping WHERE sec_id='$sec_id' AND ay='$ay'";
    $subjects_result = $dbconn->query($subjects_q);

    foreach ($subjects_result as $subject) 
	{
        $code = $subject['subject_code'];
		echo "<br> subject : ".$code;
        $staff = $subject['staff_idno'];
        $remaining = $subject['teaching_periods'];

        for ($day = 1; $day <= 6 && $remaining > 0; $day++) 
		{
            if (!isset($subject_days[$code][$day])) 
			{
                for ($period = 1; $period <= 6 && $remaining > 0; $period++) 
				{
                    if (!isset($occupied[$day][$period])) 
					{
                        // Check for staff conflict
                        $conflict_q = "SELECT COUNT(*) FROM predefined_timetable WHERE day_no=? AND st_period=? AND staff_idno=? AND ay=?";
                        $stmt = $dbconn->prepare($conflict_q);
                        $stmt->bind_param("iiis", $day, $period, $staff, $ay);
                        $stmt->execute();
                        $stmt->bind_result($count);
                        $stmt->fetch();
                        $stmt->close();

                        if ($count == 0) 
						{
                            $insert_q = "INSERT INTO predefined_timetable (ay, sec_id, subject_code, day_no, st_period, end_period, staff_idno, room_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                            $stmt = $dbconn->prepare($insert_q);
                            $room = 'TBD';
                            $stmt->bind_param("sisiiiss", $ay, $sec_id, $code, $day, $period, $period, $staff, $room);
                            $stmt->execute();
                            $stmt->close();
							echo "<br> Inserted ";
                            $occupied[$day][$period] = true;
                            $subject_days[$code][$day] = true;
                            $remaining--;
                            break;
                        }
                    }
                }
            }
        }
    }

    echo "<div class='alert alert-success mt-3'>Timetable slots successfully allocated.</div>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Time Table</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <style>
        table {
            width: 100%;
            margin-top: 20px;
        }
        table td, table th {
            padding: 8px;
            text-align: center;
        }
        input[type="text"] {
            width: 100%;
        }
        .lab {
            background-color: pink;
        }
        .not-allocated {
            background-color: #ffffcc;
        }
        .left-align {
            text-align: left !important;
            padding-left: 5px;
        }
    </style>
</head>
<body class="container mt-4">
<div class="card border-success">
    <div class="card-header bg-success text-white">
        <h3 class="card-title">TIME TABLE</h3>
    </div>
    <div class="card-body">
        <form name='time_frm' method="POST" action="">
            <div class="row">
                <div class="form-group col-sm-3">
                    <label>Select Regulation</label>
                    <select name='regulation' class="form-control select2 select2-purple" data-dropdown-css-class="select2-purple">
                        <option value="BT23">BT23</option>
                        <option value="UG20">UG20</option>
                    </select>
                </div>
                <div class="form-group col-sm-3">
                    <label>Branch and Section</label>
                    <select name='sec_data' class="form-control select2 select2-purple" data-dropdown-css-class="select2-purple">
                        <?php
                        $secq = "SELECT * FROM sec WHERE possision='active' and dept_id=6 ORDER BY course_id, dept_id, sem, section";
                        $sec_rows = $dbconn->query($secq);
                        foreach ($sec_rows as $sec_row) {
                            echo "<option value='" . $sec_row['sec_id'] . ":" . $sec_row['sec_name'] . ":" . $sec_row['dept_id'] . ":" . $sec_row['sem'] . "'>" . $sec_row['sec_name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group col-sm-3">
                    <label>Academic Year</label>
                    <select name='ay' class="form-control select2 select2-purple" data-dropdown-css-class="select2-purple">
                        <option value='2024-25'>2024-25</option>
                    </select>
                </div>
                <div class="form-group col-sm-3 d-flex align-items-end">
                    <input type='submit' class="btn btn-primary btn-block" name='sec_ok' value="OK">
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>
