<!DOCTYPE html>
<? error_reporting(E_ALL);
ini_set('display_errors', 1);?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stock Verification</title>
    <link type="text/css" rel="stylesheet" href="../css/nav.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
  

    <style>
        /* Your existing styles */
        /* Adjust the height for the table */
        .stock__form table {
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
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	
	<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.1.2/css/buttons.dataTables.min.css">
    
    
	<link type="text/css" rel="stylesheet" href="../css/menu.css">
	
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>

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

        .main__stock {
            background-color: #DDDDDD;
            min-height: 100vh;
        }

        #stock_table {
            margin: 10px;
            border: solid 2px black;
            width: 50%;
        }
		
        .right-align {
            text-align: right;
        }

        .center-align {
            text-align: center;
        }
		td,th{
			padding:5px;
		}
        .highlight {
            color: #ff0000; /* red color */
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
		 .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .header-container h1 {
            margin: 0;
            font-size: 24px;
            color: #343a40;
        }

        .header-container .btn-group {
            margin-left: auto;
        }
		
		 .btn-group .btn {
        flex: 1; /* Make buttons take equal space */
        margin: 0 5px; /* Space between buttons */
        text-align: center; /* Center text inside button */
    }

    .btn-group {
        display: flex; /* Use flexbox to align buttons horizontally */
        justify-content: center; /* Center buttons horizontally */
    }
	#gen_po_btn{
			 margin: 0 5px;
            border: solid 2px black;
            width: 20%;
			
		}
		
   
    </style>
</head>
<body>
    <!-- Header and Navigation Bar -->
<?= include('./menu.php') ?>


	
    <section class="main__stock">
    <h1 style="text-align: center;">Stock Verification </h1>

        <table id="stockTable" style="width: 1050px;" class="display">
			<thead>
				<tr>
					<th style="text-align: center;">S.No</th>
					<th style="text-align: center;">Item id</th>
					<th style="text-align: left;">Item name</th>
					<th style="text-align: center;">Total Purchase</th>
					<th style="text-align: center;">Total Issue</th>
					<th style="text-align: center;">Balance</th>
					<th style="text-align: center;">Current Stock</th>
					<th style="text-align: center;">Diff</th>
					
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
    </section>

    <script>
        $(document).ready(function() {
            // Initialize DataTables
            var table = $('#stockTable').DataTable({
                "ajax": {
                    "url": "ajax_stock_verify.php",
                    "type": "POST",
                    "dataSrc": ""
                },
                "columns": [
                    { "data": "sno", "className": "center-align" },
                    { "data": "itemid", "className": "center-align" },
                    { "data": "itemname" },
                    { "data": "total_purchase", "className": "center-right" },
                    { "data": "total_issue", "className": "center-align" },
                    { "data": "balance", "className": "right-align" },
					{ "data": "current_stock", "className": "right-align" },
					{ "data": "diff_data", "className": "right-align" },
					
                ],
                "dom": '<"top"fB>rt<"bottom"lp><"clear">',
                "buttons": [{
                    extend: 'excelHtml5',
                    className: 'btn btn-primary',
                    text: '<i class="fa fa-file-excel">download</i>',
                    titleAttr: 'Export to Excel',
                    title: 'StockVerificationData'
                }],
            });
			 

           
      });
    </script>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
	
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	
	
	
</body>
</html>