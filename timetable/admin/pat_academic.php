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
        <h3 class="card-title">TIME TABLE</h3>
    </div>
    <div class="card-body">
        <form name='time_frm' method="POST" action="">
            <div class="row">
                
                <div class="form-group col-sm-3">
                    <label>Branch and Section</label>
                    <select name='sec_data' class="form-control select2" required>
                        <?php
                        $secq = "SELECT * FROM sec  ORDER BY course_id, dept_id, sem, section";
                        $sec_rows = $dbconn->query($secq);
                        foreach ($sec_rows as $sec_row) {                         
							$value = htmlspecialchars("{$sec_row['sec_id']}:{$sec_row['sec_name']}:{$sec_row['dept_id']}:{$sec_row['sem']}:{$sec_row['course_id']}:{$sec_row['section']}");
						$text = htmlspecialchars($sec_row['sec_code']);
							echo "<option value='$value'>$text</option>";

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
				<div class="form-group col-sm-3">
                    <label>W.E.F Date:</label>
					<!--  date('j F, Y') -->
                   <input type="text" name="wef_dt" value="7 July, 2025">
                </div>
				
                <div class="form-group col-sm-3 d-flex align-items-end">
                    <input type='submit' class="btn btn-primary btn-block" name='sec_ok' value="OK">
                </div>
                <div class="form-group col-sm-3 d-flex align-items-end">
                    <input type='button' class="btn btn-primary btn-block" name='delete_tt' id='delete_tt' value="DeeleteTT">
                </div>
            </div>
        </form>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id="confirmDeleteForm" method="POST">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Timetable Deletion</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>To delete the timetable, please enter the password:</p>
          <input type="password" name="delete_password" id="delete_password" class="form-control" required>
          <input type="hidden" name="cancel_tt" value="1">
          <input type="hidden" name="modal_sec_data" id="modal_sec_data">
          <input type="hidden" name="modal_ay" id="modal_ay">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" name="confirm_delete_btn" class="btn btn-danger">Confirm Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Popper.js (required for Bootstrap modals) -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>

<!-- Bootstrap JS (must match your Bootstrap version) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>


<script>

function printTimetable() {
    var printContents = document.getElementById('printableArea').innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload(); 
	// Optional: reload after print to restore functionality

}
</script>

<script>

function custom_printTimetable() {
    var content = document.getElementById('printableArea').innerHTML;
    var win = window.open('', '', 'height=600,width=800');
    win.document.write('<html><head><title>Print Timetable</title>');
    win.document.write('<style>');
    win.document.write(`
        body { font-family: "Times New Roman", serif; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid black; padding: 5px; text-align: center; }
        th { background-color: #eee; }
    `);
    win.document.write('</style>');
    win.document.write('</head><body>');
    win.document.write(content);
    win.document.write('</body></html>');
    win.document.close();
    win.print();
}
</script>


<script>
    $(document).ready(function () {
        $('.select2').select2({
            tags: true,
            theme: 'bootstrap4',
            placeholder: "Select or type...",
            allowClear: true,
            width: '100%'
        });
         // Confirm before deleting timetable
        $('#xcancel_tt').click(function (e) {
            const confirmed = confirm("Are you sure you want to delete the timetable for the selected Academic Year and Section?");
            if (!confirmed) {
                e.preventDefault(); // Stop form submission if not confirmed
            }
        });
         // When "Cancel Timetable" button is clicked, show modal
        $('#delete_tt').click(function () {

            const secData = $("select[name='sec_data']").val();
            const ay = $("select[name='ay']").val();

            // Set into hidden fields inside the modal
            $("#modal_sec_data").val(secData);
            $("#modal_ay").val(ay);
            alert('secData : '+secData);

            $('#deleteConfirmModal').modal('show');
        });
    });
</script>
</body>
</html>
<?php
$days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
$no_of_days = 6;
$no_of_periods = 6;
if (isset($_POST['confirm_delete_btn'])) {
    $entered_password = $_POST['delete_password'];

    // Set your admin password here securely
    $admin_password = 'admin123';  // CHANGE THIS TO A STRONG PASSWORD

    if ($entered_password !== $admin_password) {
        echo "<div class='alert alert-danger'>Incorrect password. Timetable not deleted.</div>";
    } else {
        $sec_data_arr = explode(":", $_POST['modal_sec_data']);
        $ay = $_POST['modal_ay'];
        $sec_id = $sec_data_arr[0];
        $sec_name = $sec_data_arr[1];

        $stmt = $dbconn->prepare("DELETE FROM timetable WHERE ay = ? AND sec_id = ?");
        $stmt->bind_param("si", $ay, $sec_id);
        if ($stmt->execute()) {
            echo "<div class='alert alert-warning'>Timetable for AY: <strong>$ay</strong> and Section ID: <strong>$sec_name</strong> has been deleted.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error deleting timetable. Please try again.</div>";
        }
        $stmt->close();

        unset($_SESSION['timetable']);
    }
}



if (isset($_POST['sec_ok'])) 
{
	$wef_dt=$_POST['wef_dt'];
    $ay = $_POST['ay'];
   // $regulation = $_POST['regulation'];
    $sec_arr = explode(":", $_POST['sec_data']);
    $sec_id = $sec_arr[0];
    $sec_name = $sec_arr[1];
    $dept_id = $sec_arr[2];
    $sem = $sec_arr[3];
	$course_id = $sec_arr[4];
	$section = $sec_arr[5];
    $sem_type = ($sem % 2 == 1) ? "ODD" : "EVEN";
	$degree_names=array(1=>"B.TECH",2=>"M.TECH",3=>"MBA");
	$sem_names=array(1=>"I",2=>"II",3=>"III",4=>"IV",5=>"V",6=>"VI",7=>"VII",8=>"VIII");
	$disp_degree_sem=$degree_names[$course_id]." ".$sem_names[$sem]. " SEMESTER";
	//== show existing Timetable
	$branch_names=array(1=>"CIVIL ENGINEERING",2=>"ELECTRICAL AND ELECTRONICS ENGINEERING",3=>"MECHANICAL ENGINEERING",4=>"ELECTRONICS AND COMMUNICATION ENGINEERING",5=>"COMPUTER SCIENCE AND ENGINEERING",6=>"INFORMATION TECHNOLOGY",7=>"AERONAUTICAL ENGINEERING",9=>"MASTER OF BUSINESS ADMINISTARTION",
	12=>"M.TECH: AEROSPACE ENGINEERING",15=>"M.TECH: EMBEDDED SYSTEMS",17=>"M.TECH: COMPUTER SCIENCE AND ENGINEERING",18=>"M.TECH: STRUCTURAL ENGINEERING",33=>"M.TECH: ELECTRICAL POWER SYSTEMS",34=>"CSE (ARTIFICIAL INTELLIGENCE AND MACHINE LEARNING)",35 =>"CSE (DATA SCIENCE)",36=>"CSE (CYBER SECURITY)", 37 => "COMPUTER SCIENCE AND INFORMATION TECHNOLOGY");
	$disp_br_name=$branch_names[$dept_id];
	if (!array_key_exists($dept_id, $branch_names)) {
		$disp_br_name = $sec_name;
	} 
	else {
    $disp_br_name = $branch_names[$dept_id];
	}
	
	function showTimetable($ay, $sec_id, $sec_name,$disp_br_name,$disp_degree_sem,$section,$wef_dt,$dept_id,$sem)
	{
		$pat_class_found=0;
		global $dbconn, $days, $no_of_days, $no_of_periods;
		$ttq="SELECT 
		t4.sub_short_code, 
		t3.staff_ids, 
		t3.room_no,
		t3.day_no,
		t3.st_hour	
		FROM (
		SELECT 
        GROUP_CONCAT( t1.staff_idno ORDER BY t1.staff_idno) AS staff_ids,
        t1.ay, 
        t1.sec_id, 
        t1.day_no, 
        t1.st_hour,
        MAX(t1.end_hour) AS end_hour,
        MAX(t1.subject_code) AS subject_code,
        MAX(t2.room_no) AS room_no,
        MAX(t1.duration) AS duration,
        MAX(t1.sem) AS sem,
        MAX(t1.sem_type) AS sem_type
    FROM 
        timetable t1 
    INNER JOIN 
        room_tt t2 
        ON t1.ay = t2.ay 
        AND t1.sec_id = t2.sec_id 
		and t1.day_no=t2.day_no
        AND t1.st_hour = t2.period
    WHERE 
        t1.sec_id = ? 
        AND t1.ay = ?
    GROUP BY 
        t1.ay, 
        t1.sec_id, 
        t1.day_no, 
        t1.st_hour
	) t3  
	INNER JOIN 
		subjects t4 
		ON t3.subject_code = t4.subject_code;
";
		$stmt = $dbconn->prepare($ttq);
		$stmt->bind_param("is", $sec_id, $ay);
		$stmt->execute();
		$res = $stmt->get_result();
	//"EID: ".substr($row['staff_ids'],4,5)
		if ($res->num_rows > 0) {
			$timetable = array_fill(0, $no_of_days, array_fill(0, $no_of_periods, ""));
			while ($row = $res->fetch_assoc()) {
				$arr_ids=explode('/',$row['staff_ids']);
				$st_code="EID: ";						

			//$st_code="EID: ".substr($row['staff_ids'],4,5);
				$fac_id_len=strlen($row['staff_ids']);
				if($fac_id_len==9)
					$st_code="EID: ".substr($row['staff_ids'],4,5);
				else if($fac_id_len >9  ){
					$st_code="EID: ".substr($row['staff_ids'],4,5)."<br>/".substr($row['staff_ids'],14,5);
					}
					
				$day = $row['day_no'] - 1;
				$period = $row['st_hour'] - 1;
				$timetable[$day][$period] = [
					'code' => $row['sub_short_code'],
					'staff' => $st_code,
					'room_no' => $row['room_no'],
					'type' => 'T'
				];
			}
			$stmt->close();
			$time_periods = ['9:30 AM <br>to<br> 10:25 AM','10:25 AM <br>to<br> 11:20 AM',
            '11:20 AM <br>to<br> 12:15 PM',
            '  ', 
            '01:05 PM <br>to<br> 02:00 PM', '02:00 PM <br>to<br> 02:55 PM', '02:55 PM <br>to<br> 03:50 PM'];
			
			echo "<div style='text-align:right; margin-bottom:10px;'>
        <button onclick=\"printTimetable()\" class='btn btn-secondary'>🖨️ Print Timetable</button>
      </div>";
	  echo "<div id='printableArea'>";
			echo "<table border='1' id='head_tbl'>
			<tr><th colspan=8><img src='../img/iare.jpg' alt='College Logo' height='80'></th></tr>
			<tr>
			 <td colspan='8' style='font-size:16px; font-weight:bold; font-family:Times New Roman; text-align:center;'>
        {$disp_br_name}
    </td>
			
			</tr>
			
			<tr>
			 <td colspan='8' style='font-size:16px; font-weight:bold; font-family:Times New Roman; text-align:center;'>
        {$disp_degree_sem}
    </td>			
			</tr>
			<tr>
			 <td colspan='8' style='font-size:16px; font-family:Times New Roman; text-align:center;'>
        SECTION - {$section}
    </td>		
			</tr>
			
			<tr><td colspan='8' style='font-size:16px; font-family:Times New Roman; text-align:center;'>TIME TABLE</td></tr>
			<tr><td colspan='8' style='font-size:16px;  font-family:Times New Roman; text-align:center;'>ACADEMIC YEAR: {$ay}</td></tr>
			<tr><td colspan='8' style='font-size:14px; font-weight:bold; font-family:Times New Roman; text-align:right;' >W.E.F: {$wef_dt}</td></tr>
			</table>";
			echo "
<table border='1' id='data_tbl' style='font-family:'Times New Roman'>
			<tr><th class='time_slot'>Day / Period</th>";

			
			for ($p = 1; $p <= $no_of_periods+1; $p++) {
				echo "<th class='time_slot'>".$time_periods[$p-1]."</th>";
				//if ($p == 3) echo "<th class='time_slot'></th>";
			}

			echo "</tr>";

			$lunch_letters = ['L', 'U', 'N', 'C', 'H', ''];

			for ($d = 0; $d < $no_of_days; $d++) {
				echo "<tr><td style='font-size:14px; font-weight:bold; font-family:Times New Roman; text-align:center;'>{$days[$d]}</td>";
				for ($p = 0; $p < $no_of_periods; $p++) {
					$entry = $timetable[$d][$p];
					if (is_array($entry)) {
						if($entry['code']=='PAT')
						{
							$pat_class_found=1;
							$entry['staff']="";
							$entry['room_no']="";
						}
						echo "<td style='font-size:14px;  font-family:Times New Roman; text-align:center;'>{$entry['code']}<br>{$entry['staff']} <br>{$entry['room_no']}</td>";
					} else {
						echo "<td>-</td>";
					}
					if ($p == 2) {
						echo "<td class='table-warning text-center font-weight-bold'>{$lunch_letters[$d]}</td>";
					}
				}
				echo "</tr>";
			}

			echo "</table>";
			$info_q="select s1.`staff_idno`, s1.staff_name,s1.subject_code,s2.subject_name,s2.sub_short_code from ( SELECT `ay`, `sec_id`, `subject_code`, t1.`staff_idno`, t2.staff_name,`teaching_periods` FROM `subject_staff_mapping` t1 inner join staff t2 on t1.staff_idno=t2.staff_idno WHERE ay=? and t1.sec_id=?) s1 inner join subjects s2 on s1.subject_code=s2.subject_code;";
			
			//starts
			$stmt = mysqli_prepare($dbconn, $info_q);
			mysqli_stmt_bind_param($stmt, "si", $ay, $sec_id);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			

			if (mysqli_num_rows($result) > 0) {
				echo "<table border='1' cellpadding='10' cellspacing='0'>";
				echo "<tr>
						<th colspan=5 style='text-align:center;'>Course and 
						Faculty Details</th>
						
					  </tr>";
				echo "<tr>
						<th>Course<br>Abbreviation</th>
						<th>Course Code</th>
						<th>Course Name</th>
						<th>EID</th>
						<th>Faculty Name</th>
						
					  </tr>";
				
				while ($row = mysqli_fetch_assoc($result)) {
					if($row['subject_code']=='PAT')
					{   $row['staff_idno']='';
						$row['staff_name']="";
					}
					else{
					
					echo "<tr>
					<td>{$row['sub_short_code']}</td>
					<td>{$row['subject_code']}</td>
							<td style='text-align:left'>{$row['subject_name']}</td>
							<td>".substr($row['staff_idno'],4,5)."</td>
							<td style='text-align:left'>{$row['staff_name']}</td>
							
						  </tr>";
					}
					
				}

				echo "</table>";
				echo "<br>";
			echo "<p><b>Date: ".date('j F, Y')."</b></p>";
			echo "<table class='head_tbl' style='margin-top:50px;border-collapse: collapse; border: none; font-weight: bold;'>
        <tr>
            <td style='border: none; padding: 5px;'>HOD</td>
            <td style='border: none; padding: 5px;'>DEAN-AAMS</td>
            <td style='border: none; padding: 5px;'>ADVISOR-ACADEMICS</td>
            <td style='border: none; padding: 5px;'>PRINCIPAL</td>
        </tr>
      </table>";
				
				/* PAT TIMETABLE STARTS*/
				if($pat_class_found==1)
				{
						$pat_q="SELECT `id`, `ay`, `dept_id`, `sem`, `day_no`, `start_hour`, `end_hour`, GROUP_CONCAT(DISTINCT t1.sub_code ORDER BY t1.sub_code) AS sub_codes, GROUP_CONCAT(DISTINCT t1.sub_short_code ORDER BY t1.sub_short_code) AS sub_short_codes, 
		GROUP_CONCAT(DISTINCT substr(t1.staff_idno,5,5) ORDER BY t1.staff_idno) AS staff_ids,
		GROUP_CONCAT(DISTINCT t1.batch_type ORDER BY t1.batch_type) AS batch_types,
		GROUP_CONCAT(DISTINCT t1.room_no ORDER BY t1.room_no) AS room_nos

		  FROM `pat_tt` t1  WHERE t1.ay=? and t1.dept_id=? and t1.sem=? group by t1.day_no,t1.start_hour;";
			
					//starts
					$pat_stmt = mysqli_prepare($dbconn, $pat_q);
					mysqli_stmt_bind_param($pat_stmt, "sii", $ay, $dept_id,$sem);
					mysqli_stmt_execute($pat_stmt);
					$pat_result = mysqli_stmt_get_result($pat_stmt);
					// Step 1: Collect all unique periods and organize data
					$periods = [];  // stores unique period start_hour values
					$timetable = []; // stores data as $timetable[day_no][start_hour] = subject info

					while ($row = mysqli_fetch_assoc($pat_result)) {
						$day = $row['day_no'];
						$period = $row['start_hour'];
						$periods[$period] = true; // collect all unique periods
						$timetable[$day][$period] = $row['sub_short_codes'] . "<br>EID: " . $row['staff_ids'] . "<br>" . $row['room_nos'];
					}

					// Sort periods (column headers)
					$period_list = array_keys($periods);
					sort($period_list);
					//STEP print header
					echo "<table border='1' id='head_tbl'>
					<tr><th colspan=3><img src='../img/iare.jpg' alt='College Logo' height='80'></th></tr>
					<tr>
					 <td colspan='3' style='font-size:16px; font-weight:bold; font-family:Times New Roman; text-align:center;'>
				{$disp_br_name}
			</td>
					
					</tr>
					
					<tr>
					 <td colspan='3' style='font-size:16px; font-weight:bold; font-family:Times New Roman; text-align:center;'>
				{$disp_degree_sem}
			</td>			
					</tr>
					<tr>
					 <td colspan='3' style='font-size:16px; font-family:Times New Roman; text-align:center;'>
				SECTION - {$section}
			</td>		
					</tr>
					
					<tr><td colspan='3' style='font-size:16px; font-weight:bold;font-family:Times New Roman; text-align:center;'>PAT TIME TABLE</td></tr>
					<tr><td colspan='3' style='font-size:16px;  font-family:Times New Roman; text-align:center;'>ACADEMIC YEAR: {$ay}</td></tr>
					<tr><td colspan='3' style='font-size:14px; font-weight:bold; font-family:Times New Roman; text-align:right;' >W.E.F: {$wef_dt}</td></tr>
					</table>";
					
					// Step 2: Print table
					echo "<table border='1' cellpadding='5' cellspacing='0'>";
					echo "<thead>
					
					<tr><th class='time_slot'>Day / Period</th>";
					foreach ($period_list as $p) {
						if($p<=3)
							echo "<th class='time_slot'>ForeNoon<br>9:30 AM TO 12:15 PM</th>";
						else
							echo "<th class='time_slot'>AfterNoon<br>01:05 PM TO 03:50 PM</th>";
					}
					echo "</tr></thead><tbody>";

					// Step 3: Print each day's row
					for ($d = 1; $d <= 6; $d++) {
						echo "<tr>";
						echo "<td><b>" . $days[$d - 1] . "</b></td>";
						foreach ($period_list as $p) {
							if (isset($timetable[$d][$p])) {
								echo "<td>" . $timetable[$d][$p] . "</td>";
							} else {
								echo "<td>-</td>"; // empty cell
							}
						}
						echo "</tr>";
					}

					echo "</tbody></table>";
					
					
	  // pat course and faculty Details-starts
		$sql = "SELECT DISTINCT 
            t3.sub_short_code,
            t1.sub_code,
            t3.subject_name,
            GROUP_CONCAT(DISTINCT SUBSTR(t1.staff_idno,5,5) ORDER BY t1.staff_idno) AS staff_ids,
            GROUP_CONCAT(DISTINCT t2.staff_name ORDER BY t1.staff_idno) AS staff_names
        FROM pat_tt t1
        INNER JOIN staff t2 ON t1.staff_idno = t2.staff_idno
        INNER JOIN subjects t3 ON t1.sub_code = t3.subject_code
        WHERE t1.ay = ? AND t1.dept_id = ? AND t1.sem = ?
        GROUP BY t1.sub_code
        ORDER BY t1.sub_code ASC";


		$stmt = mysqli_prepare($dbconn, $sql);
		mysqli_stmt_bind_param($stmt, "sii", $ay, $dept_id, $sem);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);

			// Display in table
			echo "<table border='1' cellpadding='5' cellspacing='0'>";
			echo "<thead>
			<tr>
				<th colspan=5 style='text-align:center'>Course and Faculty Details</th>
			</tr>
			<tr>
				<th>Course<br>Abbreviation</th>
				<th>Course Code</th>
				<th>Course Name</th>
				<th>Emp.Ids</th>
				<th>Faculty Names</th>
			</tr>
			</thead>
			<tbody>";

			if (mysqli_num_rows($result) > 0) {
				while ($row = mysqli_fetch_assoc($result)) {
					$ids=explode(",",$row['staff_ids']);
					$names=explode(",",$row['staff_names']);


					$name_lines = "";
					$id_lines = "";
					for ($i = 0; $i < count($ids); $i++) {
					$name_lines .= htmlspecialchars($names[$i]) . "<br>";
					$id_lines .= "EID: ".htmlspecialchars($ids[$i]) ."<br>";
					}
					
					echo "<tr>";
					echo "<td>" . htmlspecialchars($row['sub_short_code']) . "</td>";
					echo "<td>" . htmlspecialchars($row['sub_code']) . "</td>";
					echo "<td style='text-align:left;'>" . htmlspecialchars($row['subject_name']) . "</td>";
					echo "<td style='text-align:center;'>" . $id_lines. "</td>";
					echo "<td style='text-align:left;'>" . $name_lines . "</td>";
					echo "</tr>";
				}
			} else {
				echo "<tr><td colspan='5'>No subjects found.</td></tr>";
			}

			echo "</tbody></table>";
	  // pat course and faculty Details-ends
	  echo "<br>";
			echo "<p><b>Date: ".date('j F, Y')."</b></p>";
			echo "<table class='head_tbl' style='margin-top:50px;border-collapse: collapse; border: none; font-weight: bold;'>
        <tr>
            <td style='border: none; padding: 5px;'>HOD</td>
			<td style='border: none; padding: 5px;'>DEAN-CDC</td>
            <td style='border: none; padding: 5px;'>DEAN-AAMS</td>
            <td style='border: none; padding: 5px;'>ADVISOR-ACADEMICS</td>
            <td style='border: none; padding: 5px;'>PRINCIPAL</td>
        </tr>
      </table>";
					
					

				}
				
			   /* PAT-TIMETABLE ENDS */
			}
			//ends
			
			
			
			echo "</div>";
			exit;
		}

		$stmt->close();
		echo "<br> Generate New TimeTable";
	}

	
	//==ends existing
	showTimetable($ay,$sec_id,$sec_name,$disp_br_name,$disp_degree_sem,$section,$wef_dt,$dept_id,$sem);
    // Step 1: Initialize timetable
    $timetable = array_fill(0, $no_of_days, array_fill(0, $no_of_periods, ""));

   /* // Step 2: Load predefined lab periods
    $stmt = $dbconn->prepare("SELECT t1.subject_code, day_no, st_period, end_period, staff_idno,sub_type FROM predefined_timetable t1 inner join subjects t2 on t1.subject_code=t2.subject_code WHERE ay = ? AND sec_id = ? and faculty_type='MAIN'"); // CONSIDER MAIN
    $stmt->bind_param("si", $ay, $sec_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        for ($p = $row['st_period'] - 1; $p <= $row['end_period'] - 1; $p++) {
            $timetable[$row['day_no'] - 1][$p] = [
                'code' => $row['subject_code'],
                'staff' => $row['staff_idno'],
                'type' => $row['sub_type'],
            ];
        }
    }
    $stmt->close();
    // Step 3: Load theory subjects
    $stmt = $dbconn->prepare("SELECT t1.subject_code, staff_idno, teaching_periods,t2.sub_type 
        FROM subject_staff_mapping  t1
		inner join subjects t2 on t1.subject_code=t2.subject_code
		
        WHERE ay = ? AND sec_id = ? 
        AND t1.subject_code NOT IN (
            SELECT subject_code FROM predefined_timetable WHERE ay = ? AND sec_id = ?
        )");
    $stmt->bind_param("sisi", $ay, $sec_id, $ay, $sec_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $subjects = [];
    while ($row = $res->fetch_assoc()) {
        $subjects[] = $row;
    }
    $stmt->close();
	

    // ========== Helper Functions ==========
    function isAvailable($timetable, $day, $period) {
        return empty($timetable[$day][$period]);
    }

    function isStaffAvailable($dbconn, $staff, $day, $period, $ay, $sem_type) {
        $stmt = $dbconn->prepare("SELECT * FROM timetable 
            WHERE staff_idno like ? AND day_no = ? AND ? BETWEEN st_hour AND end_hour 
            AND ay = ? AND sem_type = ?");
        $dno = $day + 1;
        $pno = $period + 1;
        $stmt->bind_param("siiss", $staff, $dno, $pno, $ay, $sem_type);
        $stmt->execute();
        $stmt->store_result();
        $tt_available = $stmt->num_rows === 0;
        $stmt->close();
        
        $pre_stmt = $dbconn->prepare("SELECT * FROM predefined_timetable 
            WHERE staff_idno like ? AND day_no = ? AND ? BETWEEN st_period AND end_period 
            AND ay = ? AND sem_type = ?");
        
        $pre_stmt->bind_param("siiss", $staff, $dno, $pno, $ay, $sem_type);
        $pre_stmt->execute();
        $pre_stmt->store_result();
        $pre_tt_available = $pre_stmt->num_rows === 0;
        $pre_stmt->close();

        //return $available;
        if( $pre_tt_available==false || $tt_available==false )
            return false;
        else
            return $pre_tt_available == $tt_available ;
    }

    function isValidTheorySlotSet($combo) {
        $grouped = [];
        foreach ($combo as [$d, $p]) {
            $grouped[$d][] = $p;
        }
        foreach ($grouped as $periods) {
            sort($periods);
            for ($i = 1; $i < count($periods); $i++) {
                if ($periods[$i] === $periods[$i - 1] + 1) {
                    return false; // Found consecutive periods
                }
            }
        }
        return true;
    }

    function combinations($arr, $r) {
        shuffle($arr); // Randomize to improve distribution
        $result = [];
        $n = count($arr);
        if ($r > $n) return $result;

        $indices = range(0, $r - 1);
        while (true) {
            $combo = [];
            foreach ($indices as $i) $combo[] = $arr[$i];
            $result[] = $combo;

            $i = $r - 1;
            while ($i >= 0 && $indices[$i] === $i + $n - $r) $i--;
            if ($i < 0) break;
            $indices[$i]++;
            for ($j = $i + 1; $j < $r; $j++) {
                $indices[$j] = $indices[$j - 1] + 1;
            }
        }
        return $result;
    }
	
	function isOneTheoryPerDay($timetable, $code, $day) {
    foreach ($timetable[$day] as $entry) {
        if (is_array($entry) && $entry['code'] === $code && $entry['type'] === 'T') {
            return false;
        }
    }
    return true;
	}
	function isOneTheoryPerDayPerStaffSec($timetable, $code, $staff, $day, $sec_id) {
    foreach ($timetable[$day] as $entry) {
        if (
            is_array($entry) &&
            $entry['type'] === 'T' &&
            $entry['code'] === $code &&
            $entry['staff'] === $staff
        ) 
		{
            return false;
        }
    }
    return true;
}

    function allocatePeriods(&$timetable, $subjects, $index, $dbconn, $ay, $sem_type,$sec_id) {
        global $no_of_days, $no_of_periods;
        if ($index === count($subjects)) return true;
        $subject = $subjects[$index];
        $code = $subject['subject_code'];
        $staff = $subject['staff_idno'];
        $required = (int)$subject['teaching_periods'];
		
		//$isTheory = ($subject['sub_type'] === 'T');
		$isTheory = isset($subject['sub_type']) && in_array(strtoupper($subject['sub_type']), ['T']);


        $slots = [];
        for ($d = 0; $d < $no_of_days; $d++) {
            for ($p = 0; $p < $no_of_periods; $p++) {
				
				if (isAvailable($timetable, $d, $p) && isStaffAvailable($dbconn, $staff, $d, $p, $ay, $sem_type) ) {
                    $slots[] = [$d, $p];
                }
                
				

				
				
            }
        }

        if (count($slots) < $required) return false;

        $combinations = combinations($slots, $required);

        foreach ($combinations as $combo) {
            if (!isValidTheorySlotSet($combo)) continue;

            foreach ($combo as [$d, $p]) {
                $timetable[$d][$p] = ['code' => $code, 'staff' => $staff, 'type' => 'T'];
            }

            if (allocatePeriods($timetable, $subjects, $index + 1, $dbconn, $ay, $sem_type,$sec_id)) return true;

            foreach ($combo as [$d, $p]) {
                $timetable[$d][$p] = "";
            }
        }

        return false;
    }
*/
$timetable = array_fill(0, $no_of_days, array_fill(0, $no_of_periods, ""));
	// Step 2: Load predefined lab periods
$stmt = $dbconn->prepare("SELECT t1.subject_code, day_no, st_period, end_period, staff_idno,sub_type FROM predefined_timetable t1 INNER JOIN subjects t2 ON t1.subject_code = t2.subject_code WHERE ay = ? AND sec_id = ? AND faculty_type = 'MAIN'");
$stmt->bind_param("si", $ay, $sec_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    for ($p = $row['st_period'] - 1; $p <= $row['end_period'] - 1; $p++) {
        $timetable[$row['day_no'] - 1][$p] = [
            'code' => $row['subject_code'],
            'staff' => $row['staff_idno'],
            'type' => $row['sub_type'],
        ];
    }
}
$stmt->close();

// Step 3: Load all subjects with total periods (subtract predefined ones)
$stmt = $dbconn->prepare("SELECT t1.subject_code, staff_idno, teaching_periods, t2.sub_type FROM subject_staff_mapping t1 INNER JOIN subjects t2 ON t1.subject_code = t2.subject_code WHERE ay = ? AND sec_id = ?");
$stmt->bind_param("si", $ay, $sec_id);
$stmt->execute();
$res = $stmt->get_result();
$subjects = [];

while ($row = $res->fetch_assoc()) {
    $allocated = 0;
    for ($d = 0; $d < $no_of_days; $d++) {
        for ($p = 0; $p < $no_of_periods; $p++) {
            if (
                is_array($timetable[$d][$p]) &&
                $timetable[$d][$p]['code'] === $row['subject_code'] &&
                $timetable[$d][$p]['staff'] === $row['staff_idno']
            ) {
                $allocated++;
            }
        }
    }
    $remaining = $row['teaching_periods'] - $allocated;
    if ($remaining > 0) {
        $row['teaching_periods'] = $remaining;
        $subjects[] = $row;
    }
}
$stmt->close();

function allocatePeriods(&$timetable, $subjects, $index, $dbconn, $ay, $sem_type, $sec_id) {
    global $no_of_days, $no_of_periods;
    if ($index === count($subjects)) return true;

    $subject = $subjects[$index];
    $code = $subject['subject_code'];
    $staff = $subject['staff_idno'];
    $required = (int)$subject['teaching_periods'];
    $isTheory = isset($subject['sub_type']) && strtoupper($subject['sub_type']) === 'T';

    $slots = [];
    $dailyCount = array_fill(0, $no_of_days, 0);

    for ($d = 0; $d < $no_of_days; $d++) {
        $count = 0;
        for ($p = 0; $p < $no_of_periods; $p++) {
            if (is_array($timetable[$d][$p]) && $timetable[$d][$p]['staff'] === $staff && $timetable[$d][$p]['type'] === 'T') {
                $count++;
            }
        }
        $dailyCount[$d] = $count;
    }

    for ($d = 0; $d < $no_of_days; $d++) {
        for ($p = 0; $p < $no_of_periods; $p++) {
            if (
                isAvailable($timetable, $d, $p) &&
                isStaffAvailable($dbconn, $staff, $d, $p, $ay, $sem_type)
            ) {
                if (!$isTheory || ($dailyCount[$d] < 1)) {
                    $slots[] = [$d, $p];
                }
            }
        }
    }

    if (count($slots) < $required) return false;

    $combinations = combinations($slots, $required);
    foreach ($combinations as $combo) {
        foreach ($combo as [$d, $p]) {
            $timetable[$d][$p] = ['code' => $code, 'staff' => $staff, 'type' => $isTheory ? 'T' : $subject['sub_type']];
        }
        if (allocatePeriods($timetable, $subjects, $index + 1, $dbconn, $ay, $sem_type, $sec_id)) return true;
        foreach ($combo as [$d, $p]) {
            $timetable[$d][$p] = "";
        }
    }
    return false;
}

function isAvailable($timetable, $day, $period) {
    return empty($timetable[$day][$period]);
}

function isStaffAvailable($dbconn, $staff, $day, $period, $ay, $sem_type) {
    $stmt = $dbconn->prepare("SELECT 1 FROM timetable WHERE staff_idno LIKE ? AND day_no = ? AND ? BETWEEN st_hour AND end_hour AND ay = ? AND sem_type = ?");
    $dno = $day + 1;
    $pno = $period + 1;
    $stmt->bind_param("siiss", $staff, $dno, $pno, $ay, $sem_type);
    $stmt->execute();
    $stmt->store_result();
    $conflict = $stmt->num_rows > 0;
    $stmt->close();

    $pre_stmt = $dbconn->prepare("SELECT 1 FROM predefined_timetable WHERE staff_idno LIKE ? AND day_no = ? AND ? BETWEEN st_period AND end_period AND ay = ? AND sem_type = ?");
    $pre_stmt->bind_param("siiss", $staff, $dno, $pno, $ay, $sem_type);
    $pre_stmt->execute();
    $pre_stmt->store_result();
    $pre_conflict = $pre_stmt->num_rows > 0;
    $pre_stmt->close();

    return !$conflict && !$pre_conflict;
}

function combinations($arr, $r) {
    shuffle($arr);
    $result = [];
    $n = count($arr);
    if ($r > $n) return $result;
    $indices = range(0, $r - 1);
    while (true) {
        $combo = [];
        foreach ($indices as $i) $combo[] = $arr[$i];
        $result[] = $combo;
        $i = $r - 1;
        while ($i >= 0 && $indices[$i] === $i + $n - $r) $i--;
        if ($i < 0) break;
        $indices[$i]++;
        for ($j = $i + 1; $j < $r; $j++) $indices[$j] = $indices[$j - 1] + 1;
    }
    return $result;
}

// Step 5: Allocate all remaining periods
if (allocatePeriods($timetable, $subjects, 0, $dbconn, $ay, $sem_type, $sec_id)) {
    $_SESSION['timetable'] = $timetable;
    $_SESSION['sec_id'] = $sec_id;
    $_SESSION['ay'] = $ay;
    $_SESSION['sem'] = $sem;
    $_SESSION['sem_type'] = $sem_type;
    echo "<div class='alert alert-success'>All periods allocated successfully. Timetable ready to lock.</div>";
} else {
    echo "<div class='alert alert-danger'>Unable to allocate all subjects. Please adjust manually.</div>";
}
	
    // Step 6: Output timetable
    echo "<table border='1'>
	<tr><td colspan=7>"."sec_id: ".$sec_id ."-".$sec_name."</td></tr>
	<tr><th>Day / Period</th>";
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

    //workload starts
        // === Workload Summary for Each Subject ===
        $subject_workload = [];

        for ($d = 0; $d < $no_of_days; $d++) {
            for ($p = 0; $p < $no_of_periods; $p++) {
                if (isset($timetable[$d][$p]) && is_array($timetable[$d][$p])) {
                                       
                     $code = $timetable[$d][$p]['code'];
                     $staff = $timetable[$d][$p]['staff'];
                    
                    $key = $code . '|' . $staff;
                    $subject_workload[$key] = ($subject_workload[$key] ?? 0) + 1;
                }
            }
        }
		$total_alloted=0;
        echo "<div class='mt-3 alert alert-info'><strong>Subject Workload Summary:</strong><br><ul>";
        foreach ($subject_workload as $key => $count) {
        list($code, $staff) = explode('|', $key);
        echo "<li><strong>$code</strong> (Staff: <strong>$staff</strong>) : $count periods/week</li>";
		$total_alloted += $count;
    }
		echo "<li><strong>Total Alloted: $total_alloted periods/week</li>";
        echo "</ul></div>";

    //workload ends
	if($total_alloted >= 36)
	{
	?>
	<form method="POST">
    <button type="submit" name="lock_btn" class="btn btn-success">Lock Timetable</button>
	</form>
<?php	
	}
	else{
		echo "<p>CHECK SUBJECT STAFF MAPPING</p>";
	}
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
    $ct_no = 1; // Class type or control no (can be used to differentiate multiple schedules)
    $room_no = 'TBD'; // Or fetch based on subject/staff mapping
    $inserted = 0;
    try 
    {
        // START TRANSACTION
        $dbconn->begin_transaction();
        for ($day = 0; $day < count($timetable); $day++) {
            for ($period = 0; $period < count($timetable[$day]); $period++) {
                $entry = $timetable[$day][$period];
                if (!empty($entry) && is_array($entry)) {
                    $staff_idno = $entry['staff'];
                    $subject_code = $entry['code'];
                    $st_hour = $period + 1;
                    $end_hour = $period + 1;
                    $duration = 1;

                    $stmt = $dbconn->prepare("INSERT INTO timetable 
                    ( `staff_idno`, `day_no`, `sec_id`, `sem`, `st_hour`, `end_hour`, `subject_code`, `room_no`, `duration`, `ay`, `locked`, `sem_type`) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $d_no=$day+1;
                    $stmt->bind_param("siiiiissisis",
                        $staff_idno,
                        $d_no,
                        $sec_id,
                        $sem,
                        $st_hour,
                        $end_hour,
                        $subject_code,
                        $room_no,
                        $duration,
                        $ay,
                        $locked,
                        $sem_type
                    );

                    if ($stmt->execute()) {
                        $inserted++;
                    }
                    $stmt->close();
                }
            }
        }
		/* insert second faculty */
		
		/*$second_fac_stmt = $dbconn->prepare("INSERT INTO `timetable`(`staff_idno`, `day_no`, `sec_id`, `sem`, `st_hour`, `end_hour`, `subject_code`, `room_no`, `duration`, `ay`, `locked`, `sem_type`) 
    SELECT 
      `staff_idno`,  `day_no`,  `sec_id`,  ? as `sem`,   `st_period`,  `end_period`,  `subject_code`, `room_no`,  1,  `ay`, 1, `sem_type`
    FROM 
      `predefined_timetable` 
    WHERE 
      ay = ? 
      AND sec_id = ? 
      AND faculty_type = 'SECOND'");
	  $second_fac_message="";
		$second_fac_stmt->bind_param("ssi", $sem, $ay, $sec_id);
		if ($second_fac_stmt->execute()) {
			if($second_fac_stmt->affected_rows > 0)
			{
			$inserted++;
			$second_fac_message="<br>Second faculty Inserted";
			}
		}*/
		
		//START
		$fetch_query = "SELECT ay, sec_id, subject_code, day_no, st_period, end_period, staff_idno, room_no, sem_type
                FROM predefined_timetable 
                WHERE ay = ? AND sec_id = ? AND faculty_type = 'SECOND'";

		// Prepare and bind the fetch statement properly
		$second_fac_stmt = $dbconn->prepare($fetch_query);
		$second_fac_stmt->bind_param("si", $ay, $sec_id);
		$second_fac_stmt->execute();
		$result = $second_fac_stmt->get_result();
		 $second_fac_message ="";
		if ($result && $result->num_rows > 0) {

			// Prepare the insert statement once
			$insert_stmt = $dbconn->prepare("INSERT INTO timetable 
				( staff_idno, day_no, sec_id, sem, st_hour, end_hour,
				 subject_code, room_no, duration, ay, locked, sem_type)
				VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

			// Loop through each record in predefined_timetable
			while ($row = $result->fetch_assoc()) {
				$row_ay = $row['ay'];
				$row_sec_id = $row['sec_id'];
				$subject_code = $row['subject_code'];
				$day_no = $row['day_no'];
				$st_period = $row['st_period'];
				$end_period = $row['end_period'];
				$staff_idno = $row['staff_idno'];
				$room_no = $row['room_no'];
				$sem_type = $row['sem_type'];

				// Set actual sem if available
				$locked = 1;
				$duration = 1;
				 // Assuming auto-increment

				for ($p = $st_period; $p <= $end_period; $p++) {
					$insert_stmt->bind_param(
						"siiiisssisis",
						 $staff_idno, $day_no, $row_sec_id, $sem,
						$p, $p, $subject_code, $room_no,
						$duration, $row_ay, $locked, $sem_type
					);
					$insert_stmt->execute();
				$second_fac_message="<br>Second faculty Inserted";
				}
			}
		}
		//end
		
  
        $dbconn->commit();
        echo "<div class='alert alert-success'>$inserted periods inserted successfully into timetable.$second_fac_message</div>";    
        unset($_SESSION['timetable']);
    }
    catch (mysqli_sql_exception $e) {
        // ROLLBACK on any error
        $dbconn->rollback();
        echo "<div class='alert alert-danger'>Transaction failed: " . $e->getMessage() . "</div>";
    };
}

?>

