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
						
                    </select>
                </div>
				<div class="form-group col-sm-3">
                    <label>W.E.F Date:</label>
					<!--  date('j F, Y') -->
                   <input type="text" name="wef_dt" value="4 August, 2025">
                </div>
				
                <div class="form-group col-sm-3 d-flex align-items-end">
                    <input type='submit' class="btn btn-primary btn-block" name='sec_ok' value="show">
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
        
    });
</script>
</body>
</html>
<?php
$days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
$no_of_days = 6;
$no_of_periods = 6;


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
		

		$query = "SELECT `id`, `ay`, `sec_id` FROM `pat_sec_id` WHERE sec_id = ?";
		$pstmt = $dbconn->prepare($query);

		if ($pstmt) {
			$pstmt->bind_param("i", $sec_id); // 'i' for integer, use 's' for string
			$pstmt->execute();
			$result = $pstmt->get_result();

			if ($result && $result->num_rows > 0) {
				$pat_class_found= 1;
			}

		$pstmt->close();
		}
		
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
        MAX(t1.sem) AS sem
        FROM 
        class_timetable t1 
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
			

			if (mysqli_num_rows($result) > 0)
			{
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

    // Step 2: Load predefined lab periods
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
?>

