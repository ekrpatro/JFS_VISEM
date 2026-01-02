<?php
session_start();
include("../html/dbconn.php");
$ay = "2024-25";
$odd_even = 0;
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
        .a-type {
            background-color: lightgreen !important;
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
                        $secq = "SELECT * FROM sec WHERE possision='active' ORDER BY course_id, dept_id, sem, section";
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
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="plugins/select2/js/select2.full.min.js"></script>
<script>
    $(document).ready(function () {
        $('.select2').select2({ theme: 'bootstrap4' });
    });
</script>
</body>
</html>

<?php
if (isset($_POST['sec_ok'])) 
{
    $ay = $_POST['ay'];
    $regulation = $_POST['regulation'];
    $sec_arr = explode(":", $_POST['sec_data']);
    $sec_id = $sec_arr[0];
    $sec_name = $sec_arr[1];
    $dept_id = $sec_arr[2];
    $sem = $sec_arr[3];
	if($sem %2 ==0)
	{
		$sem_type="EVEN";
	}
	else
	{
		$sem_type="ODD";
	}

    $days = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
    $timetable = array_fill(0, 6, array_fill(0, 6, ""));

    $ttq = "SELECT t1.`id`, t1.`ay`, t1.`sec_id`, t1.`subject_code`, t1.`staff_idno`, t1.`teaching_periods`, t2.staff_name, t3.subject_name, t3.sub_type 
            FROM `subject_staff_mapping` t1 
            LEFT JOIN staff t2 ON t1.staff_idno = t2.staff_idno 
            LEFT JOIN subjects t3 ON t3.subject_code = t1.subject_code 
            WHERE t1.ay = ? AND t1.sec_id = ? 
            ORDER BY t1.sec_id ASC;";
    $stmt = $dbconn->prepare($ttq);
    $stmt->bind_param("ss", $ay, $sec_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $theoryCourses = [];
    $labCourses = [];
    $facultyTheoryMap = [];

    while ($row = $result->fetch_assoc()) 
	{
        $type = strtoupper($row['sub_type']);
        if ($type === 'T' || $type === 'A') 
		{
            $theoryCourses[] = $row['subject_code'];
            $facultyTheoryMap[$row['subject_code']] = [
                'staff' => $row['staff_idno'],
                'type' => $type
            ];
        } elseif ($type === 'L') {
            $labCourses[] = ['code' => $row['subject_code'], 'staff' => $row['staff_idno']];
        }
    }
    $stmt->close();

    function getRandomEmptySlot(&$timetable, $length = 1, $startPeriodOptions = null) {
        $candidates = [];
        for ($d = 0; $d < 6; $d++) {
            for ($p = 0; $p <= 6 - $length; $p++) {
                if ($startPeriodOptions && !in_array($p, $startPeriodOptions)) continue;
                $valid = true;
                for ($i = 0; $i < $length; $i++) {
                    if ($timetable[$d][$p + $i] !== "") {
                        $valid = false;
                        break;
                    }
                }
                if ($valid) $candidates[] = [$d, $p];
            }
        }
        return count($candidates) > 0 ? $candidates[array_rand($candidates)] : null;
    }

    $usedLabDays = [];
    foreach ($labCourses as $lab) {
        while (true) {
            $slot = getRandomEmptySlot($timetable, 3, [0, 3]);
            if ($slot) {
                [$day, $start] = $slot;
                if (in_array($day, $usedLabDays)) continue;
                for ($i = 0; $i < 3; $i++) {
                    $timetable[$day][$start + $i] = ['code' => $lab['code'], 'staff' => $lab['staff'], 'type' => 'L'];
                }
                $usedLabDays[] = $day;
                break;
            }
        }
    }

    foreach (["Sports", "Library"] as $special) 
	{
        while (true) 
		{
            $day = rand(0, 5);
            if ($timetable[$day][5] == "") 
			{
				$timetable[$day][5] = ['code' => $special, 'staff' => '', 'type' => 'S'];
                break;
            }
        }
    }

    foreach ($theoryCourses as $course) 
	{
        $remaining = 5;
        while ($remaining > 0) 
		{
            $slot = getRandomEmptySlot($timetable);
            if ($slot) 
			{
                [$day, $period] = $slot;
                $timetable[$day][$period] = [
                    'code' => $course,
                    'staff' => $facultyTheoryMap[$course]['staff'],
                    'type' => $facultyTheoryMap[$course]['type']
                ];
                $remaining--;
            } else break;
        }
    }

    echo "<h3>Timetable <strong>$sec_name</strong></h3>";
    echo "<table border='1' cellpadding='8'>";
    echo "<tr><th>Day / Period</th>";
    for ($p = 1; $p <= 6; $p++) 
		echo "<th>P$p</th>";
    echo "</tr>";

    for ($d = 0; $d < 6; $d++) 
	{
        echo "<tr><td>{$days[$d]}</td>";
        for ($p = 0; $p < 6; $p++) 
		{
            $entry = $timetable[$d][$p];
            if (is_array($entry)) 
			{
                $class = '';
                $type = strtoupper($entry['type']);
                if ($type === 'L') 
					$class = 'lab';
                elseif ($type === 'A') 
					$class = 'a-type';

                $subject = htmlspecialchars(str_replace('(Lab)', '', $entry['code']));
                $staff = htmlspecialchars($entry['staff']);
                echo "<td class='$class'>{$subject}<br>{$staff}</td>";
            } 
			else 
			{
                echo "<td>-</td>";
            }
        }
        echo "</tr>";
    }
    echo "</table><br>";
	// Mapped faculty name
	$ttq = "SELECT t1.`id`, t1.`ay`, t1.`sec_id`, t1.`subject_code`, t1.`staff_idno`, t1.`teaching_periods`, t2.staff_name, t3.subject_name, t3.sub_type 
            FROM `subject_staff_mapping` t1 
            LEFT JOIN staff t2 ON t1.staff_idno = t2.staff_idno 
            LEFT JOIN subjects t3 ON t3.subject_code = t1.subject_code 
            WHERE t1.ay = ? AND t1.sec_id = ? ORDER BY t1.sec_id,t3.sub_type  ASC;";
    $stmt = $dbconn->prepare($ttq);
    $stmt->bind_param("ss", $ay, $sec_id);
    $stmt->execute();
    $result = $stmt->get_result();
	$sno=1;
	echo "<table border='1' cellpadding='8'>";
    echo "<tr><th>Staff_idno</th> <th>Staff Name</th> <th>Course Code</th> <th>Course Name</th></tr>";
	 while ($row = $result->fetch_assoc()) 
	 {
		 if($row['sub_type']=='T')
			echo "<tr style='color:#0000FF'> <td> ".$sno++."</td>";
		else
			echo "<tr > <td> ".$sno++."</td>";
       echo " <td> ".$row['staff_idno']."</td>";
	   echo " <td> ".$row['staff_name']."</td>";
	   echo " <td> ".$row['subject_code']."</td>";
	   echo " <td> ".$row['subject_name']."</td> </tr>";
       
    }
	echo "</table>";
	echo "<form method='POST'>";
		echo "<input type='hidden' name='locked_timetable' value='" . base64_encode(serialize($timetable)) . "'>";
		echo "<input type='hidden' name='sec_id' value='$sec_id'>";
		echo "<input type='hidden' name='sem' value='$sem'>";
		echo "<input type='hidden' name='ay' value='$ay'>";
		echo "<input type='hidden' name='sem_type' value='$sem_type'>";
		echo "<button type='submit' class='btn btn-danger' name='lock_btn'>Lock Timetable</button>";
	echo "</form>";
    echo "<p><strong>Legend:</strong> <span style='background-color:pink;'>Lab</span> | <span style='background-color:lightgreen;'>Audit (A)</span></p>";
}
if (isset($_POST['lock_btn'])) 
{
    $locked_timetable = unserialize(base64_decode($_POST['locked_timetable']));
    $sec_id = $_POST['sec_id'];
    $sem = $_POST['sem'];
    $ay = $_POST['ay'];
    $sem_type = $_POST['sem_type'];

    $ct_no = 1; // example, use proper controller ID if needed

    for ($day_no = 0; $day_no < count($locked_timetable); $day_no++) 
	{
        for ($p = 0; $p < count($locked_timetable[$day_no]); $p++) 
		{
            $entry = $locked_timetable[$day_no][$p];
            if (is_array($entry)) 
			{
                $st_hour = $p + 1;
                $end_hour = $p + 1;
                $duration = 1;

                // Adjust duration for lab periods (3 continuous)
                if ($entry['type'] === 'L') {
                    $duration = 3;
                    $end_hour = $st_hour + 2;
                }

                $subject_code = $entry['code'];
                $staff_idno = $entry['staff'];
                $room_no = ''; // optionally collect this
                $locked = 1;

                // Prevent duplicate insert for multi-period entries (like labs)
                $alreadyInserted = false;
                for ($i = $p - 1; $i >= $p - 2 && $i >= 0; $i--) 
				{
                    if (isset($locked_timetable[$day_no][$i]) && $locked_timetable[$day_no][$i] == $entry)
					{
                        $alreadyInserted = true;
                        break;
                    }
                }
                if ($alreadyInserted) 
					continue;
				$locked=1;
                $stmt = $dbconn->prepare("INSERT INTO timetable ( staff_idno, day_no, sec_id, sem, st_hour, end_hour, subject_code, room_no, duration, ay, locked, sem_type)
                                          VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sisiiississi",$staff_idno, $day_no, $sec_id, $sem, $st_hour, $end_hour, $subject_code, $room_no, $duration, $ay, $locked, $sem_type);
                $stmt->execute();
            }
        }
    }

    echo "<script>alert('Timetable successfully locked and saved.');</script>";
}

?>
