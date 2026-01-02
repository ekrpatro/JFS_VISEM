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
    <title>Purchase Register</title>
 
	<link type="text/css" rel="stylesheet" href="../css/menu.css">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

	<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.1.2/css/buttons.dataTables.min.css">

   

	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
		<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
		<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
		<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
	<!-- added -->
		<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
		<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- ends -->
	<style>
        /* Your existing styles */
        /* Adjust the height for the table */
        .purchase_register__form table {
            width: 100%;
            margin-top: 1.2rem;
            table-layout: auto;
            border-spacing: 1rem;
            border-collapse: collapse;
            background-color: #FFFFFF;
            border: 1px solid #232323;
        }
   
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            width: 100%;
            overflow-x: hidden;
            background-size: cover;
        }

        #stock_table{
            margin: 10px;
            border:solid 2px black;
            width:50%;

        }


        .right-align {
        text-align: right;
    }
    .center-align {
        text-align: center;
    }
    .highlight {
        color: #ff0000; /* red color */
    }
.centered-table {
            padding: 30px;
            border: 2px solid #ccc;
            border-radius: 5px;
        }
.centered-table table {
    margin: auto; /* Ensure the table is centered horizontally */
}
        .dataTables_filter {
            text-align: right;
            margin-right: 40px;
        }
        .dataTables_wrapper {
            position: relative;
        }
       
        .table-container {
            position: relative;
        }

