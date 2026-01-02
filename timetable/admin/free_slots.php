<?php
session_start();
include("../html/dbconn.php");

// Initialize
$ay = $sem_type = $day_no = $dept_id = "";
$results = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ay = $_POST['ay'];
    $sems = explode(",", $_POST['sems']);       // now array: [1,3,5,7]
    $periods = explode(",", $_POST['periods']); // now array: [4,5,6]
    $day_no = intval($_POST['day_no']);
    $dept_id = intval($_POST['dept_id']);

    // Prepare comma-separated values for IN clause
    $sem_list = implode(",", array_map('intval', $sems));
    $period_list = implode(",", array_map('intval', $periods));

    // Final SQL query with dynamic IN clause
    $sql = "
    SELECT 
        f.staff_idno,
        f.staff_name,
        (
            SELECT COUNT(*) 
            FROM class_timetable t2
            WHERE t2.ay = ?
              AND t2.sem IN ($sem_list)
              AND t2.sec_id IN (
                  SELECT sec_id FROM sec WHERE dept_id = ?
              )
              AND CONCAT('/', t2.staff_idno, '/') LIKE CONCAT('%/', f.staff_idno, '/%')
        ) AS total_allotted_periods
    FROM 
        staff f
    WHERE 
        f.possision = 'Active'
        AND f.staff_type = 'Teaching'
        AND f.dept_id = ?
        AND f.staff_idno NOT IN (
            SELECT DISTINCT TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(t.staff_idno, '/', numbers.n), '/', -1)) AS staff_idno
            FROM class_timetable t
            JOIN (
                SELECT 1 AS n UNION SELECT 2 UNION SELECT 3 UNION 
                SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7
            ) numbers
            ON CHAR_LENGTH(t.staff_idno) - CHAR_LENGTH(REPLACE(t.staff_idno, '/', '')) >= numbers.n - 1
            WHERE t.day_no = ?
              AND t.ay = ?
              AND t.sem IN ($sem_list)
              AND t.st_hour IN ($period_list)
              AND t.sec_id IN (
                  SELECT sec_id FROM sec WHERE dept_id = ?
              )
        )
    ORDER BY 
        f.staff_idno
    ";

    // Now prepare and bind the remaining scalar variables
    $stmt = $dbconn->prepare($sql);
    $stmt->bind_param("siisii", $ay, $dept_id, $dept_id, $day_no, $ay, $dept_id);
    $stmt->execute();
    $results = $stmt->get_result();
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Free Faculty Finder</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        table { border-collapse: collapse; width: 80%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background-color: #f0f0f0; }
        label { display: inline-block; width: 120px; margin-top: 10px; }
        input, select { padding: 5px; width: 200px; }
    </style>
</head>
<body>

<h2>Check  Faculty Free Slots</h2>

<form method="POST">
    <label>Academic Year:</label>
    <input type="text" name="ay" value="<?= htmlspecialchars($ay) ?>" required><br>
	<label>Sem Type:</label>
    <select name="sems">
        <option value="1,3,5,7"> ODD (I,III,V,VII)</option>
        <option value="2,4,6,8"> EVEN (II,IV,VI,VIII)</option>
    </select><br>

    <label>Session Type:</label>
    <select name="periods">
        <option value="1,2,3">FORE-NOON</option>
        <option value="4,5,6">AFTER-NOON</option>
    </select><br>

    <label>Day Number:</label>
    <input type="number" name="day_no" min="1" max="7" value="<?= htmlspecialchars($day_no) ?>" required><br>

    <label>Department ID:</label>
    <input type="number" name="dept_id" value="<?= htmlspecialchars($dept_id) ?>" required><br><br>

    <input type="submit" value="Find Free Faculty">
</form>

<?php 
$day_names=Array(1=>"MonDay",2=>"TuesDay",3=>"WednesDay",4=>"ThursDay",5=>"FriDay",6=>"SaturDay");
if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
    <h3>Faculty Available  for Lab <?= $dept_id ?> on Day <?= $day_names[$day_no] ?> (<?= $ay ?>)</h3>
    <?php if ($results->num_rows > 0): ?>
        <table>
            <tr>
                <th>Staff ID</th>
                <th>Staff Name</th>
                <th>Total Allotted Periods</th>
            </tr>
            <?php while ($row = $results->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['staff_idno']) ?></td>
                    <td style='text-align:left;'><?= htmlspecialchars($row['staff_name']) ?></td>
                    <td><?= $row['total_allotted_periods'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No free faculty found for the selected criteria.</p>
    <?php endif; ?>
<?php endif; ?>

</body>
</html>
