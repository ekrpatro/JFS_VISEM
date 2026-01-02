<?php
session_start();
include("../html/dbconn.php");
$ay = "2024-25";
$odd_even = 0;
/*
CREATE TABLE subject_staff_mapping AS
SELECT 
    t1.`ay`,  
    t1.`sec_id`, 
    t3.`subject_code`, 
    t2.`staff_idno` 
FROM 
    `subject_mapping` t1 
LEFT JOIN  
    staff t2 ON t1.staff_id = t2.staff_id 
LEFT JOIN 
    subjects t3 ON t3.subject_id = t1.subject_id 
WHERE 
    t1.ay = '2024-25';

*/
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Time Table</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <!-- Select2 CSS -->
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
                        $secq = "SELECT * FROM sec where possision='active' ORDER BY course_id, dept_id, sem, section";
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


<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- Select2 JS -->
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

//generated...
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
	
    // Fetch course details for this section
	$ttq = "SELECT t1.`id`, t1.`ay`, t1.`sec_id`, t1.`subject_code`, t1.`staff_idno`, t1.`teaching_periods`, t2.staff_name,t3.subject_name,t3.sub_type FROM `subject_staff_mapping` t1 left join staff t2 on t1.staff_idno=t2.staff_idno left join subjects t3 on t3.subject_code=t1.subject_code where t1.ay=?  and t1.sec_id=?	ORDER BY `t1`.`sec_id` ASC;";
    $stmt = $conn->prepare($ttq);
    $stmt->bind_param("ss",$ay,$sec_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $theoryCourses = [];
    $labCourses = [];
    $facultyTheoryMap = [];

    while ($row = $result->fetch_assoc()) {
        if ($row['sub_type'] === 'T') {
            $theoryCourses[] = $row['subject_code'];
            $facultyTheoryMap[$row['subject_code']] = $row['staff_idno'];
        } elseif ($row['sub_type'] === 'L') {
            $labCourses[] = $row['subject_code'];
        }
    }

    $stmt->close();

    // Helper function
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

    // Allocate lab courses
    foreach ($labCourses as $lab) {
        while (true) {
            $slot = getRandomEmptySlot($timetable, 3, [0, 3]);
            if ($slot) {
                [$day, $start] = $slot;
                for ($i = 0; $i < 3; $i++) {
                    $timetable[$day][$start + $i] = $lab . "(Lab)";
                }
                break;
            }
        }
    }

    // Add Sports and Library to 6th period
    foreach (["Sports", "Library"] as $special) {
        while (true) {
            $day = rand(0, 5);
            if ($timetable[$day][5] == "") {
                $timetable[$day][5] = $special;
                break;
            }
        }
    }

    // Allocate 5 periods/week per theory course
    $theorySchedule = array_fill_keys($theoryCourses, 5);
    foreach ($theorySchedule as $course => $remaining) {
        while ($remaining > 0) {
            $slot = getRandomEmptySlot($timetable);
            if ($slot) {
                [$day, $period] = $slot;
                $timetable[$day][$period] = $course;
                $remaining--;
            } else break;
        }
    }

    // Ensure 2 faculty get period 1 or 4
    $facultyAdded = [];
    foreach ($facultyTheoryMap as $course => $faculty) {
        if (count($facultyAdded) >= 2) break;
        $slot = getRandomEmptySlot($timetable, 1, [0, 3]);
        if ($slot) {
            [$day, $period] = $slot;
            $timetable[$day][$period] = $course . "($faculty)";
            $facultyAdded[] = $faculty;
        }
    }

    // Display Timetable
    echo "<h3>Generated Timetable for Section: <strong>$sec_name</strong></h3>";
    echo "<table border='1' cellpadding='8'>";
    echo "<tr><th>Day / Period</th>";
    for ($p = 1; $p <= 6; $p++) 
		echo "<th>P$p</th>";
    echo "</tr>";

    for ($d = 0; $d < 6; $d++) {
        echo "<tr><td>{$days[$d]}</td>";
        for ($p = 0; $p < 6; $p++) {
            echo "<td>" . ($timetable[$d][$p] ?: "-") . "</td>";
        }
        echo "</tr>";
    }
    echo "</table><br>";
}
?>


