<?php
include("../html/dbconn.php");

$days = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
$no_of_periods = 6;

$staff_idno = '';
$ay = '2025-26';
$sem_type = 'ODD';

$timetable = array_fill(0, count($days), array_fill(0, $no_of_periods, "-"));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staff_idno = $_POST['staff_idno'];
    $ay = $_POST['ay'];
    $sem_type = $_POST['sem_type'];

    $stmt = $dbconn->prepare("SELECT day_no, st_hour, end_hour, subject_code, room_no, sec_code, t.sem 
        FROM timetable t inner join sec s on t.sec_id=s.sec_id
        WHERE ay = ? AND staff_idno = ? AND sem_type = ?");
    $stmt->bind_param("sss", $ay, $staff_idno, $sem_type);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $day = $row['day_no'] - 1;
        for ($p = $row['st_hour']; $p <= $row['end_hour']; $p++) {
            $period = $p - 1;
            $timetable[$day][$period] = "{$row['subject_code']}<br>Sec: {$row['sec_code']}<br>Rm: {$row['room_no']}";
        }
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Staff Timetable</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        table { width: 100%; margin-top: 20px; }
        th, td { text-align: center; padding: 8px; }
        td { vertical-align: top; }
        .table-warning {
            background-color: #fff3cd !important;
            font-weight: bold;
        }
    </style>
</head>
<body class="container mt-5">
    <h3 class="mb-4">View Staff Timetable</h3>

    <form method="POST" class="form-inline mb-4">
        <label class="mr-2">Staff ID:</label>
        <input type="text" name="staff_idno" class="form-control mr-3" value="<?php echo htmlspecialchars($staff_idno); ?>" required>

        <label class="mr-2">Academic Year:</label>
        <input type="text" name="ay" class="form-control mr-3" value="<?php echo htmlspecialchars($ay); ?>" required>

        <label class="mr-2">Semester Type:</label>
        <select name="sem_type" class="form-control mr-3">
            <option value="ODD" <?php if ($sem_type === 'ODD') echo 'selected'; ?>>ODD</option>
            <option value="EVEN" <?php if ($sem_type === 'EVEN') echo 'selected'; ?>>EVEN</option>
        </select>

        <button type="submit" class="btn btn-primary">Load Timetable</button>
    </form>

    <?php if (!empty($staff_idno)): ?>
    <h4>Timetable for <strong><?php echo htmlspecialchars($staff_idno); ?></strong> - <?php echo "$ay ($sem_type)"; ?></h4>
    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Day / Period</th>
                <?php
                for ($i = 1; $i <= $no_of_periods; $i++) {
                    echo "<th>P$i</th>";
                    if ($i == 3) echo "<th class='table-warning'>LUNCH</th>";  // Insert LUNCH header after P3
                }
                ?>
            </tr>
        </thead>
        <tbody>
            <?php
            $lunch_letters = ['L', 'U', 'N', 'C', 'H', '']; // One letter per row

            for ($d = 0; $d < count($days); $d++) {
                echo "<tr><th>{$days[$d]}</th>";
                for ($p = 0; $p < $no_of_periods; $p++) {
                    echo "<td>{$timetable[$d][$p]}</td>";
                    if ($p == 2) {
                        // After P3, add the lunch column letter for this day
                        echo "<td class='table-warning text-center font-weight-bold'>{$lunch_letters[$d]}</td>";
                    }
                }
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
    <?php endif; ?>
</body>
</html>
