<!DOCTYPE html>
<?php 
//include("admindbconn.php");
session_start();
include("admindbconn.php");
// Session validation
if (!isset($_SESSION['user_name']) || $_SESSION["role"] != 'ADMIN') {
    echo "Invalid User";
   // exit(0);
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multiple Record Insert Purchase Record</title>
    <!-- <link rel="stylesheet" href="styles.css">-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/menu.css">
	
	<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

	
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        padding: 20px;
    }

    .container {
        max-width: 1050px;
        margin: auto;
        background: white;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h1,
    h2 {
        text-align: center;
    }

    form {
        display: flex;
        flex-direction: column;
    }

    label {
        margin: 10px 0 5px;
    }

    input,
    select {
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    button {
        padding: 10px;
        background: #007BFF;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
    }

    button i {
        margin-right: 5px;
    }

    button:hover {
        background: #0056b3;
    }

    .smallButton {
        padding: 5px 10px;
        font-size: 0.9em;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th,
    td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .item input {
        margin: 0 5px;
        flex: 1;
    }

    .deleteItem {
        background: #dc3545;
        color: white;
        padding: 5px 10px;
        font-size: 0.9em;
        margin: 0 5px;
    }

    .deleteItem:hover {
        background: #c82333;
    }
	#div_grand_tot {
    text-align: right;
    margin-top: 20px;
}
#spangrandTotal {
    font-weight: bold; /* Apply bold font weight */
}
  .form-control {
		font-size: 1.6em;
        width: 70%; /* Ensure the select element takes up full width of its container */
        box-sizing: border-box; /* Includes padding and border in the element's total width and height */
    }

    .form-control option {
		font-weight: bold;
		font-size: 1.6em;
        white-space: nowrap; /* Prevents text from wrapping to the next line */
        overflow: hidden; /* Hides overflow text */
        text-overflow: ellipsis; /* Displays ellipsis (...) for overflow text */
    }

    /* Optional: Adjust width for select elements specifically */
    #bill_id {
		font-weight: bold;
        min-width: 250px; /* Adjust as needed */
    }
	#invoice_number {
        max-width: 250px; /* Adjust as needed */
		font-size: 1.6em;
    }
	#purchase_date {
    width: 100%; /* Makes the input fill its container's width */
    max-width: 250px; /* Adjust this value as needed */
	 font-size: 1.6em; /* Increases the font size (adjust as needed) */
    padding: 10px;
}
	 .form-group.row {
        background-color: #d9ecd0; /* Light gray background color */
        border: 1px solid #ddd; /* Light gray border */
        border-radius: 8px; /* Rounded corners */
        padding: 20px; /* Spacing inside the box */
        margin-bottom: 20px; /* Space below the section */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
    }

    .form-group.row label {
        font-weight: bold; /* Make labels bold for emphasis */
        display: block; /* Ensure labels are on their own lines */
        margin-bottom: 5px; /* Space between label and input */
    }

    .form-group.row select,
    .form-group.row input {
		font-weight: bold;
        width: 70%; /* Full width of the container */
        box-sizing: border-box; /* Includes padding and border in the element's total width */
        padding: 10px; /* Space inside the input/select */
        margin-bottom: 15px; /* Space below inputs/selects */
        border: 2px solid #ccc; /* Light border for inputs/selects */
        border-radius: 4px; /* Slightly rounded corners */
    }
	.button-container {
    display: flex;
    justify-content: center; /* Centers buttons horizontally */
    gap: 10px; /* Space between buttons */
    margin-top: 20px; /* Space above the buttons */
}

.smallButton {
    padding: 10px 15px; /* Consistent padding for buttons */
    font-size: 1em; /* Adjust font size as needed */
    border: none;
    border-radius: 4px;
    cursor: pointer;
    background-color: #007BFF; /* Blue background color */
    color: white;
    transition: background-color 0.3s, box-shadow 0.3s;
}

.smallButton:hover {
    background-color: #0056b3; /* Darker blue for hover effect */
}

