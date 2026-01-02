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
                        $secq = "SELECT * FROM sec WHERE possision='active' and dept_id=5 ORDER BY course_id, dept_id, sem, section";
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
        $('.select2').select2({
            theme: 'bootstrap4'
        });
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

    $days = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
    $timetable = array_fill(0, 6, array_fill(0, 6, ""));

    $ttq = "SELECT t1.`id`, t1.`ay`, t1.`sec_id`, t1.`subject_code`, t1.`staff_idno`, t1.`teaching_periods`, t2.staff_name, t3.subject_name, t3.sub_type 
            FROM `subject_staff_mapping` t1 
            LEFT JOIN staff t2 ON t1.staff_idno = t2.staff_idno 
            LEFT JOIN subjects t3 ON t3.subject_code = t1.subject_code 
            WHERE t1.ay = ? AND t1.sec_id = ? ORDER BY t1.sec_id ASC;";
    $stmt = $dbconn->prepare($ttq);
    $stmt->bind_param("ss", $ay, $sec_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $theoryCourses = [];
    $labCourses = [];
    $facultyTheoryMap = [];
    $subTypes = [];

    while ($row = $result->fetch_assoc()) {
        $subTypes[$row['subject_code']] = $row['sub_type'];
        if ($row['sub_type'] === 'T') {
            $theoryCourses[] = $row['subject_code'];
            $facultyTheoryMap[$row['subject_code']] = $row['staff_idno'];
        } elseif ($row['sub_type'] === 'L') {
            $labCourses[] = ['code' => $row['subject_code'], 'staff' => $row['staff_idno']];
        }
    }
    $stmt->close();

    function getRandomEmptySlot(&$timetable, $length = 1, $startPeriodOptions = null) 
	{
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
                if ($valid) {
                    $candidates[] = [$d, $p];
                }
            }
        }
        return count($candidates) > 0 ? $candidates[array_rand($candidates)] : null;
    }

    // Assign labs (3 continuous periods, once per week, avoid repeating days)
    $usedLabDays = [];
    foreach ($labCourses as $lab) 
	{
        while (true) 
		{
            $slot = getRandomEmptySlot($timetable, 3, [0, 3]);
            if ($slot) 
			{
                [$day, $start] = $slot;
                if (in_array($day, $usedLabDays)) continue;
                for ($i = 0; $i < 3; $i++) 
				{
                    $timetable[$day][$start + $i] = ['code' => $lab['code'], 'staff' => $lab['staff'], 'type' => 'L'];
                }
                $usedLabDays[] = $day;
                break;
            } 
			else 
			{
                break;
            }
        }
    }

    // Assign sports and library in 6th period
    foreach (["Sports", "Library"] as $special) 
	{
        while (true) 
		{
            $day = rand(0, 5);
            if ($timetable[$day][5] === "") 
			{
                $timetable[$day][5] = ['code' => $special, 'staff' => '', 'type' => 'S'];
                break;
            }
        }
    }

    // Assign theory subjects without repeating same subject on same day
    $theorySchedule = array_fill_keys($theoryCourses, 5);
    foreach ($theorySchedule as $course => $remaining) 
	{
        $attempts = 0;
        while ($remaining > 0 && $attempts < 100) 
		{
            $attempts++;

            $validDays = [];
            for ($d = 0; $d < 6; $d++) 
			{
                $alreadyAssignedToday = false;
                foreach ($timetable[$d] as $cell) 
				{
                    if (is_array($cell) && $cell['code'] === $course) 
					{
                        $alreadyAssignedToday = true;
                        break;
                    }
                }
                if (!$alreadyAssignedToday) 
				{
                    $validDays[] = $d;
                }
            }

            if (empty($validDays)) 
				break;

            $day = $validDays[array_rand($validDays)];

            $emptyPeriods = [];
            for ($p = 0; $p < 6; $p++) 
			{
                if ($timetable[$day][$p] === "") 
				{
                    $emptyPeriods[] = $p;
                }
            }

            if (!empty($emptyPeriods)) 
			{
                $period = $emptyPeriods[array_rand($emptyPeriods)];
                $timetable[$day][$period] = ['code' => $course, 'staff' => $facultyTheoryMap[$course], 'type' => 'T'];
                $remaining--;
            }
        }

        if ($remaining > 0) 
		{
            echo "<div class='alert alert-danger'>Could not assign all 5 periods for subject: <strong>$course</strong></div>";
        }
    }

    // Display Timetable
    echo "<h3>Timetable for <strong>$sec_name</strong></h3>";
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
            if (is_array($entry)) {
                $class = $entry['type'] === 'L' ? 'lab' : '';
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
			echo "<tr style='color:#00FF00'> <td> ".$sno++."</td>";
		else
			echo "<tr > <td> ".$sno++."</td>";
       echo " <td> ".$row['staff_idno']."</td>";
	   echo " <td> ".$row['staff_name']."</td>";
	   echo " <td> ".$row['subject_code']."</td>";
	   echo " <td> ".$row['subject_name']."</td> </tr>";
       
    }
	echo "</table>";
    $stmt->close();	
	
}
?>