</style>
</head>
<body>
    <!-- Header and Navigation Bar -->
    <?= include('./menu.php') ?>
    <br>
    <h1 style="text-align: center;">Purchase Register</h1>
    <div class="centered-table">
        <table id="pr_id">
            <tr>
                <td><label for="purchase_start_date">Start Date:</label></td>
				<td><label for="purchase_end_date">End Date:</label></td>
                <td><label for="invoiceno">Invoice No:</label></td>
                <td colspan=2></td>
            </tr>
            <tr>
				<td><input type="date" class="form-control" id="purchase_start_date" name="purchase_start_date" value='2024-07-07'></td>
                <td><input type="date" class="form-control" id="purchase_end_date" name="purchase_end_date" value='<?=date("Y-m-d"); ?>'></td>
                <td><input type="text" class="form-control" id="invoiceno" name="invoiceno"></td>
                <td><button type="button" id="gen_btn" class="btn btn-primary">Report</button></td>
				<td style='padding-left:10px'><button type="button" id="procure_btn" class="btn btn-primary">Proqurement</button></td>
				<td style='padding-left:10px'><button type="button" id="consolidate_btn" class="btn btn-primary">Consolidate Proqurement Cost</button></td>
				<td style='padding-left:10px'><button type="button" id="uploadbil_btn" class="btn btn-primary">Upload Bill</button></td> <!--invoice_bill.php-->
          
			</tr>
        </table>
    </div>
    <section class="purchase_register__form">
        <table id="purchaseTable" style="width: 1050px;" class="display">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Purchase Date</th>
                    <th>Invoice Number</th>
					<th>Seller id</th>
                    <th>ShopName</th>
                    <th>Item id</th>
                    <th>Item name</th>
					<th>Brand name</th>
                    <th>Price/unit</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
					<th>Edit</th>
					<!--<th>Delete</th>-->
                </tr>
            </thead>
            <tbody></tbody>
        </table>
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
							<input type="text" id="edit_id" name="id">

							<div class="form-group">
								<label for="edit_invoice_no">Invoice Number</label>
								<input type="text" class="form-control" id="edit_invoice_no" name="invoice_no" readonly>
							</div>
							<div class="form-group">
								<label for="edit_purchase_date">Purchase Date</label>
								<input type="date" class="form-control" id="edit_purchase_date" name="purchase_date" readonly >
							</div>

							<div class="form-group">
								<label for="edit_quantity">Quantity</label>
									<input type="hidden"  id="old_quantity" name="old_quantity">
									<input type="number" class="form-control" id="edit_quantity" name="quantity">
								</div>
								<div class="form-group">
									<label for="edit_rate">Rate</label>
									<input type="hidden"  class="form-control" id="old_rate" name="old_rate">
									<input type="number" step="0.01" class="form-control" id="edit_rate" name="rate">
								</div>
								<div class="form-group">
									<label for="edit_seller_id">Seller Id.</label>
									<input type="text" class="form-control" id="edit_seller_id" name="edit_seller_id" readonly>
								</div>

								<div class="form-group">
									<label for="edit_itemid">Item ID</label>
									<input type="text" class="form-control" id="edit_itemid" name="edit_itemid" readonly>
							</div>

							<button type="submit" class="btn btn-primary">Save Changes</button>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>

    </section>
    <script>
        $(document).ready(function() {
            // Initialize DataTables
            var table = $('#purchaseTable').DataTable({
                "ajax": {
                    "url": "ajax_purchase_data.php",
                    "type": "POST",
                    "data": function(d) {
                        d.invoiceno = $('#invoiceno').val();
                        d.purchase_start_date = $('#purchase_start_date').val();
						d.purchase_end_date = $('#purchase_end_date').val();
						d.action='fetch_purchase';
                    },
                    "dataSrc": ""
                },
                "columns": [
                    { "data": "sno" },
                    { "data": "purchase_date" },
                    { "data": "invoice_no" },
					{ "data": "seller_id" },
					{ "data": "shopname" },
					{ "data": "itemid" },
					{ "data": "itemname" },
					{ "data": "brand_name" },
					{ "data": "unit_price" },
					{ "data": "quantity" },
					{ "data": "total_price", "className": "right-align" },
					{
						"data": "id",
						"render": function(data, type, row) {
							if (row.id=='') {
								return ''; // No buttons for the grand total row
							} else {
							return `
								<button class='btn btn-warning btn-edit' data-id='${data}'>Edit</button>
							`;
								}
							}
					}
					

                ],
               
				"dom": '<"top"fB>rt<"bottom"lp><"clear">',
                "buttons": [{
                    extend: 'excelHtml5',
                    className: 'btn btn-primary',
                    text: '<i class="fa fa-file-excel">download</i>',
                    titleAttr: 'Export to Excel',
                    title: function() { return 'Purchase between '+$('#purchase_start_date').val()+ ' and ' + $('#purchase_end_date').val(); }
                }],
                "error": function(xhr, error, thrown) {
                    console.log('Error fetching data:', error);
                }
            });
			// Handle edit button click
			$('#purchaseTable').on('click', '.btn-edit', function() {
				var id = $(this).data('id');
				var data = table.row($(this).parents('tr')).data();
				$('#edit_id').val(id);
				$('#edit_invoice_no').val(data.invoice_no);
				$('#edit_purchase_date').val(data.purchase_date);
				$('#edit_itemid').val(data.itemid);
				$('#edit_seller_id').val(data.seller_id);
				$('#edit_rate').val(data.unit_price);
				$('#edit_quantity').val(data.quantity);
				$('#old_rate').val(data.unit_price);
				$('#old_quantity').val(data.quantity);
				$('#editModal').modal('show');
			});
			

			// Handle form submission
			$('#editForm').on('submit', function(e) {

				e.preventDefault();
				var id = $('#edit_id').val();
				var invoice_no = $('#edit_invoice_no').val();
				var purchase_date = $('#edit_purchase_date').val();
				var seller_id = $('#edit_seller_id').val();

				var itemid = $('#edit_itemid').val();
				var rate = $('#edit_rate').val();

				var quantity = $('#edit_quantity').val();
				var old_rate = $('#old_rate').val();
				var old_quantity = $('#old_quantity').val();
				//alert("old Quantity "+old_quantity+ " old_rate = "+old_rate);

				$.ajax({
					url: 'ajax_purchase_data.php',
					type: 'POST',
					data: {
							id: id,
							invoice_no: invoice_no,
							purchase_date: purchase_date,
							seller_id: seller_id,
							itemid: itemid,
							rate: rate,
							quantity: quantity,
							old_rate: old_rate,
							old_quantity: old_quantity,
							action: 'update_purchase'
							},
						dataType: 'json', // Expecting a JSON response
						success: function(response) 
						{
							if (response.status === 'success')
							{
								Swal.fire('Success', response.message, 'success'); // Using SweetAlert2 for feedback
								$('#editModal').modal('hide');
								$('#purchaseTable').DataTable().ajax.reload();
							} 
							else 
							{
								Swal.fire('Error', response.message, 'error'); // Using SweetAlert2 for feedback
							}
						},
					error: function(xhr, status, error) {
							console.error('Error updating record:', error);
							Swal.fire('Error', 'An unexpected error occurred.', 'error');
						}
					});
			});

            // Handle button click to generate report
            $('#gen_btn').on('click', function(e) {
                e.preventDefault();
                table.ajax.reload();
            });
			$('#procure_btn').click(function() 
			{
                var startDate = encodeURIComponent($('#purchase_start_date').val());
                var endDate = encodeURIComponent($('#purchase_end_date').val());
                var invoiceNo = encodeURIComponent($('#invoiceno').val());

                var url = `proqurement_report.php?startdate=${startDate}&enddate=${endDate}&invoiceno=${invoiceNo}`;
               
                // Redirect to the new URL
                window.location.href = url;
            });
			$('#consolidate_btn').click(function() 
			{
                var startDate = encodeURIComponent($('#purchase_start_date').val());
                var endDate = encodeURIComponent($('#purchase_end_date').val());
                var invoiceNo = encodeURIComponent($('#invoiceno').val());

                var url = `consolidate_proqurement_report.php?startdate=${startDate}&enddate=${endDate}&invoiceno=${invoiceNo}`;
               
                // Redirect to the new URL
                window.location.href = url;
            });
			$('#uploadbil_btn').click(function() 
			{
                var startDate = encodeURIComponent($('#purchase_start_date').val());
                var endDate = encodeURIComponent($('#purchase_end_date').val());
                var invoiceNo = encodeURIComponent($('#invoiceno').val());

                var url = `invoice_bill.php?startdate=${startDate}&enddate=${endDate}&invoiceno=${invoiceNo}`;
               
                // Redirect to the new URL
                window.location.href = url;
            });
        });
    </script>
</body>
</html>