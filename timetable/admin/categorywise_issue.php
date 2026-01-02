<!DOCTYPE html>
<?php include("admindbconn.php") ?>
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
        <h2>ITEMS CONSUMED REPORT</h2>

        
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
				<label for="item_category">Select Item :</label>
				<select class="form-control" id="item_category" name="item_category" required>
					
					<?php
					$itemQuery = "SELECT distinct item_category FROM item order by item_category";
					$itemResult = mysqli_query($dbconn, $itemQuery);

					if ($itemResult) {
						while ($itemRow = mysqli_fetch_array($itemResult)) { ?>
							<option value="<?php echo $itemRow['item_category']; ?>"><?php echo $itemRow['item_category'] ; ?></option>
						<?php }
					} else {
						echo "<option value=''>Not  found</option>";
					}
					?>
				</select>
			</div>
			
			<div class="col-md-1 d-flex align-items-end">
				<button type="button" id='issue_between_date' class="btn btn-primary w-100" >Issue Between Dates</button>
			</div>
		</div>

        <div class="mt-3">
			<span id='date_to_from'></span>
            <table id="issueReportTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>S.No</th>                        
                        <th>Item ID</th>
                        <th>Item Name</th>
						<th>Brand Name</th>                        
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>                       
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
                            return  $("#item_category").val() + ' item Issue  on ' + $("#issue_start_date").val();
                        else
                            return  $("#item_category").val() + ' item Issue  between ' + $("#issue_start_date").val() + " and " + $("#issue_end_date").val();
                 
					
					},
					exportOptions: {
                        columns: [0, 1,2,3, 4, 5, 6] // 
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
                    url: './ajax_fetch_category.php',
                    type: 'POST',
                    data: function() {
                        return {
                            'issue_start_date': $('#issue_start_date').val(),
							'issue_end_date': $('#issue_end_date').val(),
                            'item_category': $('#item_category').val(),
                            'action': window.actionType
                        };
                    },
                    dataSrc: ''
                },
                columns: [{
                        data: 'sno',"className": "center-align" 
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
                        data: 'quantity',"className": "right-align" 
                    },
                    {
                        data: 'unit_price',"className": "center-align" 
                    },
                    {
                        data: 'total_price',"className": "center-align" 
                    }
                    
                ]
            });
			$('#issue_start_date').on('change', function() {
                //$('#issue_end_date').val($(this).val()); // Update issue_end_date value
            });
           
		    $("#issue_between_date").click(function() {
				var start_date = $('#issue_start_date').val();
				var end_date = $('#issue_end_date').val();
				var item_category = $('#item_category').val();
				
				if (start_date == '' || end_date == '') {
					Swal.fire({
						title: 'Please Enter Date',
						icon: 'error',
					});
					return false;
				}

				if (start_date === end_date) {
					$('#date_to_from').html(`${item_category} on <strong>${start_date}</strong>`);
				} else {
					$('#date_to_from').html(` ${item_category} from <strong>${start_date}</strong> to <strong>${end_date}</strong>`);
				}
				
				window.actionType = 'all_item';
				dataTable.ajax.reload();
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
