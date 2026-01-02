
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
    <title>IssueRegister</title>
	<!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    
    <!-- Font Awesome CSS -->
   <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">-->
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    
    <!-- Custom CSS -->
    <link type="text/css" rel="stylesheet" href="../css/menu.css">
  
   
	 
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
		 td {
			# border:solid 4px rgb(60, 179, 113);
			padding: 5px;
		}
        body {
            width: 100%;
            overflow-x: hidden;
            background-size: cover;
        }
		.right-align {
            text-align: right;
        }

        .center-align {
            text-align: center;
        }
		#output {
			
           
			padding: 20px;
			border-radius: 4px;
			margin: 0 auto; /* Horizontally center */
			max-width: 800px; /* Limit the width of the div */
			position: relative; /* Enable positioning context */
			text-align: center; /* Center text inside the div */
           
        }
		#output table {
			margin: 0 auto; /* Center the table within the div */
			width: auto; /* Allow table to take up necessary width */
			border-collapse: collapse; /* Optional: makes table borders appear as single lines */
		}

		#output th, #output td {
			padding: 10px; /* Space around table cells */
			border: 1px solid #dee2e6; /* Light border color */
		}

		#output thead {
			background-color: #f8f9fa; /* Light background for header */
		}
		  
		 .btn-excel {
        background-color: #ffc107; /* Yellow color */
        color: #fff; /* White text */
        border-color: #ffc107; /* Matching border color */
        transition: background-color 0.3s ease; /* Smooth transition for background color */
    }

    .btn-excel:hover {
        background-color: #dc3545; /* Red color on hover */
        border-color: #dc3545; /* Matching border color on hover */
    }
	thead tr th {
    color: #FFFFFF;
    border-bottom: 2px solid #FFFFFF !important;
    padding: 0.8rem;
    -webkit-box-flex: 0;
    -ms-flex: 0 0 auto;	
    flex: 0 0 auto;
    text-align: center;
    font-weight: 500;
    font-size: 1rem;
    font-family: Verdana, Geneva, Tahoma, sans-serif;
	position: sticky;
    top: 0;
    background-color: #8AAAE5; /* Or any background color to make it stand out */
    z-index: 10; /* Ensures the header is above other content */
    border-bottom: 2px solid #ddd; /* Optional: Adds a bottom border to the header */

  }
  </style>
</head>

