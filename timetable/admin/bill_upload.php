<?php
include("admindbconn.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['invoice_file'])) 
{
    // ✅ Corrected absolute path for Linux (Bitnami Apache)
    $targetDir = __DIR__ . "/../uploads/";  

    
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $invoice_no = trim($_POST['invoice_no']);
    $purchase_date = trim($_POST['purchase_date']);

    
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $purchase_date)) {
        echo "<p style='color: red;'>Invalid purchase date format.</p>";
        exit();
    }
    
    
    $allowedTypes = ['application/pdf'];
    $fileMimeType = mime_content_type($_FILES['invoice_file']['tmp_name']);
    if (!in_array($fileMimeType, $allowedTypes)) {
        echo "<p style='color: red;'>Only PDF files are allowed.</p>";
        exit();
    }

    
    if ($_FILES['invoice_file']['size'] > 1 * 1024 * 1024) {
        echo "<p style='color: red;'>File size must be below 1MB.</p>";
        exit();
    }

    
    $newFileName = uniqid('invoice_' . $invoice_no . "-") . ".pdf";
    $targetFilePath = realpath($targetDir) . "/" . $newFileName; 

    
    if (move_uploaded_file($_FILES['invoice_file']['tmp_name'], $targetFilePath)) {
        chmod($targetFilePath, 0644); 

       
        echo "<p style='color: green;'>File uploaded successfully! Full Path: <strong>$targetFilePath</strong></p>";

        
        $sql = "UPDATE purchase_bills SET bill = ? WHERE invoice_no = ? AND purchase_date = ?";
        $stmt = $dbconn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("sss", $newFileName, $invoice_no, $purchase_date);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo "<p style='color: green;'>Invoice updated in database successfully.</p>";
            } else {
                echo "<p style='color: red;'>No matching invoice found. Database update failed.</p>";
            }

            $stmt->close();
        } else {
            echo "<p style='color: red;'>Database error: Unable to prepare statement.</p>";
        }
    } 
    else 
    {
        echo "<p style='color: red;'>Please try again - File upload failed. Path attempted: <strong>$targetFilePath</strong></p>";
    }
} 
else
{
    echo "<p style='color: red;'>No file uploaded.</p>";
}

if (isset($dbconn)) {
    $dbconn->close();
}
?>
