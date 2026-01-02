<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Item Management</title>
    <link type="text/css" rel="stylesheet" href="../css/menu.css">
	  
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.4/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        h2 {
            margin-bottom: 20px;
        }
        #itemForm {
            max-width: 500px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        #itemForm label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        #itemForm input[type="text"], 
        #itemForm input[type="number"],
        #itemForm select {
            width: 100%;
            padding: 8px;
            margin-bottom: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        #itemForm button {
            padding: 10px 15px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        #itemForm button:hover {
            background-color: #0056b3;
        }
        #addItemBtn {
            padding: 10px 15px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-bottom: 20px;
        }
		.editBtn{
			padding: 10px 15px;
            background-color: #28a745;
            color: #00f;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-bottom: 20px;
		}
        #addItemBtn:hover {
            background-color: #218838;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        /* Modal styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto; /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Could be more or less, depending on screen size */
            max-width: 600px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
<?= include('./menu.php') ?>
<br>
    <h2>Item Register</h2>
    <button id="addItemBtn">Add Item</button>
    
    <!-- Modal Structure -->
    <div id="itemModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form id="itemForm">
                <label for="itemname">Item Name:</label>
                <input type="text" id="itemname" name="itemname" required><br>
				<label for="brand_name">Brand Name:</label>
                <input type="text" id="brand_name" name="brand_name" ><br>
                <label for="item_category">Item Category:</label>
                <select id="item_category" name="item_category">
                    <option value="">Select Category</option>
                    <option value="grocery">Grocery</option>  
					<option value="veg">Vegetable</option>					
                    <option value="nonveg">Non-Vegetable</option>
                    <option value="fruits">Fruits</option>
                    <option value="bakery">Bakery</option>
					<option value="milk">Milk Items</option>
					<option value="single-use">Single-Use/Use and Throw</option>
					<option value="gas">Gas</option>                    
					</select><br>
                <label for="measurement_unit">Measurement Unit:</label>
                <select id="measurement_unit" name="measurement_unit" required>
                    <option value="">Select Unit</option>
                    <option value="Kg">Kg</option>
                    <option value="Ltr">Ltr</option>
                    <option value="Btl">Bottle</option>
                    <option value="Pkts">Packets</option>
                    <option value="Nos">Numbers</option>
                </select><br>
				<label for="min_stock_quantity">Minimum Stock Quantity</label>
                <input type="number" id="min_stock_quantity" name="min_stock_quantity" required><br>
                <label for="disp_priority">Display Priority:</label>
                <input type="number" id="disp_priority" name="disp_priority" required><br>
                <button type="submit">Submit</button>
                <button type="button" class="cancelBtn">Cancel</button>
            </form>
        </div>
    </div>

    <table id="itemTable" class="display">
        <thead>
            <tr>
                <th>ID</th>
                <th>Item Name</th>
				<th>Brand Name</th>
                <th>Item Category</th>
				<th>Minimum Stock Quantity</th>
                <th>Measurement Unit</th>                
				<th>Display Priority</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <script src="item_app.js"></script>
</body>
</html>
