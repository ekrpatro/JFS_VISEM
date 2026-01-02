<?php


// timetable_allocator.php
if (isset($_POST['sec_ok'])) {
    $ay = $_POST['ay'];
    $regulation = $_POST['regulation'];
    $sec_arr = explode(":", $_POST['sec_data']);
    $sec_id = $sec_arr[0];
    $sec_name = $sec_arr[1];
    $dept_id = $sec_arr[2];
    $sem = $sec_arr[3];
    $sem_type = ($sem % 2 == 1) ? "ODD" : "EVEN";
    $days = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

    // Load predefined timetable
    $timetable = array_fill(0, 6, array_fill(0, 6, ""));
    $q = "SELECT subject_code, day_no, st_period, end_period, staff_idno FROM predefined_timetable WHERE ay=? AND sec_id=?";
    $stmt = $dbconn->prepare($q);
    $stmt->bind_param("si", $ay, $sec_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $day = $row['day_no'] - 1;
        for ($p = $row['st_period'] - 1; $p <= $row['end_period'] - 1; $p++) {
            $timetable[$day][$p] = [
                'code' => $row['subject_code'],
                'staff' => $row['staff_idno'],
                'type' => 'L'
            ];
        }
    }
    $stmt->close();

    // Load subject-staff theory mapping
    $query = "SELECT t1.subject_code, t1.staff_idno, t1.teaching_periods FROM subject_staff_mapping t1 JOIN subjects t2 ON t1.subject_code = t2.subject_code WHERE t1.ay=? AND t1.sec_id=? AND t2.sub_type IN ('T','A')";
    $stmt = $dbconn->prepare($query);
    $stmt->bind_param("si", $ay, $sec_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $theory = [];
    while ($row = $res->fetch_assoc()) {
        $theory[] = $row;
    }
    $stmt->close();

    // Cache for faculty schedule
    $faculty_cache = [];
    function isFacultyFree($staff_id, $day, $period, $ay, $sem_type, $dbconn, &$faculty_cache) {
        $key = "$staff_id-$day-$period";
        if (isset($faculty_cache[$key])) return $faculty_cache[$key];

        $sql = "SELECT 1 FROM timetable WHERE staff_idno=? AND day_no=? AND ? BETWEEN st_hour AND end_hour AND ay=? AND sem_type=?";
        $stmt = $dbconn->prepare($sql);
        $stmt->bind_param("siiss", $staff_id, $day + 1, $period + 1, $ay, $sem_type);
        $stmt->execute();
        $stmt->store_result();
        $free = $stmt->num_rows === 0;
        $stmt->close();

        $faculty_cache[$key] = $free;
        return $free;
    }

    function subjectUsedOnDay($timetable, $subject, $day) {
        foreach ($timetable[$day] as $entry) {
            if (is_array($entry) && $entry['code'] == $subject) return true;
        }
        return false;
    }

    foreach ($theory as $row) {
        $sub = $row['subject_code'];
        $staff = $row['staff_idno'];
        $needed = $row['teaching_periods'];
        $allocated = 0;
        $attempts = 0;

        while ($allocated < $needed && $attempts++ < 500) {
            $day = rand(0, 5);
            if (subjectUsedOnDay($timetable, $sub, $day)) continue;

            for ($p = 0; $p < 6; $p++) {
                if ($timetable[$day][$p] === "" && isFacultyFree($staff, $day, $p, $ay, $sem_type, $dbconn, $faculty_cache)) {
                    $timetable[$day][$p] = ['code' => $sub, 'staff' => $staff, 'type' => 'T'];
                    $allocated++;
                    break;
                }
            }
        }
    }

    $_SESSION['timetable'] = $timetable;
    $_SESSION['sec_id'] = $sec_id;
    $_SESSION['ay'] = $ay;
    $_SESSION['sem_type'] = $sem_type;

    // Display output
    echo "<h4>Generated Timetable for $sec_name</h4>";
    echo "<table border='1'><tr><th>Day</th>";
    for ($i = 1; $i <= 6; $i++) echo "<th>P$i</th>";
    echo "</tr>";
    foreach ($timetable as $i => $day) {
        echo "<tr><td>{$days[$i]}</td>";
        foreach ($day as $slot) {
            if (is_array($slot)) echo "<td>{$slot['code']}<br>{$slot['staff']}</td>";
            else echo "<td>-</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}
?>
