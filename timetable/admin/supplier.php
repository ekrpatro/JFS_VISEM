
<?php
session_start();

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
    <title>Seller Info</title>
	 <link type="text/css" rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <style>
        .container {
            margin-top: 20px;
        }
        .modal-content {
            padding: 20px;
        }
    </style>
</head>
<body>
	<?= include('./menu.php') ?>
    <div class="container">
        <h1>Seller Info</h1>
        <button class="btn btn-primary" data-toggle="modal" data-target="#addModal">Add New Record</button>
        <table id="sellerTable" class="table table-striped">
            <thead>
                <tr>
                    <th>Seller Id</th>
                    <th>Shop Name</th>
                    <th>Contact</th>
                    <th>Address</th>                                       
					<th>ItemCategory</th> 
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Add New Seller</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addForm">
                        <div class="form-group">
                            <label for="add_shopname">Shop Name</label>
                            <input type="text" class="form-control" id="add_shopname" name="shopname" required>
                        </div>
                        <div class="form-group">
                            <label for="add_contact">Contact</label>
                            <input type="text" class="form-control" id="add_contact" name="contact" required>
                        </div>
                        <div class="form-group">
                            <label for="add_address">Address</label>
                            <input type="text" class="form-control" id="add_address" name="address" required>
                        </div>
						<div class="form-group">
                            <label for="add_item_category">Item Category</label>
                           <!-- <input type="text" class="form-control" id="edit_item_category" name="edit_item_category" required>-->
							<select class='form-control' id="add_item_category" name="add_item_category" >
								<option value='Grocery'> Grocery</option>
								<option value='Vegetables'> Vegetables</option>
								<option value='NonVeg'> NonVeg</option>
								<option value='Milk'> Milk</option>
								<option value='Gas'> Gas</option>
								<option value='FireWood'> FireWood</option>
								<option value='Housekeeping'> Housekeeping</option>
								<option value='Equipments'> Equipments (Machinery)</option>
								<option value='Services_Maintenance'> Services and Maintenance</option>
								<option value='Utensils'> Utensils</option>
							</select>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Seller</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Seller</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <input type="hidden" id="edit_seller_id" name="seller_id">                      
                        <div class="form-group">
                            <label for="edit_shopname">Shop Name</label>
                            <input type="text" class="form-control" id="edit_shopname" name="shopname" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_contact">Contact</label>
                            <input type="text" class="form-control" id="edit_contact" name="contact" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_address">Address</label>
                            <input type="text" class="form-control" id="edit_address" name="address" required>
                        </div>
						<div class="form-group">
                            <label for="edit_item_category">Item Category</label>
                           <!-- <input type="text" class="form-control" id="edit_item_category" name="edit_item_category" required>-->
							<select class='form-control' id="edit_item_category" name="item_category"  >
								<option value='Grocery'> Grocery</option>
								<option value='Vegetables'> Vegetables</option>
								<option value='NonVeg'> NonVeg</option>
								<option value='Milk'> Milk</option>
								<option value='Gas'> Gas</option>
								<option value='FireWood'> FireWood</option>
								<option value='Housekeeping'> Housekeeping</option>
								<option value='Equipments'> Equipments (Machinery)</option>
								<option value='Services_Maintenance'> Services and Maintenance</option>
								<option value='Utensils'> Utensils</option>
							</select>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            var table = $('#sellerTable').DataTable({
                "ajax": {
                    "url": "ajax_fetch_sellers.php",
                    "type": "POST",
                    "data": function(d) {
                        d.action = 'fetch_seller';
                    },
                    "dataSrc": "data"
                },
                "columns": [
                    { "data": "seller_id" },
                    { "data": "shopname" },
                    { "data": "contact" },
                    { "data": "address" },                   
					{ "data": "item_category" }, 
                    {
                        "data": null,
                        "render": function(data) {
                            return `
                                <button class="btn btn-warning btn-edit" data-id="${data.seller_id}">Edit</button>
                            `;
                        }
                    }
                ]
            });

            // Handle Add Form Submission
            $('#addForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'ajax_fetch_sellers.php',
                    type: 'POST',
                    data: $(this).serialize() + '&action=add_seller',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('Success', response.message, 'success');
                            $('#addModal').modal('hide');
                            table.ajax.reload();
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    dataType: 'json'
                });
            });

            // Handle Edit Button Click
            $('#sellerTable').on('click', '.btn-edit', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: 'ajax_fetch_sellers.php',
                    type: 'POST',
                    data: { action: 'get_seller', seller_id: id },
                    success: function(response) {
                        var data = response.data;
                        $('#edit_seller_id').val(data.seller_id);
                        $('#edit_shopname').val(data.shopname);
                        $('#edit_contact').val(data.contact);
                        $('#edit_address').val(data.address);
						$('#edit_item_category').val(data.item_category);
                        $('#editModal').modal('show');
                    },
                    dataType: 'json'
                });
            });

            // Handle Edit Form Submission
            $('#editForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'ajax_fetch_sellers.php',
                    type: 'POST',
                    data: $(this).serialize() + '&action=update_seller',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('Success', response.message, 'success');
                            $('#editModal').modal('hide');
                            table.ajax.reload();
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    dataType: 'json'
                });
            });
        });
    </script>
</body>
</html>
