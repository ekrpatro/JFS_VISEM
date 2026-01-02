<!DOCTYPE html>
<?php include("admindbconn.php") ?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Item Price List</title>
  
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <!-- DataTables CSS -->
    
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
   
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <?= include('./menu.php') ?>

    <div class="container mt-5">
        <h2>Item Price List </h2>

        <div class="form-group row">
            
            <div class="col-md-2">
                <label for="item_category">Select ItemCategory :</label>
                <select class="form-control" id="item_category" name="item_category" required>
                    <option value="0">All</option>
                    <?php
                    $itemQuery = "SELECT distinct item_category FROM item";
                    $itemResult = mysqli_query($dbconn, $itemQuery);

                    if ($itemResult) {
                        while ($itemRow = mysqli_fetch_array($itemResult)) { ?>
                            <option value="<?=$itemRow['item_category']; ?>"><?=$itemRow['item_category'] ; ?></option>
                        <?php }
                    } else {
                        echo "<option value=''>No items category found</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">  <!-- show_bill_btn -->
                <button type="button" id='show_price_btn' class="btn btn-primary">Show Price List</button>
            </div>          
			
            
        </div>
		 
        
        <!-- Modal -->
        
		

       
    </div>
    <div id="output" class="mt-3" style="margin: auto 20%; width:80%;">
	
	</div>
    
    <!-- Centered print button -->
    <div class="text-center mt-3">
        <input type="button" id="print_btn" name="button" value="Print" class="btn btn-primary" />
    </div>

    <script>
        $(document).ready(function() {                 

        
            
            // Datewise Bill starts
            $("#show_price_btn").click(function() {
                if ($('#item_category').val() === '' ) {
                    Swal.fire({
                        title: 'Please select item category',
                        icon: 'error',
                    });
                    return false;
                }
                
               // dataTable.clear().draw();

                let formData = {                    
                    item_category: $("#item_category").val(),
                    action: 'print_pricelist',
                };

                $.ajax({
                    url: 'ajax_item_price.php',
                    type: 'POST',
                    data: formData,
                    xhrFields: {
                        responseType: 'html'
                    },
                    success: function(html) {
                        alert('Price List Report');
                        window.document.getElementById("output").innerHTML = html;
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);
                        alert("Error: " + textStatus + " " + errorThrown);
                    }
                });
            });
            // Datewise bills ends 
			
			

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