<body>
	 <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <?= include('./menu.php') ?>

    <div class="container mt-5">
        <h2>ITEMS CONSUMED REPORT - ISSUE REGISTER</h2>

        
		<div class="form-group row">
			<div class="col-md-2">
				<label for="issue_start_date">Start Date:</label>
				<input type="date" class="form-control" id="issue_start_date" name="issue_start_date" value='<?= date("Y-m-d") ?>' required>
			</div>
			<div class="col-md-2">
				<label for="issue_end_date">End Date:</label>
				<input type="date" class="form-control" id="issue_end_date" name="issue_end_date" value='<?= date("Y-m-d") ?>' required>
			</div>
			<div class="col-md-2">
				<label for="item_id">Select Item :</label>
				<select class="form-control" id="item_id" name="item_id" required>
					<option value="0">All</option>
					<?php
					$itemQuery = "SELECT id, itemname FROM item order by itemname";
					$itemResult = mysqli_query($dbconn, $itemQuery);

					if ($itemResult) {
						while ($itemRow = mysqli_fetch_array($itemResult)) { ?>
							<option value="<?php echo $itemRow['id']; ?>"><?php echo $itemRow['itemname'] . " - " . $itemRow['id']; ?></option>
						<?php }
					} else {
						echo "<option value=''>No items found</option>";
					}
					?>
				</select>
			</div>
			<div class="col-md-2 d-flex align-items-end">
				<button type="button" id='issue_btn' class="btn btn-primary w-100"> (BLSDA)Report</button>
			</div>
			<div class="col-md-2 d-flex align-items-end">
				<button type="button" id='abstract_btn' class="btn btn-primary w-100">Abstract Report</button>
			</div>
			<div class="col-md-1 d-flex align-items-end">
				<button type="button" id='cost_btn' class="btn btn-primary w-100" >Food Cost</button>
			</div>
			<div class="col-md-1 d-flex align-items-end">
				<button type="button" id='issue_between_date' class="btn btn-primary w-100" >Issue Between Dates</button>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-md-2 d-flex align-items-end">
				<button type="button" id='itemwise_issue_cost_btn' class="btn btn-primary w-100">Itemwise Cost Report</button>
			</div>
			<div class="col-md-2 d-flex align-items-end">
				<button type="button" id='perhead_btn' class="btn btn-primary w-100" >Perhead  Cost</button>
			</div>
			<div class="col-md-2 d-flex align-items-end">
				<button type="button" id='monthwise_btn' class="btn btn-primary w-100" >Monthwise Cost</button>
			</div>
		</div>
        <div class="mt-3">
            <table id="issueReportTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Issue Date</th>
                        <th>Item ID</th>
                        <th>Item Name</th>
						<th>Brand Name</th>
                        <th>Issue<br>Category</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                        <th>Actions</th> <!-- New column for actions -->
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

		
	
    
    <!-- Centered print button -->
    <div class="text-center mt-3">
        <input type="button" id="print_btn" name="button" value="Print" class="btn btn-primary" />
    </div>
	<div id="output" class='mt-3' style='margin:auto 10%;'></div>
		

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Issue Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <input type="hidden" id="edit_id" name="id">
                        <div class="form-group">
                            <label for="edit_issue_date">Issue Date</label>
                            <input type="date" class="form-control" id="edit_issue_date" name="issuedate">
                        </div>
                        <div class="form-group">
                            <label for="edit_item_id">Item ID</label>
                            <input type="text" class="form-control" id="edit_item_id" name="itemid">
                        </div>
						<div class="form-group">
                            <label for="edit_quantity">Issue Quantity</label>
                           
							<input type="text" class="form-control" id="edit_quantity" name="edit_quantity" readonly>
                        </div>
                        <div class="form-group">
                            <label for="return_quantity">ReturnQuantity</label>
                            <input type="text" class="form-control" id="return_quantity" name="return_quantity" value="0">
							
                        </div>
                        <div class="form-group">
                            <label for="edit_unit_price">Unit Price</label>
                            <input type="text" class="form-control" id="edit_unit_price" name="unit_price">
                        </div>
                        <div class="form-group">
                            <label for="edit_issue_category">Issue Category</label>
                            <input type="text" class="form-control" id="edit_issue_category" name="issue_category">
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
	

    
	
    
	

    <script>
		
        $(document).ready(function() {
			$("#item_id").select2();
			
            var dataTable = $('#issueReportTable').DataTable({
                'pageLength': 100,
                'processing': true,
                'fixedHeader': {
                    header: true,
                    footer: true
                },
                'dom': 'Bfrtip',
                'buttons': [{
                    extend: 'excelHtml5',
                    className: 'btn btn-excel',
                    text: '<i class="fa fa-file-excel"></i> Download',
                    titleAttr: 'Export to Excel',
                    title: function() {
						 if ($("#issue_start_date").val() == $("#issue_end_date").val())
                            return 'Issue Register on ' + $("#issue_start_date").val();
                        else
                            return 'Issue Register between ' + $("#issue_start_date").val() + " and " + $("#issue_end_date").val();
                 
					
					},
					exportOptions: {
                        columns: [0, 3, 4, 5, 6, 7, 8] // Exclude columns: 1 (Issue Date), 2 (Item ID), 9 (Actions)
                    },
                }, ],
                'responsive': true,
                'columnDefs': [{
                        responsivePriority: 1,
                        targets: 2
                    },
                    {
                        responsivePriority: 2,
                        targets: -1
                    }
                ],
                'destroy': true,
                'searching': true,
                'orderable': false,
                'ajax': {
                    url: './ajax_fetch_issue_report.php',
                    type: 'POST',
                    data: function() {
                        return {
                            'issue_start_date': $('#issue_start_date').val(),
							'issue_end_date': $('#issue_end_date').val(),
                            'item_id': $('#item_id').val(),
                            'action': window.actionType
                        };
                    },
                    dataSrc: ''
                },
                columns: [{
                        data: 'sno',"className": "center-align" 
                    },
                    {
                        data: 'issue_date'
                    },
                    {
                        data: 'itemid'
                    },
                    {
                        data: 'itemname'
                    },
					 {
                        data: 'brand_name'
                    },
                   {
                        data: 'issue_category'
                    },
                    {
                        data: 'quantity',"className": "right-align" 
                    },
                    {
                        data: 'unit_price',"className": "center-align" 
                    },
                    {
                        data: 'total_price',"className": "center-align" 
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `<button class="btn btn-primary btn-edit" data-id="${row.id}">Return</button>`;
                        }
                    }
                ]
            });
			$('#issue_start_date').on('change', function() {
                $('#issue_end_date').val($(this).val()); // Update issue_end_date value
            });
            $("#issue_btn").click(function() {
                if ($('#issue_date').val() == '') {
                    Swal.fire({
                        title: 'Please Enter Date',
                        icon: 'error',
                    });
                    return false;
                }
			    window.actionType = 'all_item';
                dataTable.ajax.reload();
            });
			$("#abstract_btn").click(function() {
                if ($('#issue_date').val() == '') {
                    Swal.fire({
                        title: 'Please Enter Date',
                        icon: 'error',
                    });
                    return false;
                }
			   
			    window.actionType = 'abstract_item';
                dataTable.ajax.reload();
            });
			
			$("#cost_btn").click(function() {
				
                if ($('#issue_start_date').val() === '' || $('#issue_end_date').val() === '') {
                    Swal.fire({
                        title: 'Please Enter Date',
                        icon: 'error',
                    });
                    return false;
                }
				
				dataTable.clear().draw();

				// Destroy DataTable
				//dataTable.destroy();

				
		
                let formData = {
                    start_date: $("#issue_start_date").val(),
					end_date: $("#issue_end_date").val(),
                };

                $.ajax({
                    url: 'blsd_cost.php',
                    type: 'POST',
                    data: formData,
                    xhrFields: {
                        responseType: 'html'
                    },
                    success: function(html) {
						alert('Food Cost report ready');
                        window.document.getElementById("output").innerHTML = html;
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);
                        alert("Error: " + textStatus + " " + errorThrown);
                    }
                });
            });
			
			$("#perhead_btn").click(function() {
				
                if ($('#issue_start_date').val() === '' || $('#issue_end_date').val() === '') {
                    Swal.fire({
                        title: 'Please Enter Date',
                        icon: 'error',
                    });
                    return false;
                }
				
				dataTable.clear().draw();

				// Destroy DataTable
				//dataTable.destroy();

				
		
                let formData = {
                    start_date: $("#issue_start_date").val(),
					end_date: $("#issue_end_date").val(),
                };

                $.ajax({
                    url: 'perhead_cost.php',
                    type: 'POST',
                    data: formData,
                    xhrFields: {
                        responseType: 'html'
                    },
                    success: function(html) {
						alert('Perhead Food Cost Report ready');
                        window.document.getElementById("output").innerHTML = html;
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);
                        alert("Error: " + textStatus + " " + errorThrown);
                    }
                });
            });
			
			$("#itemwise_issue_cost_btn").click(function() {
				
                if ($('#issue_start_date').val() === '' || $('#issue_end_date').val() === '') {
                    Swal.fire({
                        title: 'Please Enter Date',
                        icon: 'error',
                    });
                    return false;
                }
					
				
		
                let formData = {
                    start_date: $("#issue_start_date").val(),
					end_date: $("#issue_end_date").val(),
                };

                $.ajax({
                    url: 'itemwise_issue_cost_between_dates.php',
                    type: 'POST',
                    data: formData,
                    xhrFields: {
                        responseType: 'html'
                    },
                    success: function(html) {
						alert('Issue Cost report ready');
                        window.document.getElementById("output").innerHTML = html;
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);
                        alert("Error: " + textStatus + " " + errorThrown);
                    }
                });
            });
			
			$("#perhead_btn").click(function() {
				
                if ($('#issue_start_date').val() === '' || $('#issue_end_date').val() === '') {
                    Swal.fire({
                        title: 'Please Enter Date',
                        icon: 'error',
                    });
                    return false;
                }
				
				dataTable.clear().draw();

				// Destroy DataTable
				//dataTable.destroy();

				
		
                let formData = {
                    start_date: $("#issue_start_date").val(),
					end_date: $("#issue_end_date").val(),
                };

                $.ajax({
                    url: 'perhead_cost.php',
                    type: 'POST',
                    data: formData,
                    xhrFields: {
                        responseType: 'html'
                    },
                    success: function(html) {
						alert('Perhead Food Cost Report ready');
                        window.document.getElementById("output").innerHTML = html;
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);
                        alert("Error: " + textStatus + " " + errorThrown);
                    }
                });
            });
			
			$("#monthwise_btn").click(function() {
				
                if ($('#issue_start_date').val() === '' || $('#issue_end_date').val() === '') {
                    Swal.fire({
                        title: 'Please Enter Date',
                        icon: 'error',
                    });
                    return false;
                }
					
				
		
                let formData = {
                    start_date: $("#issue_start_date").val(),
					end_date: $("#issue_end_date").val(),
                };

                $.ajax({
                    url: 'monthwise_issue_cost.php',
                    type: 'POST',
                    data: formData,
                    xhrFields: {
                        responseType: 'html'
                    },
                    success: function(html) {
						alert('Monthwise Issue Cost report ');
                        window.document.getElementById("output").innerHTML = html;
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);
                        alert("Error: " + textStatus + " " + errorThrown);
                    }
                });
            });
			
			
			
			
			//issue between date starts
			$("#issue_between_date").click(function() {
				
                if ($('#issue_start_date').val() === '' || $('#issue_end_date').val() === '') {
                    Swal.fire({
                        title: 'Please Enter Date',
                        icon: 'error',
                    });
                    return false;
                }
				
				dataTable.clear().draw();				
		
                let formData = {
                    start_date: $("#issue_start_date").val(),
					end_date: $("#issue_end_date").val(),
					item_id: $("#item_id").val(),
                };

                $.ajax({
                    url: 'genearate_datewise_issue.php',
                    type: 'POST',
                    data: formData,
                    xhrFields: {
                        responseType: 'html'
                    },
                    success: function(html) {
						alert('Issue Register Ready');
                        window.document.getElementById("output").innerHTML = html;
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);
                        alert("Error: " + textStatus + " " + errorThrown);
                    }
                });
            });
			//issue between ends
			

            $(document).on('click', '.btn-edit', function() {
                var id = $(this).data('id');
                // Fetch the data of the selected row using AJAX
                $.ajax({
                    url: './ajax_fetch_update_issue.php',
                    type: 'POST',
                    data: {
                        id: id,
                        action: 'fetch_issue'
                    },
                    success: function(response) {
                        var data = JSON.parse(response);

                        $('#edit_id').val(data.id);
                        $('#edit_issue_date').val(data.issue_date);
                        $('#edit_item_id').val(data.itemid);
                        $('#edit_quantity').val(data.quantity);
                        $('#edit_unit_price').val(data.unit_price);
                        $('#edit_issue_category').val(data.issue_category);
                        $('#editModal').modal('show');
                    }
                });
            });

            $('#editForm').submit(function(e) {
                e.preventDefault();

                var id = $('#edit_id').val(); // Retrieve ID from hidden field
				//var ret_qty=$('#return_quantity').val();
				//var edt_qty=$('#edit_quantity').val();
				var ret_qty = parseFloat($('#return_quantity').val()) || 0; // Convert to number or default to 0
				var edt_qty = parseFloat($('#edit_quantity').val()) || 0; // Convert to number or default to 0
    
				
				if( ret_qty > edt_qty  || $('#return_quantity').val() =='')
				{
					Swal.fire({
						title: 'Please Enter valid return quantity',
						icon: 'error',
					});
					return false;
					
				}
				

                $.ajax({
                    url: './ajax_fetch_update_issue.php',
                    type: 'POST',
                    data: {
                        id: id,
                        issue_date: $('#edit_issue_date').val(),
                        item_id: $('#edit_item_id').val(),
                        return_quantity: $('#return_quantity').val(),
                        unit_price: $('#edit_unit_price').val(),
                        issue_category: $('#edit_issue_category').val(),
                        action: 'update_issue'
                    },
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.status == 'success') {
                            $('#editModal').modal('hide');
                            $('#issueReportTable').DataTable().ajax.reload();
                            Swal.fire('Updated!', data.message, 'success');
                        } else {
                            Swal.fire('Error!', data.message, 'error');
                        }
                    }
                });
            });
			$("#print_btn").click(function() {
        printDiv();
    });
	function printDiv() {
				var content = document.getElementById("output").innerHTML;
				var printWindow = window.open('', '', 'height=500, width=800');
				
				printWindow.document.write('<html><head><title>Print</title>');
				printWindow.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">'); 
				printWindow.document.write('</head><body >');
				printWindow.document.write(content);
				printWindow.document.write('</body></html>');
				
				printWindow.document.close(); // Close the document for writing
				printWindow.focus(); // Focus on the new window
				printWindow.print(); // Print the content
		}			

     });
    </script>
</body>

</html>