.smallButton:active {
    background-color: #004085; /* Even darker blue when pressed */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2); /* Shadow for active effect */
}
.submitButton {
    background-color: #007bff; /* Blue background for "Submit" */
    color: white;
}


</style>
</head>

<body>
    <?php include('./menu.php'); ?>
    <div class="container">
        <h1>Multiple Insert Purchase Record</h1>
        <form id="supplierForm">
            
			<!-- start -->
			<div class="form-group row">
				<div class='col-md-8'>
					<label for="bill_id">Select Bill</label>
					<?
								
					?>
					<select id="bill_id" name="bill_id" class="form-control" required>
						<?php
						$bill_q="SELECT `id`, `purchase_date`, `seller_id`, `item_category`, `invoice_no`, `total_amount` FROM `purchase_bills` order by id desc";
						$bill_rs = mysqli_query($dbconn, $bill_q);

						if ($bill_rs) {
							while ($bill_row = mysqli_fetch_array($bill_rs)) { ?>
								<option value="<?php echo $bill_row['invoice_no'].":".$bill_row['purchase_date'].":".$bill_row['total_amount'].":".$bill_row['seller_id']; ?>"><?php echo 'Date: '.$bill_row['purchase_date'].', Invoice_no: '.$bill_row['invoice_no'].", Total Bill:".$bill_row['total_amount']; ?></option>
						<?php }
						} else {
							echo "<option value=''>No Bills found</option>";
						}
						?>
					</select>
				</div>
				<br>
				<div class="col-md-4">
					<label for="invoice_number">Invoice Number</label>
					<input type="text" id="invoice_number" name="invoice_number" class="form-control"  readonly required>
				</div>
				<div class="col-md-4">
					<label for="supplierName">Supplier Name:</label>
					<select id="supplierName" name="supplierName" class="form-control"  readonly required>
						<?php
						$query = "SELECT seller_id, shopname FROM seller order by seller_id"; // Corrected SQL query
						$result1 = mysqli_query($dbconn, $query);

						if ($result1) {
							while ($row1 = mysqli_fetch_array($result1)) { ?>
								<option value="<?php echo $row1['seller_id']; ?>"><?php echo $row1['shopname']; ?></option>
						<?php }
						} else {
							echo "<option value=''>No suppliers found</option>";
						}
						?>
					</select>
				</div>
				<br>
				<div class="col-md-4">
					<label for="total_bill">Total Bill:</label>
					<input type="text" id="total_bill" name="total_bill" class="form-control" value="" required readonly >
				</div>
				<div class="col-md-4">
					<label for="purchase_date">Purchase Date:</label>
					<input type="date" id="purchase_date" name="purchase_date" class="form-control" value="<?= date('Y-m-d') ?>" required readonly >
				</div>
			</div>

			<!--- end -->

            <div id="itemSection">
                <h2>Items</h2>
                <div class="item">
                    <input type="text" class="serialNumber" readonly>
                    <!--<input type="text" class="itemName" placeholder="Item Name" required>-->
                    <select class="itemName" required>
                        <option value="">Select Item</option>
                        <?php
                        $itemQuery = "SELECT id, itemname FROM item order by itemname";
                        $itemResult = mysqli_query($dbconn, $itemQuery);

                        if ($itemResult) {
                            while ($itemRow = mysqli_fetch_array($itemResult)) { ?>
                                <option value="<?php echo $itemRow['id']; ?>"><?php echo $itemRow['itemname']; ?></option>
                        <?php }
                        } else {
                            echo "<option value=''>No items found</option>";
                        }
                        ?>
                    </select>
                    <input type="number" step="0.01" class="unitPrice" placeholder="Unit Price" required>
                    <input type="number" step="0.01" class="quantity" placeholder="Quantity" required>
                    <input type="number" step="0.01" class="totalPrice" placeholder="Total Price" readonly>
                    <button type="button" class="deleteItem"><i class="fas fa-trash-alt"></i></button>
                </div>
            </div>
			<div id='div_grand_tot'>
				   Grand Total: <span id="spangrandTotal" style='padding:5px; background-color:yellow;'>0.00</span>
    
				
			</div>
			<!--<div id='bottom' class='justify-content'>
				<button type="button" id="addItem" class="smallButton"><i class="fas fa-plus"></i> Add Item</button>
				<button type="submit" class="smallButton"><i class="fas fa-check"></i> Submit</button>
			</div>
			-->
			<div id='bottom' class='button-container'>
				<button type="button" id="addItem" class="smallButton"><i class="fas fa-plus"></i> Add Item</button>
				<button type="submit" class="smallButton submitButton"><i class="fas fa-check"></i> Submit</button>
			</div>
        </form>

        <h2>Purchase List</h2>
        <table id="supplierTable">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Invoice Number</th>
                    <th>Supplier Name</th>
                    <th>Purchase Date</th>
                    <th>Item Name</th>
					<th>Quantity</th>
                    <th>Unit Price</th>
                    
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                <!-- Supplier records will be added here dynamically -->
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <!--<script src="scripts.js"></script>-->
</body>
<script>
    $(document).ready(function() {
        $('#bill_id').select2({
            placeholder: "Select a Bill",
            allowClear: true
        });
		$('#supplierName').select2({
            placeholder: "Select a supplier",
            allowClear: true
        });
        $('.itemName').select2({
            placeholder: "Select an Item",
            allowClear: true
        });
		 $('#bill_id').on('change', function() {
            var selectedValue = $(this).val();
            var invoiceNo = selectedValue.split(':')[0]; // Get invoice_no from the selected value
			var purch_date = selectedValue.split(':')[1]; // Get purchase date from the selected value
			var tot_bil_amt = selectedValue.split(':')[2]; // Get total_amount` date from the selected value
			var shop_id = selectedValue.split(':')[3]; // Get total_amount` date from the selected value
			$('#invoice_number').val(invoiceNo);
            $('#purchase_date').val(purch_date); // Update the invoice_number input field
			$('#total_bill').val(tot_bil_amt); // Update the invoice_number input field
			//$('#supplierName').propup(shop_id); // Update the invoice_number input field
			$('#supplierName').val(shop_id).trigger('change');
			
        });

        function createNewItem(serialNumber) {
            return `
            <div class="item">
                <input type="text" class="serialNumber" value="${serialNumber}" readonly>
                <select class="itemName" required>
                    <option value="">Select Item</option>
                    <?php
                    $itemQuery = "SELECT id, itemname,brand_name FROM item order by itemname";
                    $itemResult = mysqli_query($dbconn, $itemQuery);

                    if ($itemResult) {
                        while ($itemRow = mysqli_fetch_array($itemResult)) { ?>
                            <option value="<?php echo $itemRow['id']; ?>"><?php echo $itemRow['itemname']." - ".$itemRow['brand_name']; ?></option>
                    <?php }
                    } else {
                        echo "<option value=''>No items found</option>";
                    }
                    ?>
                </select>
				<input type="number" class="quantity" step="0.01" placeholder="Quantity" required>
                <input type="number" class="unitPrice" step="0.01" placeholder="Unit Price" required>
                
                <input type="number" class="totalPrice" step="0.01" placeholder="Total Price" readonly>
                <button type="button" class="deleteItem"><i class="fas fa-trash-alt"></i></button>
            </div>
        `;
        }

        function calculateTotalPrice(unitPrice, quantity,item) {
			/* if ($(this).closest(".item").find(".itemName").val() !== "")
				return (unitPrice * quantity).toFixed(2);
			else
			{
				alert("please select item name");
			}*/
			if ($(item).find(".itemName").val() !== "") 
			{
				return (unitPrice * quantity).toFixed(2);
			} 
			else 
			{
				$(item).find(".quantity").val(0);
				alert("Please select an item name");
				return 0; // Return 0 to prevent incorrect calculations
			}
        }

        function updateSerialNumbers() {
            $(".serialNumber").each(function(index) {
                $(this).val(index + 1);
            });
        }

        function resetForm() {
            $("#supplierForm")[0].reset();
            $("#itemSection").html(`
            <h2>Items</h2>
            ${createNewItem(1)}
        `);
            $('#supplierName').val(null).trigger('change');
        }

        function updateGrandTotal() {
            let spangrandTotal = 0.0;
            $(".totalPrice").each(function() {
                spangrandTotal += parseFloat($(this).val()) || 0;
            });
            $("#spangrandTotal").text(spangrandTotal.toFixed(2));
        }

        $("#addItem").on("click", function() {
            let itemCount = $(".item").length;
            $("#itemSection").append(createNewItem(itemCount + 1));
            $(".itemName").select2();
			
        });

        $(document).on("input", ".unitPrice, .quantity", function() {
            let item = $(this).closest(".item");
            let unitPrice = parseFloat(item.find(".unitPrice").val()) || 0;
            let quantity = parseFloat(item.find(".quantity").val()) || 0;
            let totalPrice = calculateTotalPrice(unitPrice, quantity,item);
            item.find(".totalPrice").val(totalPrice);
            updateGrandTotal();
        });

        $(document).on("click", ".deleteItem", function() {
            $(this).closest(".item").remove();
            updateSerialNumbers();
            updateGrandTotal();
        });

        function clearSubmittedList() {
            $("#supplierTable tbody").empty();
        }

        $("#supplierForm").on("submit", function(event) {
            event.preventDefault();

            let supplierName = $("#supplierName").val();
            let invoice_number = $("#invoice_number").val();
            let purchase_date = $("#purchase_date").val();
			
            let grand_total = 0.0;

            let itemCount = $(".item").length;
            let msg_data = `
                <div><strong>Invoice Number:</strong> ${invoice_number}</div>
                <div><strong>Total Items:</strong> ${itemCount}</div>
                <div><strong>Purchase Date:</strong> ${purchase_date}</div>
                <div><strong>Grand Total:</strong> ${$("#spangrandTotal").text()}</div>
            `;

            Swal.fire({
                title: 'Confirm Submission',
                html: msg_data,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Submit',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proceed with form submission
                    let items = [];
                    $(".item").each(function() {
                        let item = {
                            serialNumber: $(this).find(".serialNumber").val(),
                            itemName: $(this).find(".itemName").val(),
                            unitPrice: parseFloat($(this).find(".unitPrice").val()) || 0,
                            quantity: parseFloat($(this).find(".quantity").val()) || 0,
                            totalPrice: parseFloat($(this).find(".totalPrice").val()) || 0
                        };

                        if (item.itemName && item.unitPrice && item.quantity) {
                            items.push(item);
                            grand_total += item.totalPrice;
                        }
                    });

                    $.ajax({
                        url: 'ajax_insert_purchase.php',
                        type: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({
                            supplierName: supplierName,
                            invoice_number: invoice_number,							
                            purchase_date: purchase_date,
                            items: items
                        }),
                        success: function(response) {
                            let data = JSON.parse(response);
                            if (data.success) {
                                Swal.fire('Success!', data.msg, 'success');
                                clearSubmittedList();
                                items.forEach(function(item) {
                                    let newRow = `
                                    <tr>
                                        <td>${item.serialNumber}</td>
                                        <td>${invoice_number}</td>
                                        <td>${supplierName}</td>
                                        <td>${purchase_date}</td>
                                        <td>${item.itemName}</td>
										<td>${item.quantity}</td>
                                        <td>${item.unitPrice}</td>
                                        
                                        <td>${item.totalPrice.toFixed(2)}</td>
                                    </tr>
                                    `;
                                    $("#supplierTable tbody").append(newRow);
                                });

                                let last_row = `
                                <tr>
                                    <td colspan="7">Grand Total</td>
                                    <td>${grand_total.toFixed(2)}</td>
                                </tr>
                                `;
                                $("#supplierTable tbody").append(last_row);

                                resetForm();
                            } else {
                                Swal.fire('Error!', data.msg, 'error');
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                            Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                        }
                    });
                } else {
                    // Handle cancellation (no action needed, just return)
                    return;
                }
            });
        });

        // Initialize the form with one item
        resetForm();
    });
</script>

</html>
