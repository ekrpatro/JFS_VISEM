<?php
session_start();
include("../html/dbconn.php");

$odd_even = 0;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Time Table</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        table { width: 100%; margin-top: 20px; }
        table td, table th { padding: 8px; text-align: center; }
        input[type="text"] { width: 100%; }
        .lab { background-color: pink; }
		.time_slot { background-color: #F1E5AC; }
        .not-allocated { background-color: #ffffcc; }
        .left-align { text-align: left !important; padding-left: 5px; }
		
/* Normal screen view */
table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
}
 table {
        font-family: 'Times New Roman', Times, serif;
    }
	p{
		font-family: 'Times New Roman', Times, serif;
	}

@media print {
    table#head_tbl, 
    table#head_tbl tr, 
    table#head_tbl td, 
    table#head_tbl th {
        border: none !important;
        border-collapse: collapse !important;
        padding: 0 !important;
    }

    table#head_tbl {
        margin: 0 !important;
    }
}
</style>
    
   
</head>
<body class="container mt-4">
<div class="card border-success">
    <div class="card-header bg-success text-white">
        <h3 class="card-title">PUBLISH TIME TABLE</h3>
    </div>
    <div class="card-body">
        <form name='time_frm' method="POST" action="">
            <div class="row">
                
                <div class="form-group col-sm-3">
                    <label>Branch</label>
                    <select name='dept_data' class="form-control select2" required>
                        <?php
                        $dq = "SELECT * FROM dept  where academic_dept='yes' ORDER BY dept_id";
                        $drows = $dbconn->query($dq);
                        foreach ($drows as $row) 
						{                         
							$value = htmlspecialchars("{$row['dept_id']}:{$row['dept_name']}");
							$text = htmlspecialchars($row['dept_code']);
							echo "<option value='$value'>$text</option>";
                        }
                        ?>
                    </select>
					
                </div>
				<div class="form-group col-sm-3">
                    <label>Sem</label>
                    <select name='sem_no' class="form-control select2" required>
                        <option value=1> I Sem </option>
						<option value=2> II Sem </option>
						<option value=3> III Sem </option>
						<option value=4> IV Sem </option>
						<option value=5> V Sem </option>
						<option value=6> VI Sem </option>
						<option value=7> VII Sem </option>
						<option value=8> VIII Sem </option>
                    </select>
					
                </div>
                <div class="form-group col-sm-3">
                    <label>Academic Year</label>
                    <select name='ay' class="form-control select2" required>
                        <option value='2025-26'>2025-26</option>					
                    </select>
                </div>
				
				
                <div class="form-group col-sm-3 d-flex align-items-end">
         <input type='submit' class="btn btn-primary btn-block" name='publish' value="publish">
                </div>
                
				
            </div>
        </form>
    </div>
</div>
<!-- Modal -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Popper.js (required for Bootstrap modals) -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<!-- Bootstrap JS (must match your Bootstrap version) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function () {
        $('.select2').select2({
            tags: true,
            theme: 'bootstrap4',
            placeholder: "Select or type...",
            allowClear: true,
            width: '100%'
        });       
        
    });
</script>
</body>
</html>
<?php
if (isset($_POST['publish'])) {
    $dept_data = $_POST['dept_data'];
    $ay = $_POST['ay'];
    $sem_no = $_POST['sem_no'];

    $dept_arr = explode(":", $dept_data);
    $dept_id = $dept_arr[0];
    $dept_name = $dept_arr[1];
	
	$room_q ="UPDATE timetable t
JOIN room_tt r
  ON t.ay = r.ay
  AND t.sec_id = r.sec_id
  AND t.day_no = r.day_no
  AND t.st_hour = r.period
SET t.room_no = r.room_no WHERE t.sec_id IN (
        SELECT sec_id FROM sec WHERE dept_id = ? AND sem = ?
    ) AND t.ay = ?";
	 $stmt = $dbconn->prepare($room_q);
    if ($stmt) {
        $stmt->bind_param("iis", $dept_id, $sem_no, $ay);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success mt-3'> room updated successfully!</div>";
        }
	}
	

    $q = "INSERT INTO class_timetable (
        ct_no, staff_idno, day_no, sec_id, sem,
        st_hour, end_hour, subject_code, room_no,
        duration, ay
    )
    SELECT 
        t.ct_no,
        grouped.staff_ids,
        t.day_no,
        t.sec_id,
        t.sem,
        t.st_hour,
        t.end_hour,
        t.subject_code,
        t.room_no,
        t.duration,
        t.ay
    FROM timetable t
    JOIN (
        SELECT
            ay,
            sec_id,
            day_no,
            st_hour,
            GROUP_CONCAT(DISTINCT staff_idno ORDER BY staff_idno SEPARATOR '/') AS staff_ids
        FROM timetable
        GROUP BY ay, sec_id, day_no, st_hour
    ) AS grouped
    ON t.ay = grouped.ay
       AND t.sec_id = grouped.sec_id
       AND t.day_no = grouped.day_no
       AND t.st_hour = grouped.st_hour
    WHERE t.sec_id IN (
        SELECT sec_id FROM sec WHERE dept_id = ? AND sem = ?
    ) AND t.ay = ?";

    $stmt = $dbconn->prepare($q);
    if ($stmt) {
        $stmt->bind_param("iis", $dept_id, $sem_no, $ay);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success mt-3'>Timetable published successfully!</div>";
        } else {
            echo "<div class='alert alert-danger mt-3'>Error executing query: " . $stmt->error . "</div>";
        }
        $stmt->close();
    } else {
        echo "<div class='alert alert-danger mt-3'>Prepare failed: " . $dbconn->error . "</div>";
    }
}

?>

