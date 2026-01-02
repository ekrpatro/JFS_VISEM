<!DOCTYPE html>
<?php include("admindbconn.php") ?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Issue Edit</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
        .generate-report-button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .generate-report-button:hover {
            background-color: #0056b3;
        }
        #open_close_stk_btn{ background-color:olive;}
        .action-buttons {
            display: flex;
            gap: 5px;
        }
    </style>
</head>
<body>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        <h2>Itemwise Issue</h2>
        <div class="form-group row">
            <div class="col-md-3">
                <label for="start_date">Start Date:</label>
                <input type="date" class="form-control" id="start_date" name="start_date" 
				value='2024-07-07' required>
            </div>
            <div class="col-md-3">
                <label for="end_date">End Date:</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value='<?= date("Y-m-d") ?>' required>
            </div>
            <div class="col-md-3">
                <label for="item_id">Select Item:</label>
                <select class="form-control" id="item_id" name="item_id" required>
                    
                    <?php
                    $itemQuery = "SELECT id, itemname FROM item";
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
            <div class="col-md-4 d-flex align-items-end">
                <input type="button" id='item_stk_btn' class="btn btn-primary w-10" value='Show Issue List' style="margin-right: 10px;">
            </div>
        </div>
        <div id="output"></div>
        <div class="mt-3">
            <table id="stkTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th colspan=11><span id='date_title'></span></th>
                    </tr>
                    <tr>
                        <th>S.No</th>
                        <th>Item ID</th>
                        <th>Item Name</th>
                        <th>Date</th>
                        <th>IssueCategory</th>
                        <th>Quantity</th>                        
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
	
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
                            <input type="date" class="form-control" id="edit_issue_date" name="issuedate" readonly>
                        </div>
                        <div class="form-group">
                            <label for="edit_item_id">Item ID</label>
                            <input type="text" class="form-control" id="edit_item_id" name="itemid" readonly>
                        </div>
						<div class="form-group">
                            <label for="edit_issue_category">Issue Category</label>
                            <input type="text" class="form-control" id="edit_issue_category" name="issue_category">
                        </div>
						<div class="form-group">
                            <label for="edit_quantity">Issue Quantity</label>
                           
							<input type="text" class="form-control" id="edit_quantity" name="edit_quantity" >
                        </div>
                       
                        
                        <button type="submit" class="btn btn-primary">Edit Issue Quantity</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            var dataTable;

            $("#item_id").select2();

            $('#item_stk_btn').click(function() {
                if ($.fn.DataTable.isDataTable('#stkTable')) {
                    dataTable.ajax.reload();
                } else {
                    dataTable = $('#stkTable').DataTable({
                        'pageLength': 100,
                        'processing': true,
                        'fixedHeader': {
                            header: true,
                            footer: true
                        },
                        'dom': 'Bfrtip',
                        'buttons': [{
                            extend: 'excelHtml5',
                            className: 'btn btn-primary',
                            text: '<i class="fa fa-file-excel"></i> Download',
                            titleAttr: 'Export to Excel',
                            title: function() {
                                return 'Stock Balance Report on ' + $("#start_date").val();
                            },
                        }],
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
                            url: './ajax_fetch_update_issue.php',
                            type: 'POST',
                            data: function() {
                                return {
                                    'start_date': $('#start_date').val(),
                                    'end_date': $('#end_date').val(),
                                    'item_id': $('#item_id').val(),
                                    'action': 'fetch_all_issue'
                                };
                            },
                            dataSrc: ''
                        },
                        columns: [{
                                data: 'id',
                                "className": "center-align"
                            },
                            {
                                data: 'itemid',
                                "className": "center-align"
                            },
                            {
                                data: 'itemname'
                            },
                            {
                                data: 'issue_date'
                            },
                            {
                                data: 'issue_category',
                                "className": "right-align"
                            },
                            {
                                data: 'quantity',
                                "className": "right-align"
                            },
                            
                            {
                                data: null,
                                defaultContent: `
                                    <div class="action-buttons">
                                        <button class="btn btn-warning btn-sm btn-edit"><i class="fa fa-edit"></i> Edit</button>
                                        
                                    </div>
                                `,
                                "className": "center-align"
                            }
                        ]
                    });
                }
            });
			 $(document).on('click', '.btn-edit', function() {
				// Get the data for the row that contains the clicked button
				var rowData = dataTable.row($(this).parents('tr')).data();

				// Populate the modal fields with the row data
				$('#edit_id').val(rowData.id);
				$('#edit_issue_date').val(rowData.issue_date);
				$('#edit_item_id').val(rowData.itemid);
				$('#edit_quantity').val(rowData.quantity);
				$('#edit_issue_category').val(rowData.issue_category);

				// Show the modal
				$('#editModal').modal('show');
			});


            $('#editForm').submit(function(e) {
				e.preventDefault();

				var id = $('#edit_id').val(); 				
				var edt_qty = parseFloat($('#edit_quantity').val()) || 0; 
				alert(id);
				alert(edt_qty);

				$.ajax({
					url: './ajax_fetch_update_issue.php',
					type: 'POST',
					data: {
						id: id,
						edit_quantity: edt_qty,
						action: 'edit_issue'
					},
					success: function(response) {
						var data = JSON.parse(response);
						if (data.status == 'success') {
							$('#editModal').modal('hide');
							// Reload the DataTable to reflect the changes
							$('#stkTable').DataTable().ajax.reload(null, false);  // Reload DataTable without resetting pagination
							Swal.fire('Updated!', data.message, 'success');
						} else {
							Swal.fire('Error!', data.message, 'error');
						}
					}
				});
			});


            
        });
    </script>
</body>
</html>
