<?php
include("admindbconn.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['invoice_file'])) 
{
    $targetDir = "../uploads/";
    $invoice_no = $_POST['invoice_no'];
    $purchase_date = $_POST['purchase_date'];

   
	$newFileName = uniqid('invoice_'.$invoice_no."-") . ".pdf";
    $targetFilePath = $targetDir . $newFileName;
	$my_file=$_FILES['invoice_file']['tmp_name'];
   
	$file_size=$_FILES['invoice_file']['size'];
    // Check file size limit (1MB)
    if ($_FILES['invoice_file']['size'] > 1 * 1024 * 1024) {
        echo "<p style='color: red;'>File size must be below 1MB.</p>";
        exit();
    }

    // Move the uploaded file
    if (move_uploaded_file($_FILES['invoice_file']['tmp_name'], $targetFilePath)) {
        // ✅ Update database with uploaded file name
        $sql = "UPDATE purchase_bills SET bill = ? WHERE invoice_no = ? AND purchase_date = ?";
        $stmt = $dbconn->prepare($sql);
        $stmt->bind_param("sss", $newFileName, $invoice_no, $purchase_date);
		$stmt->execute();
        if ($stmt->affected_rows > 0) {
            echo "<p style='color: green;'>Invoice uploaded successfully:  $newFileName</p>";
        } else {
            echo "<p style='color: red;'>No Invoice found....Database updation failed.</p>";
        }

        $stmt->close();
    } 
	else 
	{
        echo "<p style='color: red;'>File upload failed. targetFilePath = ".$targetFilePath." myFile = ".$my_file."File size = ".$file_size." </p>";
    }
} 
else
{
    echo "<p style='color: red;'>No file uploaded.</p>";
}

// Close database connection
$dbconn->close();
?>
