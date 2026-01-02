<!DOCTYPE html>
<?php
include("admindbconn.php");
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Purchase Bills</title>
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
    <div class="container">
        <h1>Edit Purchase Bills </h1>
      <!--  <button class="btn btn-primary" data-toggle="modal" data-target="#addModal">Add New Record</button>-->
        <table id="purchaseTable" class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Purchase Date</th>
                    <th>Seller ID</th>
					 <th>Seller Name</th>
                    <th>Item Category</th>
                    <th>Invoice No</th>
                    <th>Total Amount</th>                    
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
                    <h5 class="modal-title" id="addModalLabel">Add New Record</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addForm">
                        <div class="form-group">
                            <label for="add_purchase_date">Purchase Date</label>
                            <input type="date" class="form-control" id="add_purchase_date" name="purchase_date" required>
                        </div>
                        <div class="form-group">
                            <label for="add_seller_id">Seller ID</label>
                            <input type="text" class="form-control" id="add_seller_id" name="seller_id" required>
                        </div>
                        <div class="form-group">
                            <label for="add_item_category">Item Category</label>
                            <input type="text" class="form-control" id="add_item_category" name="item_category" required>
                        </div>
                        <div class="form-group">
                            <label for="add_invoice_no">Invoice No</label>
                            <input type="text" class="form-control" id="add_invoice_no" name="invoice_no" required>
                        </div>
                        <div class="form-group">
                            <label for="add_total_amount">Total Amount</label>
                            <input type="number" class="form-control" id="add_total_amount" name="total_amount" step="0.01" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Record</button>
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
                    <h5 class="modal-title" id="editModalLabel">Edit Record</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <input type="hidden" id="edit_id" name="id">
                        <div class="form-group">
                            <label for="edit_purchase_date">Purchase Date</label>
							<input type="hidden" class="form-control" id="old_purchase_date" name="old_purchase_date" required>
                            <input type="date" class="form-control" id="edit_purchase_date" name="purchase_date" required>
                        </div>
                        <div class="form-group">
							<input type="hidden" class="form-control" id="old_seller_id" name="old_seller_id" required>
                            <label for="edit_seller_id">Seller ID</label>
                           
							<select class='form-control' id="edit_seller_id" name="seller_id" required>							
							<?php
							$sellerQuery = "SELECT `seller_id`, `shopname` FROM `seller` order by seller_id";
							$sellerResult = mysqli_query($dbconn, $sellerQuery);
							if ($sellerResult) {
								while ($sellRow = mysqli_fetch_array($sellerResult)) { ?>
									<option value="<?php echo $sellRow['seller_id']; ?>"><?php echo $sellRow['shopname']; ?></option>
								<?php }
							} else {
								echo "<option value=''>No items found</option>";
							}
							?>
							</select>
                        </div>
                        <div class="form-group">
                            <label for="edit_item_category">Item Category</label>
                           <select class='form-control' id="edit_item_category" name="item_category" required>
							
							<?php
							$catQuery = "select distinct item_category from purchase_bills";
							$itemResult = mysqli_query($dbconn, $catQuery);

							if ($itemResult) {
								while ($itemRow = mysqli_fetch_array($itemResult)) { ?>
									<option value="<?php echo $itemRow['item_category']; ?>"><?php echo $itemRow['item_category']; ?></option>
								<?php }
							} else {
								echo "<option value=''>No items found</option>";
							}
							?>
							</select>
                        </div>
                        <div class="form-group">
                            <label for="edit_invoice_no">Invoice No</label>
                            <input type="hidden" class="form-control" id="old_invoice_no" name="old_invoice_no" required>
							<input type="text" class="form-control" id="edit_invoice_no" name="invoice_no" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_total_amount">Total Amount</label>
                            <input type="number" class="form-control" id="edit_total_amount" name="total_amount" step="0.01" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Record</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this record?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <!--<button type="button" id="confirmDelete" class="btn btn-danger">Delete</button>-->
                </div>
            </div>
        </div>
    </div>
	<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<!--<h5 class="modal-title" id="detailsModalLabel">Details</h5>-->
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<!-- Content loaded via AJAX -->
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
            var table = $('#purchaseTable').DataTable({
                "ajax": {
                    "url": "ajax_bill_data.php",
                    "type": "POST",
					 "data": function(d) {                        
						d.action='fetch_bills';
                    },
                    "dataSrc": ""
                },
                "columns": [
                    { "data": "id" },
                    { "data": "purchase_date" },
                    { "data": "seller_id" },
					{ "data": "shopname" },
                    { "data": "item_category" },
                    { "data": "invoice_no",
						"render": function(data, type, row) {
                            return `
                          <button class="btn btn-info btn-show"   data-id="${row.id}" data-shopname="${row.shopname}" data-seller_id="${row.seller_id}"  data-purchase_date="${row.purchase_date}"  data-invoice_no="${data}">
								${data}
							</button>
                            `;
                        }

					},
                    { "data": "total_amount" },
                   
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            return `
                                <button class="btn btn-warning btn-edit" data-id="${data.id}">Edit</button>
                                <!--<button class="btn btn-danger btn-delete" data-id="${data.id}">Delete</button>-->
                            `;
                        }
                    }
                ]
            });

            // Handle Add Form Submission
            $('#addForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'ajax_bill_data.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#addModal').modal('hide');
                        table.ajax.reload();
                    }
                });
            });
			$('#purchaseTable').on('click', '.btn-show', function(e) {
				e.preventDefault();
				var itemId = $(this).data('id');
				var invoice_no = $(this).data('invoice_no'); // Get the invoice_no
				var seller_id = $(this).data('seller_id'); // Get the invoice_no
				var purchase_date = $(this).data('purchase_date'); // Get the invoice_no
				var shopname = $(this).data('shopname'); // Get the invoice_no
				$.ajax({
					type: 'POST',
					url: 'ajax_bill_data.php',
					data: { 
							action: 'get_invoice_details', 
							invoice_no: invoice_no,
							seller_id: seller_id,
							purchase_date: purchase_date,
							shop_name: shopname
							},
					
					success: function(response) {
										
						  $('#detailsModal .modal-body').html('<h3>Invoice Details</h3>' + response);
          
						$('#detailsModal').modal('show');
					},
					error: function(jqXHR, textStatus, errorThrown) {
						console.log(textStatus, errorThrown);
						alert("Error: " + textStatus + " " + errorThrown);
					}
				});
			});

            // Handle Edit Button Click
            $('#purchaseTable').on('click', '.btn-edit', function() {			
                
                var id = $(this).data('id');
				var data = table.row($(this).parents('tr')).data();
				$('#edit_id').val(id);
				$('#edit_invoice_no').val(data.invoice_no);
				$('#old_invoice_no').val(data.invoice_no);
				$('#old_seller_id').val(data.seller_id);
				$('#old_purchase_date').val(data.purchase_date);
				$('#edit_purchase_date').val(data.purchase_date);				
				$('#edit_seller_id').val(data.seller_id);				
				$('#edit_item_category').val(data.item_category);				
				$('#edit_item_category').trigger('change');
				
				$('#edit_total_amount').val(data.total_amount);
				$('#editModal').modal('show');
				
            });

            // Handle Edit Form Submission
            $('#editForm').on('submit', function(e) {
                e.preventDefault();
				var id = $('#edit_id').val();
				var invoice_no = $('#edit_invoice_no').val();
				var old_invoice_no = $('#old_invoice_no').val();
				var old_seller_id = $('#old_seller_id').val();
				var old_purchase_date = $('#old_purchase_date').val();
				
				var purchase_date = $('#edit_purchase_date').val();
				var seller_id = $('#edit_seller_id').val();
				$('#seller_id').trigger('change');
				var item_category = $('#edit_item_category').val();
				var total_amount = $('#edit_total_amount').val();			
                $.ajax({
                    url: 'ajax_bill_data.php',
                    type: 'POST',
                    data: {
							id: id,
							invoice_no: invoice_no,
							old_invoice_no: old_invoice_no,
							old_seller_id: old_seller_id,
							old_purchase_date: old_purchase_date,
							purchase_date: purchase_date,
							seller_id: seller_id,
							item_category: item_category,
							total_amount: total_amount,							
							action: 'update_purchase_bills'
							},
						dataType: 'json', // Expecting a JSON response
                    success: function(response) {
							if (response.status === 'success')
							{
								Swal.fire('Success', response.message, 'success'); // Using SweetAlert2 for feedback
								$('#editModal').modal('hide');
								$('#purchaseTable').DataTable().ajax.reload();
								//table.ajax.reload();
							} 
							else 
							{
								Swal.fire('Error', response.message, 'error'); // Using SweetAlert2 for feedback
							}
						
                       
                        
                    }
                });
            });

            // Handle Delete Button Click
            $('#purchaseTable').on('click', '.btn-delete', function() {
                var id = $(this).data('id');
                $('#confirmDelete').data('id', id);
                $('#deleteModal').modal('show');
            });

            // Handle Confirm Delete
            $('#confirmDelete').on('click', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: 'delete_record.php',
                    type: 'POST',
                    data: { id: id },
                    success: function(response) {
                        $('#deleteModal').modal('hide');
                        table.ajax.reload();
                    }
                });
            });
        });
    </script>
</body>
</html>
