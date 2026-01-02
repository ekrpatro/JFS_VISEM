<?php
include("../html/dbconn.php");

if (isset($_POST["submit"])) {
    if ($_FILES["csv_file"]["error"] == 0) {
        $filename = $_FILES["csv_file"]["tmp_name"];
        $file = fopen($filename, "r");

        // Skip the header row
        fgetcsv($file);

        $inserted = 0;
        //INSERT INTO `staff_short_names`( `id`, `staff_idno`, `staff_name`, `short_name`) 
		
        while (($row = fgetcsv($file)) !== false) {
            $row = array_map('trim', $row);
            
            list($id,$staff_idno, $staff_name,  $short_name) = $row;

            $stmt = $dbconn->prepare("INSERT INTO `staff_short_names`(`staff_idno`, `staff_name`, `short_name`) values( ?, ?, ?)");
            $stmt->bind_param("sss", $staff_idno, $staff_name, $short_name);

            if ($stmt->execute()) {
                $inserted++;
            }
        }

        fclose($file);
        echo "<div class='alert alert-success'>Successfully inserted $inserted records.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error uploading file.</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Staff Short Names CSV</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h2>Upload CSV to Insert staff_short_names</h2>
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>Select CSV File:</label>
            <input type="file" name="csv_file" accept=".csv" class="form-control" required>
        </div>
        <button type="submit" name="submit" class="btn btn-primary">Upload and Insert</button>
    </form>
</body>
</html>
