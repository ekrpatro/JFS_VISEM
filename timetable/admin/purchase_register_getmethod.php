<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purchase Register</title>
    <link type="text/css" rel="stylesheet" href="../css/nav.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
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
    </style>
	
	<link type="text/css" rel="stylesheet" href="../css/menu.css">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
   
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

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
    <h1 style='text-align:center;'>Purchase Register </h1>
	<div class="centered-table">
			<table id='pr_id' >
				<tr>
					<td><label for="purchase_date">Purchase Date:</label>
						
					</td>
					<td>
					
						<label for="invoiceno">Invoice No :</label>
						
						
					</td>
					<td>
						<label for="invoiceno"></label>
						
					</td>
				</tr>
				<tr>
					<td>
						<input type="date" class="form-control" id="purchase_date" name="purchase_date" required>
					</td>
					<td>
					
						
						<input type="invoiceno" class="form-control" id="invoiceno" name="invoiceno" required>
						
					</td>
					<td>
						
						<button type="button" id='gen_btn' class="btn btn-primary"> Get Report</button>
					</td>
				</tr>
			</table>
            
			
            
    </div>
    <section class="purchase_register__form">
      
        <table id="purchaseTable" style='width: 1050px;' class="display">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Purchase Date</th>
                    <th>Invoice Number</th>
                    <th>ShopName</th>
                    <th>Item id</th>
                    <th>Item name</th>
                    <th>Price/unit</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
      
    </section>
    <script>
        $(document).ready(function() {
            // Initialize DataTables
            var table = $('#purchaseTable').DataTable({
                "ajax": {
                    "url": "ajax_purchase_data.php",
                    "type": "POST",
                    "dataSrc": ""
                },
                "columns": [
                    { "data": "sno" },
                    { "data": "purchase_date" },
                    { "data": "invoice_no" },
                    { "data": "shopname" },
                    { "data": "itemid" },
                    { "data": "itemname" },
                    { "data": "unit_price" },
                    { "data": "quantity" },
                    { "data": "total_price","className": "right-align" }
                ],
               
                "dom": '<"top"f>rt<"bottom"lp><"clear">',
				"buttons": [{
					extend:    'excelHtml5',
					className: 'btn btn-primary',
					text:      '<i class="fa fa-file-excel"></i>',
					titleAttr: 'Export to Excel',
					title: function(){ return 'PurchaseRegisterData'; },
				}
			],
            });
            

            // Handle form submission
            $('#searchForm').on('submit', function(e) {
                e.preventDefault();
                var searchItem = $('#searchItem').val();
                table.ajax.url("ajax_purchase_data.php?searchitem=" + searchItem).load();
            });
			 $('#gen_btn').on('click', function(e) {
                e.preventDefault();
				
                var invoiceno = $('#invoiceno').val();
				// alert(invoiceno);
				
                table.ajax.url("ajax_purchase_data.php?invoiceno=" + invoiceno).load();
            });
			$('#gen_btn').on('click', function(e) {
                e.preventDefault();
				
                var invoiceno = $('#invoiceno').val();
				//alert(invoiceno);
                var purchase_date = $('#purchase_date').val();
				
				
                table.ajax.url("ajax_purchase_data.php?invoiceno=" + invoiceno+"&purchase_date="+purchase_date).load();
            });

       
        });
    </script>
</body>
</html>