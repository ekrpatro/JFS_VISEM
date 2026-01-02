<?php
session_start();
include("../html/dbconn.php");

ini_set('max_execution_time', 300); // Increase script time

$odd_even = 0;
$days = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
$no_of_days = 6;
$no_of_periods = 6;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Time Table</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        table { width: 100%; margin-top: 20px; }
        table td, table th { padding: 8px; text-align: center; }
        input[type="text"] { width: 100%; }
        .lab { background-color: pink; }
        .not-allocated { background-color: #ffffcc; }
        .left-align { text-align: left !important; padding-left: 5px; }
    </style>
</head>
<body class="container mt-4">
<div class="card border-success">
    <div class="card-header bg-success text-white">
        <h3 class="card-title">TIME TABLE</h3>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="row">
                <div class="form-group col-sm-3">
                    <label>Select Regulation</label>
                    <select name='regulation' class="form-control select2" required>
                        <option value="BT23">BT23</option>
                        <option value="UG20">UG20</option>
                    </select>
                </div>
                <div class="form-group col-sm-3">
                    <label>Branch and Section</label>
                    <select name='sec_data' class="form-control select2" required>
                        <?php
                        $secq = "SELECT * FROM sec WHERE sem =7 and dept_id=4 ORDER BY course_id, dept_id, sem, section";
                        $sec_rows = $dbconn->query($secq);
                        foreach ($sec_rows as $sec_row) {
                            echo "<option value='{$sec_row['sec_id']}:{$sec_row['sec_name']}:{$sec_row['dept_id']}:{$sec_row['sem']}'>{$sec_row['sec_code']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group col-sm-3">
                    <label>Academic Year</label>
                    <select name='ay' class="form-control select2" required>
                        <option value='2025-26'>2025-26</option>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('.select2').select2({ tags: true, theme: 'bootstrap4', placeholder: "Select or type...", allowClear: true, width: '100%' });
    });
</script>
</body>
</html>
<?php
$staffAvailabilityCache = [];

function isAvailable($timetable, $day, $period) {
    return empty($timetable[$day][$period]);
}

function isStaffAvailable($dbconn, $staff, $day, $period, $ay, $sem_type) {
    global $staffAvailabilityCache;
    $key = "{$staff}_{$day}_{$period}_{$ay}_{$sem_type}";
    if (isset($staffAvailabilityCache[$key])) return $staffAvailabilityCache[$key];
    $stmt = $dbconn->prepare("SELECT 1 FROM timetable WHERE staff_idno = ? AND day_no = ? AND ? BETWEEN st_hour AND end_hour AND ay = ? AND sem_type = ?");
	$dno=$day+1;
	$pno=$period + 1;
    $stmt->bind_param("siiss", $staff, $dno, $pno, $ay, $sem_type);
    $stmt->execute();
    $stmt->store_result();
    $available = $stmt->num_rows === 0;
    $stmt->close();
    $staffAvailabilityCache[$key] = $available;
    return $available;
}

function isValidTheorySlotSet($combo) {
    $grouped = [];
    foreach ($combo as [$d, $p]) $grouped[$d][] = $p;
    foreach ($grouped as $periods) {
        sort($periods);
        for ($i = 1; $i < count($periods); $i++) {
            if ($periods[$i] === $periods[$i - 1] + 1) return false;
        }
    }
    return true;
}

function combinations($arr, $r, $limit = 1000) {
    shuffle($arr);
    $result = [];
    $n = count($arr);
    if ($r > $n) return $result;
    $indices = range(0, $r - 1);
    $count = 0;
    while (true) {
        $combo = [];
        foreach ($indices as $i) $combo[] = $arr[$i];
        $result[] = $combo;
        if (++$count >= $limit) break;
        $i = $r - 1;
        while ($i >= 0 && $indices[$i] === $i + $n - $r) $i--;
        if ($i < 0) break;
        $indices[$i]++;
        for ($j = $i + 1; $j < $r; $j++) $indices[$j] = $indices[$j - 1] + 1;
    }
    return $result;
}

function allocatePeriods(&$timetable, $subjects, $index, $dbconn, $ay, $sem_type) {
    global $no_of_days, $no_of_periods;
    if ($index === count($subjects)) return true;
    $subject = $subjects[$index];
    $code = $subject['subject_code'];
    $staff = $subject['staff_idno'];
    $required = (int)$subject['teaching_periods'];
    $slots = [];
    for ($d = 0; $d < $no_of_days; $d++) {
        for ($p = 0; $p < $no_of_periods; $p++) {
            if (isAvailable($timetable, $d, $p) && isStaffAvailable($dbconn, $staff, $d, $p, $ay, $sem_type)) {
                $slots[] = [$d, $p];
            }
        }
    }
    if (count($slots) < $required) return false;
    $combinations = combinations($slots, $required, 1000);
    foreach ($combinations as $combo) {
        if (!isValidTheorySlotSet($combo)) continue;
        foreach ($combo as [$d, $p]) {
            $timetable[$d][$p] = ['code' => $code, 'staff' => $staff, 'type' => 'T'];
        }
        if (allocatePeriods($timetable, $subjects, $index + 1, $dbconn, $ay, $sem_type)) return true;
        foreach ($combo as [$d, $p]) $timetable[$d][$p] = "";
    }
    return false;
}

