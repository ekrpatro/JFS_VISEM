<?php
session_start();
include("../html/dbconn.php");
$ay = "2025-26";
$odd_even = 0;

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Time Table</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    
    <style>
        table {
            width: 100%;
            margin-top: 20px;
        }

        table td, table th {
            padding: 8px;
            text-align: center;
        }

        input[type="text"] {
            width: 100%;
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
                    <select name='sec_data' class="form-control select2 select2-purple" data-dropdown-css-class="select2-purple">
                        <?php
                        $secq = "SELECT * FROM sec where possision='active'  ORDER BY course_id, dept_id, sem, section";
                        $sec_rows = $dbconn->query($secq);
                        foreach ($sec_rows as $sec_row) {
                            echo "<option value='" . $sec_row['sec_id'] . ":" . $sec_row['sec_name'] . ":" . $sec_row['dept_id'] . ":" . $sec_row['sem'] . "'>" . $sec_row['sec_name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group col-sm-3">
                    <label>Academic Year</label>
                    <select name='ay' class="form-control select2 select2-purple" data-dropdown-css-class="select2-purple">
                        <option value='<?=$ay?>'><?=$ay?></option>
                    </select>
                </div>

                <div class="form-group col-sm-3 d-flex align-items-end">
                    <input type='submit' class="btn btn-primary btn-block" name='sec_ok' value="OK">
                </div>
            </div>
        </form>
    </div>
</div>

<div id="activity_container" class="mt-4"></div>

<?php


if (isset($_POST['sec_ok'])) {
    $day_names = array(1 => "Monday", 2 => "Tuesday", 3 => "Wednesday", 4 => "Thursday", 5 => "Friday", 6 => "Saturday");
    $ay = $_POST['ay'];
    
    $sec_arr = explode(":", $_POST['sec_data']);
    $sec_id = $sec_arr[0];
    $sec_name = $sec_arr[1];
    $dept_id = $sec_arr[2];
    $sem = $sec_arr[3];
	
	
	
?>
    <div id='inner2' align=center>
        <form method='post' action="">
            <input type="hidden" name='sec_id' value="<?= $sec_id; ?>">
            <input type="hidden" name='dept_id' value="<?= $dept_id; ?>">
            <input type="hidden" name='sem' value="<?= $sem; ?>">
            <input type="hidden" name='ay' value="<?= $ay; ?>">
           
            <table class="table table-bordered table-sm">
                <tr class="bg-light">
                    <th colspan="7"><?= $sec_name; ?> Class Time Table for AY <span class="text-danger"><?= $ay ?></span></th>
                </tr>
                <tr class="table-secondary">
                    <th>DAY / PERIOD</th>
                    <th>I</th>
                    <th>II</th>
                    <th>III</th>
                    <th>IV</th>
                    <th>V</th>
                    <th>VI</th>
					
                </tr>
                <?php
				
				
                for ($dno = 1; $dno < 7; $dno++) 
				{
                    echo "<tr><th>{$day_names[$dno]}</th>";
                    for ($hr = 1; $hr < 7; $hr++) 
					{					
						 $ttq = "SELECT c.*, s.sub_type,s.sub_short_code 
								FROM class_timetable c 
								LEFT JOIN subjects s ON c.subject_code = s.subject_code 
								WHERE c.sec_id = $sec_id AND c.ay = '$ay' AND c.day_no = $dno AND c.st_hour = $hr";

						$tt_result = $dbconn->query($ttq);
						$tt_row = $tt_result->fetch_assoc();
                        
						$ct_no = $tt_row['ct_no'] ?? 0;
						$locked = $tt_row['locked'] ?? 0;
						$sub_code = $tt_row['subject_code'] ?? "SUBCODE";
						$staff_idno = $tt_row['staff_idno'] ?? "EMPID";
						$room_no = $tt_row['room_no'] ?? "ROOMNO";
						$sub_type = $tt_row['sub_type'] ?? "";
						$sub_short_code = $tt_row['sub_short_code'] ?? "sub_code";

						$is_placeholder = ($sub_code === "SUBCODE");
						 // Highlight pink if sub_type is 'L'
						$bg_style = "";
						if ($is_placeholder) {
							$bg_style = "background:black; color:lime;";
						} elseif (strtoupper($sub_type) === 'L') {
							$bg_style = "background:pink;";
						}
						$readonly = $locked == 1 ? "readonly" : "";

                      
						echo "<td>";
						echo "<input type='hidden' name='ct_nos[]' value='$ct_no'>";
						echo "<input type='text' name='subject_code[]' title='$sub_short_code' class='form-control mb-1' value='$sub_code' $readonly style='$bg_style'>";
						echo "<input type='text' name='staff_idno[]' title='Staff ID' class='form-control mb-1' value='$staff_idno' $readonly style='$bg_style'>";
						echo "<input type='text' name='room_no[]' title='Room No' class='form-control'  $readonly value='$room_no'>";
						echo "</td>";
                    }
                    echo "</tr>";
                }
                ?>
                <tr>
                    <td colspan="7" class="text-center">
                        <input type='submit' class="btn btn-success" name='tt_ok' value='Save Time Table'>
                    </td>
                </tr>
            </table>
        </form>
		
    </div>
	<div id='inner3'>
		<?php
			 $ttq = "SELECT t1.`id`, t1.`ay`, t1.`sec_id`, t1.`subject_code`, t1.`staff_idno`, t1.`teaching_periods`, t2.staff_name,t3.subject_name FROM `subject_staff_mapping` t1 left join staff t2 on t1.staff_idno=t2.staff_idno left join subjects t3 on t3.subject_code=t1.subject_code where t1.ay='$ay'  and t1.sec_id=$sec_id
				ORDER BY `t1`.`sec_id` ASC;";

			$fs_map = $dbconn->query($ttq);
			$fs_row = $fs_map->fetch_assoc();
			
			//fill the html table  from fs_row
			if ($fs_map->num_rows > 0) 
			{
				echo "<table class='table table-bordered table-sm mt-4'>";
				echo "<thead class='thead-light'>
						<tr>
							<th colspan=5 style='text-align:center'>Faculty Subject Mapping</th>
							
						</tr>
						<tr>
							<th>Subject Code</th>
							<th>Subject Name</th>
							<th>Staff ID</th>
							<th>Staff Name</th>
							<th>Periods</th>
						</tr>
					  </thead><tbody>";

				while ($fs_row = $fs_map->fetch_assoc()) 
				{
					echo "<tr>
							<td>{$fs_row['subject_code']}</td>
							<td style='text-align:left'>{$fs_row['subject_name']}</td>
							<td>{$fs_row['staff_idno']}</td>
							<td style='text-align:left'>{$fs_row['staff_name']}</td>
							<td>{$fs_row['teaching_periods']}</td>
						  </tr>";
				}

				echo "</tbody></table>";
			} 
			else 
			{
				echo "<p class='text-muted'>No staff-subject mapping found for the selected section.</p>";
			}

		?>
	</div>
<?php
}
?>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- Select2 JS -->
<script src="plugins/select2/js/select2.full.min.js"></script>
<script>
    $(document).ready(function () {
        $('.select2').select2({
            theme: 'bootstrap4'
        });

        $(".activity1").click(function () {
            var url = "pages/staff/TimeTable/activity_test.php?secid=" + $(this).data("sec") +
                      "&period=" + $(this).data("period") +
                      "&subject_id=" + $(this).data("sub") +
                      "&staff_id=" + $(this).data("staff") +
                      "&course_id=" + $(this).data("course") +
                      "&sem=" + $(this).data("sem");

            $.ajax({
                type: 'GET',
                url: url,
                cache: false,
                success: function (data) {
                    $("#activity_container").html(data);
                }
            });
        });
    });
</script>
</body>
</html>
