<!DOCTYPE html>
<?php include("admindbconn.php") ?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ItemWise Stock </title>
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
        <h2>Itemwise Stock </h2>

        
		<div class="form-group row">
			<div class="col-md-3">
				<label for="start_date"> Start Date:</label>
				<input type="date" class="form-control" id="start_date" name="start_date" value='<?= date("Y-m-d") ?>' required>
			</div>
			<div class="col-md-3">
				<label for="end_date"> End Date:</label>
				<input type="date" class="form-control" id="end_date" name="end_date" value='<?= date("Y-m-d") ?>' required>
			</div>
			<div class="col-md-3">
				<label for="item_id">Select Item :</label>
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
				<input type="button"  id='item_stk_btn' class="btn btn-primary w-10" value='ShowStock' style="margin-right: 10px;">
				
			</div>
		</div>
		<div id="output" >
		</div>

        <div class="mt-3">
            <table id="stkTable" class="table table-bordered">
                <thead>
					<tr>
                        <th colspan=8><span id='date_title'></span></th> 
					</tr>
                    <tr>
                        <th>S.No</th>                       
                        <th>Item ID</th>
                        <th>Item Name</th>
						<th>Date</th>
						<th>Opening Balance</th> 
						<th>Purchase</th>						 						 
						<th>Issue</th>	  
						<th>Closing Balance</th>                        
						<!--<th>Current<br>StockQuantity</th>  -->
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
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
                    url: './ajax_fetch_stock_data.php',
                    type: 'POST',
                    data: function() {
                        return {
                            'start_date': $('#start_date').val(),
                            'end_date': $('#end_date').val(),
                            'item_id': $('#item_id').val(),
                            'action': 'itemwise_balance'
                        };
                    },
                    dataSrc: ''
                },
                columns: [{
                        data: 'sno',
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
                        data: 'stock_date'
                    },
                    {
                        data: 'opening_balance',
                        "className": "right-align"
                    },
                    {
                        data: 'purchase',
                        "className": "right-align"
                    },
                    {
                        data: 'issue',
                        "className": "right-align"
                    },
                    {
                        data: 'closing_balance',
                        "className": "right-align"
                    }
                    
                ]
            });
        }
    });
});


    </script>
</body>

</html>
