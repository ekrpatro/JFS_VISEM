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
    <title>New Item Register </title>
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
				<label for="packing_size">Packing Size</label>
                <input type="number" id="packing_size" name="packing_size" ><br>
                <label for="item_category">Item Category:</label>
                <select id="item_category" name="item_category">
                    <option value="">Select Category</option>
                    <option value="grocery">Grocery</option>  
					<option value="veg">Vegetable</option>
					
                    <option value="nonveg">Non-Vegetable</option>
                    <option value="fruits">Fruits</option>
                    <option value="bakery">Bakery</option>
					<option value="milk">Milk Items</option>
					<option value="gas">Gas</option>
					<option value="firewood">FireWood</option>
					<option value='single-use'> Single-Use</option>
					<option value='equipments'> Equipments</option>
					<option value='services_maintenance'> Services  and Maintenance</option>
					<option value='housekeeping'> Housekeeping</option>
					<option value='utensils'> Utensils(Steel Items)</option>
                    
					</select><br>
				<label for="min_stock_quantity">Min_Stock_quantity:</label>
                <input type="text" id="min_stock_quantity" name="min_stock_quantity"  value="0" required><br>
                <label for="measurement_unit">Measurement Unit:</label>
                <select id="measurement_unit" name="measurement_unit" required>
                    <option value="">Select Unit</option>
                    <option value="Kg">Kg</option>
                    <option value="Ltr">Ltr</option>
                    <option value="Btl">Bottle</option>
                    <option value="Pkts">Packets</option>
                    <option value="Nos">Numbers</option>
                </select><br>
                <label for="disp_priority">Display Priority:</label>
                <input type="number" id="disp_priority" name="disp_priority" required><br>
				<label for="gm_id">Item Master Id:</label>
                <input type="number" id="gm_id" name="gm_id" required><br>
				
                <button type="submit">Submit</button>
                <button type="button" class="cancelBtn">Cancel</button>
            </form>
        </div>
    </div>

    <table id="itemTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Item Name</th>
				<th>Brand Name</th>
				<th>Packing Size</th>
                <th>Item Category</th>
				
				<th>Min Stock Quantity</th> <!-- New Column -->
                <th>Measurement Unit</th>
                <th>Display Priority</th>				
				 <th>Item Master Id</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
	<script>
	$(document).ready(function() {
    let itemTable = $('#itemTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ],
        columns: [
            { title: "ID" },
            { title: "Item Name" },
			{ title: "Brand Name" },
			{ title: "Packing Size" },
            { title: "Item Category" },
			
			{ title: "Min Stock" },
            { title: "Measurement Unit" },
            { title: "Display Priority" },
			{ title: "Item Master Id" },
            { title: "Actions" }
        ]
    });

    function fetchItems() {
       // alert("ok");
        $.ajax({
            url: 'ajax_item.php',
            type: 'POST',
            data: { action: 'fetch' },
            success: function(response) {
                try {
                    //alert(response)
                    const items = JSON.parse(response);
                    if (items.error) {
                        console.error("Error fetching items: " + items.error);
                        return;
                    }
                    itemTable.clear();
                    items.forEach(item => {
                        itemTable.row.add([
                            item.id,
                            item.itemname,
							item.brand_name,
							item.packing_size,
                            item.item_category,
							item.min_stock_quantity,							
                            item.measurement_unit,
                            item.disp_priority,
							item.gm_id,
                            `
                                <button class="editBtn" data-id="${item.id}"> Edit</button>
                               <!-- <button class="deleteBtn" data-id="${item.id}">Delete</button> -->
                            `
                        ]).draw(false);
                    });
                } catch (e) {
                    console.error("Error parsing JSON: " + e.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error: " + status + " - " + error);
            }
        });
    }

    fetchItems();
     // Get the modal
     const modal = $("#itemModal");
     const form = $("#itemForm");
 
     // Get the button that opens the modal
     $("#addItemBtn").on("click", function() {
         modal.show(); // Show the modal
         form[0].reset(); // Reset form fields
         form.removeData("id").removeData("action"); // Remove data attributes
     });
      // Get the <span> element that closes the modal
    $(".close").on("click", function() {
        modal.hide(); // Hide the modal
    });
     // Handle the Cancel button
     $(".cancelBtn").on("click", function() {
        modal.hide(); // Hide the modal
        form[0].reset(); // Optionally reset form fields
        form.removeData("id").removeData("action"); // Optionally remove data attributes
    });

    // Close the modal if the user clicks outside the modal content
    $(window).on("click", function(event) {
        if ($(event.target).is("#itemModal")) {
            modal.hide(); // Hide the modal
        }
    });

    $("#itemForm").on("submit", function(e) {
        e.preventDefault();
        const action = $(this).data("action") || 'add';
        const item_id = $(this).data("id");
        const itemData = {
            action: action,
            item_id: item_id,
            itemname: $("#itemname").val(),
			brand_name: $("#brand_name").val(),
			packing_size: $("#packing_size").val(),
            item_category: $("#item_category").val(),
			min_stock_quantity: $("#min_stock_quantity").val(),
            measurement_unit: $("#measurement_unit").val(),
            disp_priority: $("#disp_priority").val(),
			gm_id: $("#gm_id").val()
        };

        $.ajax({
            url: 'ajax_item.php',
            type: 'POST',
            data: itemData,
            success: function(response) {
                try {
                    
                    const result = JSON.parse(response);
					//alert(result);
                    if (result.error) {
                        console.error("Error: " + result.error);
                        return;
                    }
                    alert("Item Inserted..")
                    
                    modal.hide();
                    $("#itemForm")[0].reset();
                    $("#itemForm").removeData("id").removeData("action");
                    fetchItems();
                } catch (e) {
                    console.error("Error parsing JSON: " + e.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error: " + status + " - " + error);
            }
        });
    });

    $(document).on("click", ".deleteBtn", function() {
        const isConfirmed = confirm("Are you sure you want to delete this item?");

        if (isConfirmed)
        {
            const item_id = $(this).data("id");
            $.ajax({
                url: 'ajax_item.php',
                type: 'POST',
                data: { action: 'delete', item_id: item_id },
                success: function(response) {
                    try {
                        const result = JSON.parse(response);
                        if (result.error) {
                            console.error("Error: " + result.error);
                            return;
                        }
                        fetchItems();
                    } catch (e) {
                        console.error("Error parsing JSON: " + e.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX error: " + status + " - " + error);
                }
            });
        }
    });

    $(document).on("click", ".editBtn", function() {
       
        const item_id = $(this).data("id");
        const itemRow = $(this).closest("tr");
        const itemData = itemTable.row(itemRow).data();
        const itemname = itemData[1];
		const brand_name = itemData[2];
		const packing_size = itemData[3];
        const item_category = itemData[4];
		const min_stock_quantity = itemData[5];
        const measurement_unit = itemData[6];
        const disp_priority = itemData[7];
		const gm_id = itemData[8];
        

        $("#itemname").val(itemname);
		$("#brand_name").val(brand_name);
		$("#packing_size").val(packing_size);
        $("#item_category").val(item_category);
		$("#min_stock_quantity").val(min_stock_quantity);
        $("#measurement_unit").val(measurement_unit);
        $("#disp_priority").val(disp_priority);
		$("#gm_id").val(gm_id);
        $("#itemForm").data("id", item_id).data("action", "update");
        modal.show();
    });
});

	</script>

    
</body>
</html>
