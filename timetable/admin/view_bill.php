<?php
if (isset($_GET['bill'])) {
    $billPath = '../uploads/' . basename($_GET['bill']); // Ensure correct path
    if (file_exists($billPath)) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($billPath) . '"');
        readfile($billPath);
        exit;
    } else {
        echo "Error: File not found.";
    }
} else {
    echo "No file specified.";
}
?>
