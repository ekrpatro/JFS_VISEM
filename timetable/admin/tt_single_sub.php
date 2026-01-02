<?php
// Database connection
$dbconn = new mysqli("localhost", "root", "", "your_database_name");
if ($dbconn->connect_error) {
    die("Connection failed: " . $dbconn->connect_error);
}

if (isset($_POST['sec_ok'])) {
    $ay = $_POST['ay'];
    $regulation = $_POST['regulation'];
    $sec_arr = explode(":", $_POST['sec_data']);
    $sec_id = $sec_arr[0];
    $sec_name = $sec_arr[1];
    $dept_id = $sec_arr[2];
    $sem = $sec_arr[3];

    $days = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
    $timetable = array_fill(0, 6, array_fill(0, 6, ""));

    // Fetch subject mappings
    $ttq = "SELECT t1.subject_code, t1.staff_idno, t3.sub_type FROM subject_staff_mapping t1 
            LEFT JOIN subjects t3 ON t1.subject_code = t3.subject_code 
            WHERE t1.ay = ? AND t1.sec_id = ?";
    $stmt = $dbconn->prepare($ttq);
    $stmt->bind_param("ss", $ay, $sec_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $theorySubjects = [];
    $labSubjects = [];
    $facultyMap = [];

    while ($row = $result->fetch_assoc()) {
        $facultyMap[$row['subject_code']] = $row['staff_idno'];
        if ($row['sub_type'] == 'T') {
            $theorySubjects[] = $row['subject_code'];
        } elseif ($row['sub_type'] == 'L') {
            $labSubjects[] = ['code' => $row['subject_code'], 'staff' => $row['staff_idno']];
        }
    }
    $stmt->close();

    // Utility functions
    function isFree($timetable, $day, $start, $len = 1) {
        for ($i = 0; $i < $len; $i++) {
            if (!empty($timetable[$day][$start + $i])) return false;
        }
        return true;
    }

    function assignLab(&$timetable, $lab) {
        for ($day = 0; $day < 6; $day++) {
            foreach ([0, 3] as $start) {
                if ($start + 2 < 6 && isFree($timetable, $day, $start, 3)) {
                    for ($i = 0; $i < 3; $i++) {
                        $timetable[$day][$start + $i] = ['code' => $lab['code'], 'staff' => $lab['staff'], 'type' => 'L'];
                    }
                    return true;
                }
            }
        }
        return false;
    }

    foreach ($labSubjects as $lab) {
        assignLab($timetable, $lab);
    }

    // Assign special subjects (Sports, Library)
    foreach (["Sports", "Library"] as $special) {
        while (true) {
            $day = rand(0, 5);
            if (empty($timetable[$day][5])) {
                $timetable[$day][5] = ['code' => $special, 'staff' => '', 'type' => 'S'];
                break;
            }
        }
    }

    // Backtracking for theory subjects
    function backtrack(&$timetable, $subjects, $facultyMap, $index = 0) {
        if ($index >= count($subjects)) return true;

        $subject = $subjects[$index];

        for ($d = 0; $d < 6; $d++) {
            // Check if subject already assigned on that day
            $alreadyAssigned = false;
            for ($p = 0; $p < 6; $p++) {
                if (isset($timetable[$d][$p]['code']) && $timetable[$d][$p]['code'] === $subject) {
                    $alreadyAssigned = true;
                    break;
                }
            }
            if ($alreadyAssigned) continue;

            // Try placing the subject in any free period of the day
            for ($p = 0; $p < 6; $p++) {
                if (empty($timetable[$d][$p])) {
                    $timetable[$d][$p] = ['code' => $subject, 'staff' => $facultyMap[$subject], 'type' => 'T'];
                    if (backtrack($timetable, $subjects, $facultyMap, $index + 1)) return true;
                    $timetable[$d][$p] = ""; // backtrack
                }
            }
        }

        return false;
    }

    // Prepare expanded subject list (each theory subject appears 5 times)
    $fullList = [];
    foreach ($theorySubjects as $sub) {
        for ($i = 0; $i < 5; $i++) $fullList[] = $sub;
    }

    shuffle($fullList); // randomize to avoid bias
    $success = backtrack($timetable, $fullList, $facultyMap);

    if (!$success) {
        echo "<p class='text-danger'><strong>Unable to generate a valid timetable with constraints.</strong></p>";
        return;
    }

    // Display Timetable
    echo "<h3><strong>$sec_name</strong></h3>";
    echo "<table border='1' cellpadding='8'>";
    echo "<tr><th>Day / Period</th>";
    for ($p = 1; $p <= 6; $p++) echo "<th>P$p</th>";
    echo "</tr>";

    for ($d = 0; $d < 6; $d++) {
        $used_subjects = [];
        echo "<tr><td>{$days[$d]}</td>";
        for ($p = 0; $p < 6; $p++) {
            $entry = $timetable[$d][$p];
            if (is_array($entry)) {
                $duplicate = in_array($entry['code'], $used_subjects);
                $used_subjects[] = $entry['code'];
                $style = $entry['type'] === 'L' ? 'background:#f3f3a4' : ($duplicate ? 'background:red;color:white' : '');
                echo "<td style=\"$style\">{$entry['code']}<br>{$entry['staff']}</td>";
            } else {
                echo "<td>-</td>";
            }
        }
        echo "</tr>";
    }
    echo "</table><br>";
}
?>