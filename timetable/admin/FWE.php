<?php
session_start();
//include("admindbconn.php");
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
    <title>Unutilized Cooked Food</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <style>
        /* Basic page styles */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
            color: #333;
        }

        #filter_date {
            display: block;
            margin: 20px auto;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 80%;
            max-width: 400px;
        }

        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 15px;
            margin: 5px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .deleteBtn {
            background-color: #dc3545;
        }

        .deleteBtn:hover {
            background-color: #c82333;
        }

        /* Basic styles for the popup */
        #editFormPopup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            z-index: 1000;
            max-width: 500px;
            width: 90%;
            box-sizing: border-box;
        }

        #editFormPopup h2 {
            margin-top: 0;
            color: #333;
        }

        #editFormPopup label {
            display: block;
            margin: 10px 0 5px;
            color: #333;
        }

        #editFormPopup input {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        #editFormPopup button {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 10px 15px;
            margin-right: 10px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #editFormPopup #saveChangesBtn {
            background-color: #28a745;
        }

        #editFormPopup #saveChangesBtn:hover {
            background-color: #218838;
        }

        #editFormPopup #cancelEditBtn {
            background-color: #6c757d;
        }

        #editFormPopup #cancelEditBtn:hover {
            background-color: #5a6268;
        }

        /* Overlay to cover the rest of the page */
        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
    </style>
</head>
<body>
    <h1>Unutilized Cooked Food </h1>
    <input type="date" id="filter_date">
    <table id="wastageTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Wastage Date</th>
                <th>Issue Category</th>
                <th>Food Name</th>               
                <th>Item Name</th>
                <th>Unit Price</th>
                <th>Quantity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <!-- Overlay -->
    <div id="overlay"></div>

    <!-- Popup Form -->
    <div id="editFormPopup">
        <h2>Edit Record</h2>
        <form id="updateForm">
            <input type="hidden" id="editId">
            <label for="editWastageDate">Wastage Date:</label>
            <input type="date" id="editWastageDate" required><br>
            <label for="editIssueCategory">Issue Category:</label>
            <input type="text" id="editIssueCategory" required><br>
            <label for="editFoodName">Food Name:</label>
            <input type="text" id="editFoodName" required><br>            
            <label for="editItemName">Item Name:</label>
            <input type="text" id="editItemName" required><br>
            <label for="editUnitPrice">Unit Price:</label>
            <input type="number" id="editUnitPrice" step="0.01" required><br>
            <label for="editQuantity">Quantity:</label>
            <input type="number" id="editQuantity" required><br>
            <button type="button" id="saveChangesBtn">Save Changes</button>
            <button type="button" id="cancelEditBtn">Cancel</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            function loadTable(date) {
                $('#wastageTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: 'ajax_fwe.php',
                        method: 'POST',
                        data: function (d) {
                            d.action = 'fetch';
                            d.wastage_date = date;
                        },
                        dataSrc: function (json) {
                            console.log(json); // Log response for debugging
                            return json;
                        }
                    },
                    columns: [
                        { data: 'id' },
                        { data: 'wastage_date' },
                        { data: 'issue_category' },
                        { data: 'food_name' },                        
                        { data: 'itemname' },
                        { data: 'unit_price' },
                        { data: 'quantity' },
                        {
                            data: null,
                            defaultContent: '<button class="editBtn">Edit</button> <button class="deleteBtn">Delete</button>'
                        }
                    ]
                });
            }

            $('#filter_date').on('change', function() {
                let date = $(this).val();
                $('#wastageTable').DataTable().destroy(); // Destroy previous instance
                loadTable(date); // Reload with new date
            });

            loadTable(''); // Initial load

            // Handle Delete
            $(document).on('click', '.deleteBtn', function() {
                var row = $(this).closest('tr');
                var id = row.find('td').eq(0).text(); // Assuming ID is in the first column

                if (confirm('Are you sure you want to delete this record?')) {
                    $.ajax({
                        url: 'ajax_fwe.php',
                        method: 'POST',
                        data: {
                            action: 'delete',
                            id: id
                        },
                        success: function(response) {
                            var result = JSON.parse(response);
                            if (result.success) {
                                alert('Record deleted successfully');
                                $('#wastageTable').DataTable().ajax.reload(); // Reload the table
                            } else {
                                alert('Error: ' + result.error);
                            }
                        }
                    });
                }
            });

            // Handle Edit
            $(document).on('click', '.editBtn', function() {
                var row = $(this).closest('tr');
                var data = $('#wastageTable').DataTable().row(row).data();

                // Populate form fields with data from the selected row
                $('#editId').val(data.id);
                $('#editWastageDate').val(data.wastage_date);
                $('#editIssueCategory').val(data.issue_category);
                $('#editFoodName').val(data.food_name);               
                $('#editItemName').val(data.itemname);
                $('#editUnitPrice').val(data.unit_price);
                $('#editQuantity').val(data.quantity);

                // Show the edit form and overlay
                $('#editFormPopup').show();
                $('#overlay').show();
            });

            // Handle Save Changes
            $('#saveChangesBtn').on('click', function() {
                var id = $('#editId').val();
                var wastageDate = $('#editWastageDate').val();
                var issueCategory = $('#editIssueCategory').val();
                var foodName = $('#editFoodName').val();
              
                var itemName = $('#editItemName').val();
                var unitPrice = $('#editUnitPrice').val();
                var quantity = $('#editQuantity').val();

                $.ajax({
                    url: 'ajax_fwe.php',
                    method: 'POST',
                    data: {
                        action: 'update',
                        id: id,
                        wastage_date: wastageDate,
                        issue_category: issueCategory,
                        food_name: foodName,                        
                        itemname: itemName,
                        unit_price: unitPrice,
                        quantity: quantity
                    },
                    success: function(response) {
                        var result = JSON.parse(response);
                        if (result.success) {
                            alert('Record updated successfully');
                            $('#wastageTable').DataTable().ajax.reload(); // Reload the table
                            $('#editFormPopup').hide(); // Hide the popup
                            $('#overlay').hide(); // Hide the overlay
                        } else {
                            alert('Error: ' + result.error);
                        }
                    }
                });
            });

            // Handle Cancel Edit
            $('#cancelEditBtn').on('click', function() {
                $('#editFormPopup').hide(); // Hide the popup
                $('#overlay').hide(); // Hide the overlay
            });

            // Handle Overlay Click
            $('#overlay').on('click', function() {
                $('#editFormPopup').hide(); // Hide the popup
                $('#overlay').hide(); // Hide the overlay
            });
        });
    </script>
</body>
</html>
