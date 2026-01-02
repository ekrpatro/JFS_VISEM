
<?php
/*
SELECT `id`, `ay`, `sec_id`, `subject_code`, `staff_idno`, `teaching_periods` FROM `subject_staff_mapping` WHERE 1

want to allocate each theory subjects given in the subject_staff_mapping  exactly as per its teaching_periods using proper backtracking so that:

Each theory subject is scheduled for the exact number of periods needed.

No subject appears more than once per day.

Faculty availability is checked.

Backtracking handles conflicts and finds a valid schedule if possible.
INSERT INTO subject_staff_mapping (ay, sec_id, subject_code, staff_idno, teaching_periods) SELECT distinct ay, sec_id, subject_code, LEFT(staff_idno, 9) AS staff_idno, SUM(duration) AS teaching_periods FROM class_timetable WHERE sec_id IN (SELECT sec_id FROM sec WHERE dept_id = 6) AND sec_id = 161 AND ay = '2024-25' GROUP BY ay, sec_id, subject_code, staff_idno;
*/
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
		.not-allocated {
    background-color: #ffffcc; /* light yellow */
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
</html><?php

if (isset($_POST['sec_ok'])) 
{    
    $ay = $_POST['ay'];
    $regulation = $_POST['regulation'];
    $sec_arr = explode(":", $_POST['sec_data']);
    $sec_id = $sec_arr[0];
    $sec_name = $sec_arr[1];
    $dept_id = $sec_arr[2];
    $sem = $sec_arr[3];
    $sem_type = ($sem % 2 == 1) ? "ODD" : "EVEN";
    $days = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

    // Check if timetable exists
    $checkTT = "SELECT * FROM timetable WHERE ay = ? AND sec_id = ?";
    $stmt = $dbconn->prepare($checkTT);
    $stmt->bind_param("si", $ay, $sec_id);
    $stmt->execute();
    $existingTT = $stmt->get_result();

    if ($existingTT->num_rows > 0) 
    {
        // Display existing timetable
        $no_of_days = 6;
        $no_of_periods = 6;
        $timetable = array_fill(0, $no_of_days, array_fill(0, $no_of_periods, ""));
        while ($row = $existingTT->fetch_assoc())
        {
            $day = $row['day_no'] - 1; // 0 to 5
            $st_hour = $row['st_hour'] - 1; // 0 to 5
            $end_hour = $row['end_hour'] - 1; // 0 to 5
            $sub_type = ($end_hour > $st_hour) ? 'L' : 'T';
            for ($i = $st_hour; $i <= $end_hour; $i++)
            {
                $timetable[$day][$i] = [
                    'code' => $row['subject_code'],
                    'staff' => $row['staff_idno'],
                    'type' => $sub_type
                ];
            }
        }
        $stmt->close();

        echo "<h3>Existing Timetable for <strong>$sec_name</strong></h3>";
        echo "<table border='1' cellpadding='8'>";
        echo "<tr><th>Day / Period</th>";
        for ($p = 1; $p <= 6; $p++) echo "<th>P$p</th>";
        echo "</tr>";
        for ($d = 0; $d < 6; $d++) 
        {
            echo "<tr><td>{$days[$d]}</td>";
            for ($p = 0; $p < 6; $p++) 
            {
                $entry = $timetable[$d][$p];
                if (is_array($entry)) 
                {
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
        $display_from_db = 1;
    } 
    else 
    {
        // Generate new timetable
        $timetable = array_fill(0, 6, array_fill(0, 6, ""));

        // 1. Load allocated lab timetable
        $lab_tt_query = "SELECT subject_code, day_no, st_period, end_period, staff_idno FROM predefined_timetable WHERE ay = ? AND sec_id = ?";
        $stmt = $dbconn->prepare($lab_tt_query);
        $stmt->bind_param("si", $ay, $sec_id);
        $stmt->execute();
        $res = $stmt->get_result();

        while ($row = $res->fetch_assoc()) 
        {
            $day = (int)$row['day_no'] - 1;
            $start = (int)$row['st_period'] - 1;
            $end = (int)$row['end_period'] - 1;
            $subject = $row['subject_code'];
            $staff = $row['staff_idno'];
            for ($p = $start; $p <= $end; $p++) 
            {
                $timetable[$day][$p] = ['code' => $subject, 'staff' => $staff, 'type' => 'L'];
            }
        }
        $stmt->close();

        // 2. Fetch Theory Courses
        $query = "SELECT t1.`id`, t1.`ay`, t1.`sec_id`, t1.`subject_code`, t1.`staff_idno`, t1.`teaching_periods`, t2.staff_name, t3.subject_name, t3.sub_type 
                  FROM `subject_staff_mapping` t1 
                  LEFT JOIN staff t2 ON t1.staff_idno = t2.staff_idno 
                  LEFT JOIN subjects t3 ON t3.subject_code = t1.subject_code 
                  WHERE t1.ay = ? AND t1.sec_id = ? AND t3.sub_type IN ('T','A') ORDER BY t1.sec_id ASC";

        $stmt = $dbconn->prepare($query);
        $stmt->bind_param("si", $ay, $sec_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $theoryCourses = [];
        while ($row = $result->fetch_assoc()) {
            $theoryCourses[] = $row;
        }
        $stmt->close();

        // Faculty availability check function - fix parameter order and query logic
        function isFacultyAvailable($dbconn, $staffId, $dayNo, $period, $ay, $sem_type) 
        {
            $check = "SELECT * FROM timetable 
                      WHERE staff_idno = ? 
                        AND day_no = ? 
                        AND ? BETWEEN st_hour AND end_hour 
                        AND ay = ? 
                        AND sem_type = ?";
            $stmt = $dbconn->prepare($check);
            $stmt->bind_param("siiss", $staffId, $dayNo, $period, $ay, $sem_type);
            $stmt->execute();
            $stmt->store_result();
            $isFree = $stmt->num_rows === 0;
            $stmt->close();
            return $isFree;
        }
		// Check if subject already exists on the day
		function isSubjectUsedOnDay($timetable, $subject, $day) 
		{
			foreach ($timetable[$day] as $cell) 
			{
				if (is_array($cell) && $cell['code'] === $subject) {
					return true;
				}
			}
			return false;
		}
		
		
		
     // 3. Allocate theory periods with safety for infinite loops
        foreach ($theoryCourses as $course) 
        {
            $subject = $course['subject_code'];
            $staff = $course['staff_idno'];
            $requiredPeriods = (int)$course['teaching_periods'];
            $allocated = 0;
            $daysUsed = [];
            $attempts = 0; // To prevent infinite loop
          while ($allocated < $requiredPeriods && $attempts < 1000)  {
                $attempts++;

                $day = rand(0, 5);
                if (in_array($day, $daysUsed)) 
					continue;

                // Find free periods where faculty is available
                $freePeriods = [];
                for ($p = 0; $p < 6; $p++) 
                {
                    if ($timetable[$day][$p] === "" && isFacultyAvailable($dbconn, $staff, $day + 1, $p + 1, $ay, $sem_type)) 
                    {
                        $freePeriods[] = $p;
                    }
                }
                if (count($freePeriods) > 0) 
                {
                    $chosenPeriod = $freePeriods[array_rand($freePeriods)];
                    $timetable[$day][$chosenPeriod] = [
                        'code' => $subject,
                        'staff' => $staff,
                        'type' => 'T'
                    ];
                    $allocated++;
                    $daysUsed[] = $day;
                }
            }
            if ($allocated < $requiredPeriods) 
            {				
                echo "<div class='alert alert-warning'>Could not allocate all periods for subject $subject</div>";
            }
			
        }
		

        // Display generated timetable
        echo "<h3>Timetable for <strong>$sec_name</strong></h3>";
        echo "<table border='1' cellpadding='8'>";
        echo "<tr><th>Day / Period</th>";
        for ($p = 1; $p <= 6; $p++) echo "<th>P$p</th>";
        echo "</tr>";

        for ($d = 0; $d < 6; $d++) 
        {
            echo "<tr><td>{$days[$d]}</td>";
            for ($p = 0; $p < 6; $p++) 
            {
                $entry = $timetable[$d][$p];
                if (is_array($entry)) 
                {
                    $class = $entry['type'] === 'L' ? 'lab' : '';
                    $subject = htmlspecialchars(str_replace('(Lab)', '', $entry['code']));
                    $staff = htmlspecialchars($entry['staff']);
                    echo "<td class='$class'>{$subject}<br>{$staff}</td>";
                } 
                else 
                {
                     echo "<td class='not-allocated'>-</td>";
                }
            }
            echo "</tr>";
        }
        echo "</table><br>";
		
		// staff mapping details starts
		$ttq = "SELECT t1.`id`, t1.`ay`, t1.`sec_id`, t1.`subject_code`, t1.`staff_idno`, t1.`teaching_periods`, t2.staff_name, t3.subject_name, t3.sub_type 
				FROM `subject_staff_mapping` t1 
				LEFT JOIN staff t2 ON t1.staff_idno = t2.staff_idno 
				LEFT JOIN subjects t3 ON t3.subject_code = t1.subject_code 
				WHERE t1.ay = ? AND t1.sec_id = ? ORDER BY t1.sec_id,t3.sub_type  ASC;";
		$stmt = $dbconn->prepare($ttq);
		$stmt->bind_param("si", $ay, $sec_id);
		$stmt->execute();
		$result = $stmt->get_result();
		
		//  teacher and subject details including teaching periods
		echo '<table class="table table-bordered table-striped">';
		echo '<thead class="table-dark">';
		echo '<tr>';
		echo '<th>S.No.</th>';
		echo '<th>Staff ID</th>';
		echo '<th>Staff Name</th>';
		echo '<th>Subject Code</th>';
		echo '<th>Subject Name</th>';
		echo '<th>Teaching Periods</th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';

		$sno = 1;
		while ($row = $result->fetch_assoc()) {
			echo '<tr>';
			echo '<td>' . $sno++ . '</td>';
			echo '<td>' . htmlspecialchars($row['staff_idno']) . '</td>';
			echo '<td class="left-align">' . htmlspecialchars($row['staff_name']) . '</td>';
			echo '<td>' . htmlspecialchars($row['subject_code']) . '</td>';
			echo '<td class="left-align">' . htmlspecialchars($row['subject_name']) . '</td>';
			echo '<td>' . htmlspecialchars($row['teaching_periods']) . '</td>';
			echo '</tr>';
		}

		echo '</tbody>';
		echo '</table>';

		//ends

        // Store timetable in session for saving on lock
        $_SESSION['timetable'] = $timetable;
        $_SESSION['sec_id'] = $sec_id;
        $_SESSION['ay'] = $ay;
        $_SESSION['sem_type'] = $sem_type;
    }
}

if (isset($_POST['lock_btn'])) 
{
    if (!isset($_SESSION['timetable'])) {
        echo "<div class='alert alert-danger'>No timetable available to save.</div>";
    } else {
        $timetable = $_SESSION['timetable'];
        $sec_id = $_SESSION['sec_id'];
        $ay = $_SESSION['ay'];
        $sem_type = $_SESSION['sem_type'];

        // Clear old timetable entries for this section and academic year
        $delQuery = "DELETE FROM timetable WHERE ay = ? AND sec_id = ?";
        $stmt = $dbconn->prepare($delQuery);
        $stmt->bind_param("si", $ay, $sec_id);
        $stmt->execute();
        $stmt->close();

        // Insert new timetable
        $insertQuery = "INSERT INTO timetable (ay, sec_id, day_no, st_hour, end_hour, subject_code, staff_idno, sem_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $dbconn->prepare($insertQuery);

        foreach ($timetable as $day => $periods) 
        {
            $p = 0;
            while ($p < 6) 
            {
                if ($periods[$p] === "") 
                {
                    $p++;
                    continue;
                }

                $subject = $periods[$p]['code'];
                $staff = $periods[$p]['staff'];
                $type = $periods[$p]['type'];
                $start = $p + 1;
                $end = $start;

                // For labs, merge consecutive periods
                if ($type === 'L') 
                {
                    while ($end < 6 && isset($periods[$end]) && is_array($periods[$end]) && $periods[$end]['code'] === $subject && $periods[$end]['type'] === 'L') 
                    {
                        $end++;
                    }
                    $end--; // last lab period
                }

                $stmt->bind_param("siisssss", $ay, $sec_id, $day + 1, $start, $end, $subject, $staff, $sem_type);
                $stmt->execute();

                $p = $end + 1;
            }
        }

        $stmt->close();
        echo "<div class='alert alert-success'>Timetable locked and saved successfully.</div>";

        // Clear session timetable to avoid duplicate save
        unset($_SESSION['timetable']);
    }
}
?>