if (isset($_POST['sec_ok'])) {
    $ay = $_POST['ay'];
    $regulation = $_POST['regulation'];
    list($sec_id, $sec_name, $dept_id, $sem) = explode(":", $_POST['sec_data']);
    $sem_type = ($sem % 2 == 1) ? "ODD" : "EVEN";
    $timetable = array_fill(0, $no_of_days, array_fill(0, $no_of_periods, ""));

    $stmt = $dbconn->prepare("SELECT subject_code, day_no, st_period, end_period, staff_idno FROM predefined_timetable WHERE ay = ? AND sec_id = ?");
    $stmt->bind_param("si", $ay, $sec_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        for ($p = $row['st_period'] - 1; $p <= $row['end_period'] - 1; $p++) {
            $timetable[$row['day_no'] - 1][$p] = [
                'code' => $row['subject_code'],
                'staff' => $row['staff_idno'],
                'type' => 'L'
            ];
        }
    }
    $stmt->close();

    $stmt = $dbconn->prepare("SELECT subject_code, staff_idno, teaching_periods FROM subject_staff_mapping WHERE ay = ? AND sec_id = ? AND subject_code NOT IN (SELECT subject_code FROM predefined_timetable WHERE ay = ? AND sec_id = ?)");
    $stmt->bind_param("sisi", $ay, $sec_id, $ay, $sec_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $subjects = [];
    while ($row = $res->fetch_assoc()) $subjects[] = $row;
    $stmt->close();

    usort($subjects, fn($a, $b) => $b['teaching_periods'] - $a['teaching_periods']);

    if (allocatePeriods($timetable, $subjects, 0, $dbconn, $ay, $sem_type)) {
        $_SESSION['timetable'] = $timetable;
        $_SESSION['sec_id'] = $sec_id;
        $_SESSION['ay'] = $ay;
        $_SESSION['sem'] = $sem;
        $_SESSION['sem_type'] = $sem_type;
        echo "<div class='alert alert-success'>All periods allocated successfully. Timetable ready to lock.</div>";
    } else {
        echo "<div class='alert alert-danger'>Unable to allocate all subjects. Please adjust manually.</div>";
    }

    echo "<table border='1'><tr><th>Day / Period</th>";
    for ($p = 1; $p <= $no_of_periods; $p++) echo "<th>P$p</th>";
    echo "</tr>";
    for ($d = 0; $d < $no_of_days; $d++) {
        echo "<tr><td>{$days[$d]}</td>";
        for ($p = 0; $p < $no_of_periods; $p++) {
            $entry = $timetable[$d][$p];
            if (is_array($entry)) {
                $style = ($entry['type'] === 'L') ? " style='background:pink'" : "";
                echo "<td$style>{$entry['code']}<br>{$entry['staff']}</td>";
            } else {
                echo "<td>-</td>";
            }
        }
        echo "</tr>";
    }
    echo "</table>";
    echo "<form method='POST'><button type='submit' name='lock_btn' class='btn btn-success'>Lock Timetable</button></form>";
}

if (isset($_POST['lock_btn'])) {
    if (!isset($_SESSION['timetable']) || !isset($_SESSION['sec_id'])) {
        echo "<div class='alert alert-danger'>No timetable found in session. Generate first.</div>";
        exit;
    }
    $timetable = $_SESSION['timetable'];
    $sec_id = $_SESSION['sec_id'];
    $ay = $_SESSION['ay'];
    $sem = $_SESSION['sem'];
    $sem_type = $_SESSION['sem_type'];
    $locked = 1;
    $room_no = 'TBD';
    $inserted = 0;
    for ($day = 0; $day < count($timetable); $day++) {
        for ($period = 0; $period < count($timetable[$day]); $period++) {
            $entry = $timetable[$day][$period];
            if (!empty($entry) && is_array($entry)) {
                $stmt = $dbconn->prepare("INSERT INTO timetable (staff_idno, day_no, sec_id, sem, st_hour, end_hour, subject_code, room_no, duration, ay, locked, sem_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $d_no = $day + 1;
                $stmt->bind_param("siiiiissisis",
                    $entry['staff'], $d_no, $sec_id, $sem,
                    $period + 1, $period + 1,
                    $entry['code'], $room_no,
                    1, $ay, $locked, $sem_type
                );
                if ($stmt->execute()) $inserted++;
                $stmt->close();
            }
        }
    }
    echo "<div class='alert alert-success'>$inserted periods inserted successfully into timetable.</div>";
    unset($_SESSION['timetable']);
}
?>
