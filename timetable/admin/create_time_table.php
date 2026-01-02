<?php
session_start();
include("../html/dbconn.php");
$ay = "2024-25";
$odd_even = 0;
set_time_limit(300); // Optional, increase script time if needed
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
        table { width: 100%; margin-top: 20px; }
        table td, table th { padding: 8px; text-align: center; }
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
                    <select name='regulation' class="form-control select2 select2-purple">
                        <option value="BT23">BT23</option>
                        <option value="UG20">UG20</option>
                    </select>
                </div>

                <div class="form-group col-sm-3">
                    <label>Branch and Section</label>
                    <select name='sec_data' class="form-control select2 select2-purple">
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
                    <select name='ay' class="form-control select2 select2-purple">
                        <option value='2024-25'>2024-25</option>
                    </select>
                </div>

                <div class="form-group col-sm-3 d-flex align-items-end">
                    <input type='submit' class="btn btn-primary btn-block" name='sec_ok' value="Generate Timetable">
                </div>
            </div>
        </form>
    </div>
</div>

<?php
if (isset($_POST['sec_ok'])) {
    $day_names = ["", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    $ay = $_POST['ay'];
    $regulation = $_POST['regulation'];
    $sec_arr = explode(":", $_POST['sec_data']);
    $sec_id = $sec_arr[0];
    $sec_name = $sec_arr[1];
    $dept_id = $sec_arr[2];
    $sem = $sec_arr[3];

    // Step 1: Fetch subject-staff mapping
    $ttq = "SELECT t1.subject_code, t1.staff_idno, t1.teaching_periods,
                   t2.staff_name, t3.subject_name
            FROM subject_staff_mapping t1
            LEFT JOIN staff t2 ON t1.staff_idno = t2.staff_idno
            LEFT JOIN subjects t3 ON t3.subject_code = t1.subject_code
            WHERE t1.ay = '$ay' AND t1.sec_id = $sec_id
            ORDER BY t1.subject_code";
    $result = $dbconn->query($ttq);

    $subjects = [];
    while ($row = $result->fetch_assoc()) {
        $subjects[] = [
            'code' => $row['subject_code'],
            'staff_id' => $row['staff_idno'],
            'periods_left' => (int)$row['teaching_periods'],
            'sub_name' => $row['subject_name'],
            'staff_name' => $row['staff_name'],
            'short_code' => strtoupper(substr($row['subject_name'], 0, 5))
        ];
    }

    // Check if teaching periods exceed available slots
    $total_periods = array_sum(array_column($subjects, 'periods_left'));
    if ($total_periods > 36) {
        echo "<div class='alert alert-danger'>Total periods required ($total_periods) exceed 36 available slots. Please adjust teaching periods.</div>";
        return;
    }

    // Step 2: Initialize timetable grid (6x6)
    $timetable = array_fill(1, 6, []);
    foreach ($timetable as $day => &$hours) {
        $hours = array_fill(1, 6, null);
    }
    unset($hours);

    // Step 3: Constraint - no same staff on same day
    function isValid($timetable, $day, $hour, $subject) {
        foreach ($timetable[$day] as $slot) {
            if ($slot && $slot['staff_id'] === $subject['staff_id']) return false;
        }
        return true;
    }

    // Step 4: Recursive Backtracking
    function fillTimetable(&$timetable, &$subjects, $day = 1, $hour = 1) {
        if ($day > 6) return true;
        $nextDay = ($hour == 6) ? $day + 1 : $day;
        $nextHour = ($hour == 6) ? 1 : $hour + 1;

        foreach ($subjects as $i => &$subject) {
            if ($subject['periods_left'] <= 0) continue;
            if (!isValid($timetable, $day, $hour, $subject)) continue;

            $timetable[$day][$hour] = [
                'sub_code' => $subject['code'],
                'staff_id' => $subject['staff_id'],
                'short_code' => $subject['short_code'],
                'staff_name' => $subject['staff_name']
            ];
            $subject['periods_left']--;

            if (fillTimetable($timetable, $subjects, $nextDay, $nextHour)) return true;

            // Backtrack
            $timetable[$day][$hour] = null;
            $subject['periods_left']++;
        }

        return false;
    }

    // Step 5: Generate Timetable
    if (!fillTimetable($timetable, $subjects)) {
        echo "<div class='alert alert-warning'>Unable to generate a valid timetable with current constraints.</div>";
        return;
    }

    // Step 6: Display Timetable
    echo "<h4 class='mt-4'>Generated Timetable for <strong>$sec_name</strong></h4>";
    echo "<table class='table table-bordered'>";
    echo "<thead class='thead-dark'><tr><th>Day / Hour</th>";
    for ($hour = 1; $hour <= 6; $hour++) echo "<th>Hour $hour</th>";
    echo "</tr></thead><tbody>";

    for ($day = 1; $day <= 6; $day++) {
        echo "<tr><th>{$day_names[$day]}</th>";
        for ($hour = 1; $hour <= 6; $hour++) {
            $slot = $timetable[$day][$hour];
            if ($slot) {
                echo "<td><strong>{$slot['short_code']}</strong><br><small>{$slot['staff_name']}</small></td>";
            } else {
                echo "<td>--</td>";
            }
        }
        echo "</tr>";
    }
    echo "</tbody></table>";
}
?>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="plugins/select2/js/select2.full.min.js"></script>
<script>
    $(function () {
        $('.select2').select2();
    });
</script>
</body>
</html>