<!DOCTYPE html>
<? 
session_start();
//include("admindbconn.php");
// Session validation
if (!isset($_SESSION['user_name']) || $_SESSION["role"] != 'ADMIN') {
    echo "Invalid User";
    exit(0);
}
error_reporting(E_ALL);
ini_set('display_errors', 1);?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>StockRegister</title>
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


	 <div class="header-container">
        <h1>Current Stock Register</h1>
        <div class="btn-group">
			<a href="check_stock.php"  class="btn btn-info">Verify Stock</a>
            <a href="datewise_stock.php"  class="btn btn-info">View Datewise Stock</a>
			<a href="itemwise_stock.php"  class="btn btn-info">Itemwise Stock</a>
			<!--<a href="issuewise_edit_delete.php"  class="btn btn-info">EditIssue</a>-->
			<a href="item_register.php" class="btn btn-secondary">Item Register/Add New Item</a>
            <button type="button" id="gen_po_btn" class="btn btn-primary">Generate PO</button>
			<a href="item_price_list.php"  class="btn btn-info">PriceList</a>
			
        </div>
    </div>
    <section class="main__stock">
    <h1 style="text-align: center;">Current Stock Register </h1>
		

        <table id="stockTable" style="width: 1050px;" class="display">
			<thead>
				<tr>
					<th style="text-align: center;">S.No</th>
					<th style="text-align: center;">Item id</th>
					<th style="text-align: left;">Item name</th>
					<th style="text-align: center;">Price/unit</th>
					<th style="text-align: center;">Quantity</th>
					<th style="text-align: center;">Total Price</th>
					<th style="text-align: center;">Minimum Stock</th>
					<th style="text-align: center;">StockWarning</th>
					<th style="text-align: center;">Purchase Details</th>
					<th style="text-align: center;">Issue Details</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
    </section>
	<div class="modal fade" id="poModal" tabindex="-1" role="dialog" aria-labelledby="poModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">   
                <h5 class="modal-title" id="poModalLabel">PO</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="output" class="mt-3" style="margin: auto 20%; width:80%;"></div>
            </div>
			<div class="modal-footer">
				<input type="button" id="print_btn" name="button" value="Print" class="btn btn-primary" />
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





    <script>
        $(document).ready(function() {
            // Initialize DataTables
            var table = $('#stockTable').DataTable({
                "ajax": {
                    "url": "ajax_fetch_stock_data.php",
                    "type": "POST",
                    "dataSrc": ""
                },
                "columns": [
                    { "data": "sno", "className": "center-align" },
                    { "data": "itemid", "className": "center-align" },
                    { "data": "itemname" },
                    { "data": "unit_price", "className": "center-right" },
                    { "data": "quantity", "className": "center-align" },
                    { "data": "total_price", "className": "right-align" },
					{ "data": "min_stock_quantity", "className": "right-align" },
					{ "data": "min_stock_warning", "className": "right-align" },
					{
						"data": "itemid",
						"render": function(data, type, row) {
							//return '<a href="#" class="view-purchase" data-id="' + data + '">View Purchases</a>';
							  return '<a href="#" class="view-purchase" data-id="' + data + '" data-itemname="' + row.itemname + '"><i class="fas fa-eye"></i>Purchases</a>';
						},
						"className": "center-align"
					},
					{
						"data": "itemid",
						"render": function(data, type, row) {
							return '<a href="#" class="view-issue" data-id="' + data + '" data-itemname="' + row.itemname + '"><i class="fas fa-eye"></i>Issues</a>';
						},
						"className": "center-align"
					}
                ],
                "createdRow": function(row, data, dataIndex) {
                    // Highlight row if quantity is 0
                    /*if (parseFloat(data.quantity) == 0.0) {
                        $(row).addClass('highlight');
                    }*/
					if (data.min_stock_warning == 'MinimumStock') {
                        $(row).addClass('highlight');
                    }
                },
                "dom": '<"top"fB>rt<"bottom"lp><"clear">',
                "buttons": [{
                    extend: 'excelHtml5',
                    className: 'btn btn-primary',
                    text: '<i class="fa fa-file-excel">download</i>',
                    titleAttr: 'Export to Excel',
                    title: 'StockData'
                }],
            });
			 $('#stockTable').on('click', '.view-purchase', function(e) {
				e.preventDefault();
				var itemId = $(this).data('id');
				var itemName = $(this).data('itemname'); // Get the item name
				$.ajax({
					type: 'POST',
					url: 'ajax_fetch_stock_data.php',
					data: { action: 'get_purchase_details', item_id: itemId },
					success: function(response) {
						// Display the purchase details in a modal or other container
						//$('#detailsModal .modal-body').html(response);
						  $('#detailsModal .modal-body').html('<h3>Purchase Details</h3><hr><h4>Item Name:<span style="color: red;"> ' + itemName + '</span></h4><hr>' + response);
          
						$('#detailsModal').modal('show');
					},
					error: function(jqXHR, textStatus, errorThrown) {
						console.log(textStatus, errorThrown);
						alert("Error: " + textStatus + " " + errorThrown);
					}
				});
			});

			// Event listener for view issue details link
			$('#stockTable').on('click', '.view-issue', function(e) {
				e.preventDefault();
				var itemId = $(this).data('id');
				var itemName = $(this).data('itemname'); // Get the item name
				$.ajax({
					type: 'POST',
					url: 'ajax_fetch_stock_data.php',
					data: { action: 'get_issue_details', item_id: itemId },
					success: function(response) {
						// Display the issue details in a modal or other container
						$('#detailsModal .modal-body').html('<h3>Issue Details</h3><hr><h4>Item Name:<span style="color: red;"> ' + itemName + '</span></h4><hr>' +response);
						$('#detailsModal').modal('show');
					},
					error: function(jqXHR, textStatus, errorThrown) {
						console.log(textStatus, errorThrown);
						alert("Error: " + textStatus + " " + errorThrown);
					}
				});
			});

            // Handle form submission
            $('#searchForm').on('submit', function(e) {
                e.preventDefault();
                var searchItem = $('#searchItem').val();
                table.ajax.url("ajax_fetch_stock_data.php?searchitem=" + searchItem).load();
            });
			 
			$('#gen_po_btn').click(function() {
				$('#poModal').modal('show');

				// Gather form data
				const formData = {
					po_date: '<?=date('Y-m-d')?>',										
					action: 'gen_po',					
				};
				// AJAX request
				$.ajax({
					type: 'POST',
					url: 'ajax_fetch_stock_data.php', // Change this to your PHP file path
					data: formData,
					xhrFields: {
                        responseType: 'html'
                    },
					success: function(html) {
                        alert('PO Report');
                        window.document.getElementById("output").innerHTML = html;
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);
                        alert("Error: " + textStatus + " " + errorThrown);
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

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
	
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	
	
	
</body>
</html>