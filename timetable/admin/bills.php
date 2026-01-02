<?php
session_start();
include("admindbconn.php");
// Session validation
if (!isset($_SESSION['user_name']) || $_SESSION["role"] != 'ADMIN') {
    echo "Invalid User";
    //exit(0);
}?>
<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purchase Bills</title>
  
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
		  <style>
    .gap {
        margin-top: 20px; /* Adjust the value as needed */
    }
</style>
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
        <h2>Purchase Bills </h2>

        <div class="form-group row">
            <div class="col-md-2">
                <label for="start_date"> Start Date:</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value='<?= date("Y-m-d") ?>' required>
            </div>
            <div class="col-md-2">
                <label for="end_date"> End Date:</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value='<?= date("Y-m-d") ?>' required>
            </div>
            <div class="col-md-2">
                <label for="item_id">Select SupplierName :</label>
                <select class="form-control" id="seller_id" name="seller_id" required>
                    <option value="0">All</option>
                    <?php
                    $itemQuery = "SELECT `seller_id`, `shopname` FROM `seller` WHERE 1";
                    $itemResult = mysqli_query($dbconn, $itemQuery);

                    if ($itemResult) {
                        while ($itemRow = mysqli_fetch_array($itemResult)) { ?>
                            <option value="<?php echo $itemRow['seller_id']; ?>"><?php echo $itemRow['shopname'] ; ?></option>
                        <?php }
                    } else {
                        echo "<option value=''>No items found</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" id='show_bill_btn' class="btn btn-primary">Show</button>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" id='datewise_btn' class="btn btn-info ">DateWise Bills</button>
            </div>
			<div class="col-md-2 d-flex align-items-end">
                <button type="button" id='catg_btn' class="btn btn-info ">Categorywise Bills</button>
            </div>
			
            
        </div>
		 <div class="form-group row">
			<div class="col-md-2 gap">
                <button type="button" id='btn_new_bill' class="btn btn-primary " data-toggle="modal" data-target="#newBillModal">Add New Bills</button>
            </div>
		 </div>
        
        <!-- Modal -->
        <div class="modal fade" id="newBillModal" tabindex="-1" role="dialog" aria-labelledby="newBillModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newBillModalLabel">Add New Bill</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="purchaseBillForm">
                            
                            <div class="form-group">
                                <label for="purchase_date">Purchase Date:</label>
                                <input type="date" id="purchase_date" name="purchase_date" class="form-control" value='<?=date('Y-m-d')?>' required>
                            </div>
							<div class="form-group">
                                <label for="invoice_no">Invoice No:</label>
                                <input type="text" id="invoice_no" name="invoice_no" class="form-control" required>
                            </div>
							<div class="form-group">
                                <label for="new_seller_id">Seller</label>
                                <select id="new_seller_id" name="new_seller_id" class="form-control select2" required>                              
              					  <?php
									$query = "SELECT seller_id, shopname,item_category FROM seller order by seller_id"; // Corrected SQL query
									$result1 = mysqli_query($dbconn, $query);

									if ($result1) {
										while ($row1 = mysqli_fetch_array($result1)) { ?>
											<option value="<?php echo $row1['seller_id'].":".$row1['item_category']; ?>"><?php echo $row1['shopname']; ?></option>
										<?php }
									} else {
										echo "<option value=''>No suppliers found</option>";
									}
									?>
								</select>
                            </div>
                            
                            <!--<div class="form-group">
                                <label for="item_category">Item Category:</label>
                                <select id="item_category" name="item_category" class="form-control select2" required>
                                    <option value="Grocery">Grocery</option>									
									<option value="Vegetables">Vegetables</option>
									<option value="NonVeg">NonVeg</option>
																		
									<option value="Milk">Milk</option>
									<option value="Fruits">Fruits</option>
									<option value="Gas">Gas</option>
									<option value="FireWood">FireWood</option>
									
                                </select>
                            </div>
							-->
                            
                            <div class="form-group">
                                <label for="total_amount">Total Amount:</label>
                                <input type="number" id="total_amount" name="total_amount" class="form-control" step="0.01" required>
                            </div>
							<input type=hidden name='action' id='action' value='ins_new_bill'>
							<button type="submit" class="btn btn-primary" id="newbill_submitBtn">Save Bill</button>                          
							
                        </form>
                    </div>
					
                </div>
            </div>
        </div>
		<div class="d-flex align-items-end">								
				<a href="purchase_item_record.php" class="btn btn-primary ">Purchase Items</a>
				<span>&nbsp;</span>
				<a href="bills_crud.php" class="btn btn-info ">Manage Bills</a>
		</div>
		

        <div class="mt-3">
            <table id="purchaseTbl" class="table table-bordered">
                <thead>
                    <tr>
                        <th colspan=6><span id='date_title'></span></th> 
                    </tr>
                    <tr>
                        <th>S.No</th>                       
                        <th>Seller Id.</th>
                        <th>Seller Name</th>
                         <th>Start Date</th>
                         <th>End Date</th>                         
                         <th>Amount</th>                         
                                
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <div id="output" class="mt-3" style="margin: auto 20%; width:80%;"></div>
    
    <!-- Centered print button -->
    <div class="text-center mt-3">
        <input type="button" id="print_btn" name="button" value="Print" class="btn btn-primary" />
    </div>

    <script>
        $(document).ready(function() {
            function formatDecimal(num) {
                return parseFloat(num).toFixed(2);
            }

            var dataTable = $('#purchaseTbl').DataTable({
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
                        return 'Purchase Report between ' + $("#start_date").val()+ ' and '+ $("#end_date").val();
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
                    },
                    {
                        render: function(data, type, row) {
                            if (type === 'display' || type === 'filter') {
                                if (data === null) {
                                    return "";
                                } else if (isNaN(data) || data === null) {
                                    return '0.00';
                                } else {
                                    return parseFloat(data).toFixed(2);
                                }
                            } else {
                                return data;
                            }
                        }
                    }
                ],
                'destroy': true,
                'searching': true,
                'orderable': false,
                'ajax': {
                    url: './ajax_bill_data.php',
                    type: 'POST',
                    data: function() {
                        return {
                            'start_date': $('#start_date').val(),
                            'end_date': $('#end_date').val(),
                            'seller_id': $('#seller_id').val(),
                            'action': 'purchase_bills'
                        };
                    },
                    dataSrc: ''
                },
                columns: [{
                        data: 'sno',"className": "center-align"
                    },
                    {
                        data: 'seller_id' ,"className": "center-align"
                    },
                    {
                        data: 'seller_name',"className": "left-align"
                    },
                    {
                        data: 'start_date', "className": "center-align"
                    },
                    {
                        data: 'end_date', "className": "center-align"
                    },
                    {
                        data: 'tot_bill', "className": "right-align"
                    }
                ]
            });

            $('#btn_new_bill').click(function() {
                
                $('#newBillModal').modal('show');
            });
			//Start
			 $('#purchaseBillForm').submit(function(e) {
                e.preventDefault();
               // var item_category = $('#item_category').val(); // Retrieve ID from hidden field
				var total_amount=$('#total_amount').val();	
				alert($('#invoice_no').val());
				
				/*if( $('#total_amount').val()==''  || $('#invoice_no').val() ==''  || $('#purchase_date').val() =='')
				{
					Swal.fire({
						title: 'Please Enter valid data',
						icon: 'error',
					});
					return false;
					
				}	*/
				if ($.trim($('#total_amount').val()) === '' || 
					$.trim($('#invoice_no').val()).length === 0 || 
					$.trim($('#purchase_date').val()) === '') {
					Swal.fire({
						title: 'Please Enter valid data',
						icon: 'error',
					});
					return false;
				}
				alert($.trim($('#invoice_no').val()).length );
				if ( $.trim($('#invoice_no').val()).length  < 3 ) {
					Swal.fire({
						title: 'Please Enter valid invoice_no',
						icon: 'error',
					});
					return false;
				}

               
				$.ajax({
						url: './ajax_bill_data.php',
						type: 'POST',
						data: {
							seller_id: $('#new_seller_id').val(),
							purchase_date: $('#purchase_date').val(),
							invoice_no: $('#invoice_no').val(),
							tot_amount: $('#total_amount').val(),
							//item_category: $('#item_category').val(),
							action: 'ins_new_bill'
						},
						success: function(response) {
							 try {
								var data = JSON.parse(response);
								if (data.status === 'success') {
									document.getElementById('purchaseBillForm').reset();
									$('#newBillModal').modal('hide');
									Swal.fire('Updated!', data.message, 'success');
								} else {
									Swal.fire('Error!', data.message, 'error');
								}
							} catch (e) {
								console.error('Parsing error:', e);
								Swal.fire('Error!', 'Warning: Duplicate Invoice for same day not allowed.', 'error');
							}
						},
						error: function(jqXHR, textStatus, errorThrown) {
							Swal.fire('Error!', 'Duplicate Invoice for same day not allowed, Request failed: ' + textStatus, 'error');
						}
					});
				
				
            });
			//ends
            $('#start_date').on('change', function() {
                $('#end_date').val($(this).val()); // Update end_date value
            });

            $("#show_bill_btn").click(function() {
                if ($('#start_date').val() == '' || $('#end_date').val() == '') {
                    Swal.fire({
                        title: 'Please Enter Date',
                        icon: 'error',
                    });
                    return false;
                }
                $('#date_title').text('Purchase Bill between ' + $('#start_date').val()+ ' and '+ $('#end_date').val());
                dataTable.ajax.reload();
            }); 
            
            // Datewise Bill starts
            $("#datewise_btn").click(function() {
                if ($('#start_date').val() === '' || $('#end_date').val() === '') {
                    Swal.fire({
                        title: 'Please Enter Date',
                        icon: 'error',
                    });
                    return false;
                }
                
                dataTable.clear().draw();

                let formData = {
                    start_date: $("#start_date").val(),
                    end_date: $("#end_date").val(),
                    seller_id: $("#seller_id").val(),
                    action: 'print_datewise',
                };

                $.ajax({
                    url: 'ajax_bill_data.php',
                    type: 'POST',
                    data: formData,
                    xhrFields: {
                        responseType: 'html'
                    },
                    success: function(html) {
                        alert('Datewise Report');
                        window.document.getElementById("output").innerHTML = html;
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);
                        alert("Error: " + textStatus + " " + errorThrown);
                    }
                });
            });
            // Datewise bills ends 
			
			//categorywise bills starts
			// Datewise Bill starts
            $("#catg_btn").click(function() {
                if ($('#start_date').val() === '' || $('#end_date').val() === '') {
                    Swal.fire({
                        title: 'Please Enter Date',
                        icon: 'error',
                    });
                    return false;
                }
                
                dataTable.clear().draw();

                let formData = {
                    start_date: $("#start_date").val(),
                    end_date: $("#end_date").val(),
                    seller_id: $("#seller_id").val(),
                    action: 'print_catwise',
                };

                $.ajax({
                    url: 'ajax_bill_data.php',
                    type: 'POST',
                    data: formData,
                    xhrFields: {
                        responseType: 'html'
                    },
                    success: function(html) {
                        alert('Categorywise Report');
                        window.document.getElementById("output").innerHTML = html;
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);
                        alert("Error: " + textStatus + " " + errorThrown);
                    }
                });
            });
			//end

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
