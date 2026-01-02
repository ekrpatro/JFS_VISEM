<?php
session_start();
include("../html/dbconn.php");

// Initialize
$ay = $sem_type = $day_no = $dept_id = "";
$dept_name="";
$fn_an="";
$results = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ay = $_POST['ay'];
    $sems = explode(",", $_POST['sems']);       // now array: [1,3,5,7]
    $periods = explode(",", $_POST['periods']); // now array: [4,5,6]
	if(intval($periods[0])==1)
	{
		$fn_an="ForeNoon";
	}
	else
	{
		$fn_an="AfterNoon";
	}
	
    $day_no = intval($_POST['day_no']);
	
    $dept_arr = explode(":",$_POST['dept_id_code']);
	$dept_id=intval($dept_arr[0]);
	$dept_name=$dept_arr[1];

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

    $stmt = $dbconn->prepare($sql);
    $stmt->bind_param("siisii", $ay, $dept_id, $dept_id, $day_no, $ay, $dept_id);
    $stmt->execute();
    $result_set = $stmt->get_result();
    while ($row = $result_set->fetch_assoc()) {
        $results[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Faculty Free Slots</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        label { display: inline-block; width: 120px; margin-top: 10px; }
        input, select { padding: 5px; width: 200px; }
        .dt-buttons button {
            background-color: #4CAF50 !important;
            color: white !important;
            border: none;
            padding: 6px 12px;
            margin-right: 5px;
            border-radius: 4px;
        }
    </style>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- DataTables Buttons for Export -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
</head>
<body>

<h2>Check Faculty Free Slots</h2>

<form method="POST">
    <label>Academic Year:</label>
    <input type="text" name="ay" value="<?= htmlspecialchars($ay) ?>" required><br>

    <label>Sem Type:</label>
    <select name="sems">
        <option value="1,3,5,7" <?= ($_POST['sems'] ?? '') == "1,3,5,7" ? "selected" : "" ?>>ODD (I,III,V,VII)</option>
        <option value="2,4,6,8" <?= ($_POST['sems'] ?? '') == "2,4,6,8" ? "selected" : "" ?>>EVEN (II,IV,VI,VIII)</option>
    </select><br>

    <label>Session Type:</label>
    <select name="periods">
        <option value="1,2,3" <?= ($_POST['periods'] ?? '') == "1,2,3" ? "selected" : "" ?>>FORE-NOON</option>
        <option value="4,5,6" <?= ($_POST['periods'] ?? '') == "4,5,6" ? "selected" : "" ?>>AFTER-NOON</option>
    </select><br>

    <label>Day Number:</label>
    <input type="number" name="day_no" min="1" max="7" value="<?= htmlspecialchars($day_no) ?>" required><br>

    <label>Department :</label>
    <select name="dept_id_code" required>
		<option value="">-- Select Department --</option>
		<?php
		$dept_query = "SELECT dept_id, dept_code FROM dept WHERE academic_dept = 'yes' AND course_id IN (1,3)";
		$dept_result = $dbconn->query($dept_query);
		while ($row = $dept_result->fetch_assoc()):
			$selected = ($row['dept_id'] == $dept_id) ? "selected" : "";
		?>
			<option value="<?= $row['dept_id'].":".$row['dept_code'] ?>" <?= $selected ?>>
				<?= htmlspecialchars($row['dept_code']) ?> 
			</option>
		<?php endwhile; ?>
	</select><br><br>


    <input type="submit" value="Find Free Faculty">
</form>

<?php 
$day_names = [1 => "Monday", 2 => "Tuesday", 3 => "Wednesday", 4 => "Thursday", 5 => "Friday", 6 => "Saturday", 7 => "Sunday"];

if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
    <h3>Faculty Available on Day <?= $day_names[$day_no] ?>
	(<?= $fn_an ?>)</h3>

    <?php if (count($results) > 0): ?>
        <table id="facultyTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Staff ID</th>
                    <th>Staff Name</th>
					<th>Department</th>
                    <th>Total Allotted Periods</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['staff_idno']) ?></td>
                        <td style='text-align:left;'><?= htmlspecialchars($row['staff_name']) ?></td>
						<td style='text-align:left;'><?= htmlspecialchars($dept_name) ?></td>
                        <td><?= $row['total_allotted_periods'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No free faculty found for the selected criteria.</p>
    <?php endif; ?>
<?php endif; ?>

<script>
$(document).ready(function () {
    $('#facultyTable').DataTable({
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
    });
});
</script>

</body>
</html>
