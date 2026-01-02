<!DOCTYPE html>
<?php include("admindbconn.php") ?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DateWise Stock Report</title>
  
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
        <h2>Datewise Stock </h2>

        
		<div class="form-group row">
			<div class="col-md-3">
				<label for="start_date"> Start Date:</label>
				<input type="date" class="form-control" id="start_date" name="start_date" value='<?= date("Y-m-d") ?>' required>
			</div>
			<!--<div class="col-md-3">
				<label for="end_date"> End Date:</label>
				<input type="date" class="form-control" id="end_date" name="end_date" value='<?= date("Y-m-d") ?>' required>
			</div>-->
			<div class="col-md-3">
				<label for="item_id">Select Item :</label>
				<select class="form-control" id="item_id" name="item_id" required>
					<option value="0">All</option>
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
				<input type="button"  id='show_stk_btn' class="btn btn-primary w-10" value='ShowStock' style="margin-right: 10px;">
				<input type="button"  id='stk_report_btn' class="btn btn-primary w-10" onclick="generateReport()" value="Generate Report" style="margin-right: 10px;">
				<input type="button"  id='print_stk_btn' class='btn btn-primary w-10' value="Print" onClick="printDiv()"/>
			</div>
		</div>
		<div id="output" >
		</div>

        <div class="mt-3">
            <table id="stkTable" class="table table-bordered">
                <thead>
					<tr>
                        <th colspan=9><span id='date_title'></span></th> 
					</tr>
                    <tr>
                        <th>S.No</th>                       
                        <th>Item ID</th>
                        <th>Item Name</th>
						 <th>Total Purchase<br>Quantity</th>
						 <th>Total Purchase<br>Cost</th>						 
						 <th>Total Issue<br> Quantity</th>						 
						 <th>Total Issue<br> Cost</th>						 
                        <th>Opening Balance<br>Quantity</th>                        
						<th>Opening Balance<br>Cost</th>                        
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>


 

    <script>
        $(document).ready(function() {
			function formatDecimal(num) {
			return parseFloat(num).toFixed(2);
			}
            var dataTable = $('#stkTable').DataTable({
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
                }, ],
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
					// Format columns with decimal values to two decimal places
					targets: [3, 4, 5, 6, 7, 8],
					render: function(data, type, row) 
							{
								if (type === 'display' || type === 'filter') 
								{
									//return formatDecimal(data);
									if ( data === null) 
									{
										return "";
									}
									else
									if (isNaN(data) || data === null) 
									{
										return '0.00';
									}
									else
										return parseFloat(data).toFixed(2);
								}
								else
									return data;
							}
					}
					
				
                ],
                'destroy': true,
                'searching': true,
                'orderable': false,
                'ajax': {
                   // url: './ajax_fetch_issue_report.php',
				    url: './ajax_fetch_stock_data.php',
                    type: 'POST',
                    data: function() {
                        return {
                            'start_date': $('#start_date').val(),
							//'end_date': $('#end_date').val(),
                            'item_id': $('#item_id').val(),
                            'action': 'stock_balance'
                        };
                    },
                    dataSrc: ''
                },
                columns: [{
                        data: 'sno',"className": "center-align"
                    },
                    
                    {
                        data: 'itemid' ,"className": "center-align"
                    },
                    {
                        data: 'itemname'
                    },
					
					{
                        data: 'tot_purchase_qty', "className": "right-align"
                    },
					{
                        data: 'tot_purchase_cost', "className": "right-align"
                    },
					{
                        data: 'tot_issue_qty', "className": "right-align"
                    },
					{
                        data: 'tot_issue_cost', "className": "right-align"
                    },
                    {
                        data: 'ob_qty', "className": "right-align"
                    }
					,
                    {
                        data: 'ob_cost', "className": "right-align"
                    }
                    
                ]
            });
			
			
			/*$('#start_date').on('change', function() {
                $('#end_date').val($(this).val()); // Update issue_end_date value
            });*/
            $("#show_stk_btn").click(function() {
                if ($('#start_date').val() == '') {
                    Swal.fire({
                        title: 'Please Enter Date',
                        icon: 'error',
                    });
                    return false;
                }
				$('#date_title').text('Stock Balance Report on ' + $('#start_date').val());

                dataTable.ajax.reload();
            });
			
		function generateReport() {
            let formData = {
                start_date: $("#start_date").val(),                
				//report_type: $("#report_type").val()
            };

            $.ajax({
                url: 'generate_stock_report.php',
                type: 'POST',
                data: formData,
                xhrFields: {
                    
					responseType: 'html'
                },
                success: function (html) {
                    
					window.document.getElementById("output").innerHTML=html;
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                    alert("Error: " + textStatus + " " + errorThrown);
                }
            });
        }
		
		function printDiv() {
			var output=window.document.getElementById("output").innerHTML;
			let a = window.open('', '', 'height=500, width=500');
			a.document.write('<html>');
            a.document.write('<body>');
            a.document.write(output);
            a.document.write('</body></html>');
            a.document.close();
            a.print();
		}
			

            
            

        });
    </script>
</body>

</html>
