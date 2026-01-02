<?php
session_start();
include("admindbconn.php");
// Session validation
if (!isset($_SESSION['user_name']) || $_SESSION["role"] != 'ADMIN') {
    echo "Invalid User";
    exit(0);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Purchase Bills</title>
    
    <link type="text/css" rel="stylesheet" href="../css/menu.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#billForm').submit(function(event) {
            event.preventDefault();
            
            let formData = new FormData(this); // ✅ Collect all form data including the file
            
            $.ajax({
                url: 'bill_upload.php',
                type: 'POST',
                data: formData,
                contentType: false, // ✅ Required for file upload
                processData: false, // ✅ Prevent jQuery from converting FormData to a query string
                success: function(response) {
                    $('#report').html(response);
                },
                error: function() {
                    $('#report').html('<p style="color: red;">An error occurred while uploading.</p>');
                }
            });
        });
    });
    </script>
    <style>    
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        h1 {
            text-align: center;
            background-color: #4CAF50;
            color: white;
            padding: 20px 0;
            margin: 0;
        }
        #billForm {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        #billForm label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        #billForm input[type="date"],
        #billForm input[type="text"],
        #billForm input[type="file"] {
            width: calc(100% - 22px);
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 16px;
        }
        #billForm button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        #billForm button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <?= include('./menu.php') ?>
    <h1>Upload Invoice Bill</h1>
    <form id="billForm" enctype="multipart/form-data">  
        <label for="purchase_date">Purchase Date:</label>
        <input type="date" id="purchase_date" name="purchase_date"  required>
        
        <label for="invoice_no">Invoice Number:</label>
        <input type="text" id="invoice_no" name="invoice_no" value="" required>
        
        <label for="invoice_file">Invoice File (.pdf only):</label>
        <input type="file" id="invoice_file" name="invoice_file" accept=".pdf" required>
        
        <button type="submit">Upload</button>
    </form>

    <div id="report"></div> <!-- ✅ Display upload response -->
</body>
</html>
