
<!DOCTYPE html>
<html>
<head>
    
</head>
<body class="container mt-4">
<div class="card border-success">
    
    <div class="card-body">
        <form name='time_frm' method="POST" action="">
            <div class="row">
                <div class="form-group col-sm-3">
                    <label>Select Regulation</label>
                    <select name='regulation' class="form-control select2 select2-purple" data-dropdown-css-class="select2-purple">
                        <option value="BT23">BT23</option>
                        <option value="UG20">UG20</option>
                    </select>
                </div>
                <div class="form-group col-sm-3">
                    <label>Branch and Section</label>
                    <select name='sec_data' class="form-control select2 select2-purple" data-dropdown-css-class="select2-purple">
                        <?php
                        $secq = "SELECT * FROM sec WHERE possision='active' and dept_id=6 ORDER BY course_id, dept_id, sem, section";
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

</body>
</html>
<?php
include("timetable_allocator.php"); 
//  the logic for auto-filling timetable.
?>
