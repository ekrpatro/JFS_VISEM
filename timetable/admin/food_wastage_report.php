<!DOCTYPE html>
<?php
//include("admindbconn.php");
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
session_start();
include("admindbconn.php");
// Session validation
if (!isset($_SESSION['user_name']) || $_SESSION["role"] != 'ADMIN') {
    echo "Invalid User";
    exit(0);
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wastage Food Entry Form</title>
	<link type="text/css" rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
		#top_div
		{
			 max-width: 1250px;
			 background: olive;
			 padding: 20px;
		}
		#din_div
		{
			 max-width: 1250px;
			 background: skyblue;
			 padding: 20px;
		}
        .container {
            max-width: 1500px;
            margin: auto;
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 10px;
            font-weight: bold;
        }

        input, select {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
        }

        button {
            padding: 10px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
			max-width: 100px;
        }

        button:hover {
            background: #0056b3;
        }

        .report_div {
            background-color: #d4edda; /* Light green background */
            padding: 20px;
            border-radius: 4px;
            margin-top: 20px;
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
		.footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }
		
		#bar td {
			# border:solid 4px rgb(60, 179, 113);
			padding: 5px;
		}
		.table-button {
            display: inline-block;
            padding: 8px 12px;
            font-size: 14px;
            color: white;
            background-color: #007BFF;
            border: none;
            border-radius: 4px;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .table-button:hover {
            background-color: #0056b3;
        }

        .table-button:active {
            background-color: #004080;
        }
    </style>
</head>
<body>
	<?= include('./menu.php') ?>
    <div class="container">
		<h1>Dining Wastage Entry Form</h1>
        <form id="diningForm">
			<div id='din_div'>
				<table>
					<tr>
						<th>
							Date
						</th>
						<th>
							Dining Hall
						</th>
						<th>
							BreakFast Wastage <br>weight(Kg.)
						</th>
						<th>
							Lunch Wastage <br>weight(Kg.)
						</th>
						<th>
							Snacks Wastage <br>weight(Kg.)
						</th>
						<th>
							Dinner Wastage <br>weight(Kg.)
						</th>
											
						
					</tr>
					<tr>
						<td>
						<input type="date" id="din_date" name="din_date" value="<?php echo date('Y-m-d'); ?>" required>

						</td>
						<td>
							<select id="din_hall_no" name="din_hall_no" required>					
								<option value="1">Dining Hall - I</option>
								<option value="2">Dining Hall - II</option>
							</select>
						</td>
						<td>
							<input type="number"  step="0.01" id="breakfast" name="breakfast" required>
						</td>
						<td>
						<input type="number"  step="0.01" id="lunch" name="lunch" required>
						</td>
						<td>
						<input type="number" step="0.01" id="snacks" name="snacks" required>
				
						</td>
						<td>
						<input type="number" step="0.01" id="dinner" name="dinner" required>
				
						</td>
					</tr>
					<tr>
						<td colspan=6 align=center> <button type="submit"> Submit</button></td>
					</tr>
				</table>			
				
			</div>
		</form>
        <h1>Non Utilized Cooked Food Entry Form</h1>
        <form id="wastageForm">
			<div id='top_div'>
				<label for="wastage_date"> Date</label>
				<input type="date" id="wastage_date" name="wastage_date" value="<?php echo date('Y-m-d'); ?>" required>

				<label for="issue_category">Issue Category</label>
				<select id="issue_category" name="issue_category" required>
					<option value="">Issue category</option>
					<option value="A">ALL</option>
					<option value="B">Breakfast</option>
					<option value="L">Lunch</option>
					<option value="S">Snacks</option>
					<option value="D">Dinner</option>
				</select>

				<label for="food_name">Food Name</label>
				<input type="text" id="food_name" name="food_name" required>
				<label for="food_weight">Food Weight</label>
				<input type="text" id="food_weight" name="food_weight" required>
			</div>
            <div id="itemSection">
                <h2>Items</h2>
				
                <div class="item">
                    
                    <select id="item_id" name="item_id[]" class="itemname_price" required>
                        <option value="">Select Item</option>
                        <?php
                         $itemQuery = "SELECT i.id, i.itemname, s.unit_price,i.brand_name FROM item i INNER JOIN stock s ON s.itemid=i.id WHERE i.id > 0 ORDER BY itemname";
                        $itemResult = mysqli_query($dbconn, $itemQuery);
                        if ($itemResult) {
                            while ($itemRow = mysqli_fetch_array($itemResult)) {
                                echo "<option value='{$itemRow['id']}:{$itemRow['unit_price']}'>{$itemRow['itemname']}- {$itemRow['brand_name']}</option>";
                            }
                        } else {
                            echo "<option value=''>No items found</option>";
                        }
                        ?>
                    </select>

                    
                    <input type="number" step="0.01" id="unit_price" name="unit_price[]" placeholder='unit_price' class="unitPrice" size=5  readonly required>

                   
                    <input type="number" step="0.01" id="quantity" name="quantity[]" placeholder='quantity' class="quantity" size=5 required>
                    <input type="number" step="0.01" class="totalPrice" placeholder="Total Price"  size=5 readonly>

                    <button type="button" class="deleteItem"><i class="fas fa-trash-alt"></i> Remove Item</button>
                </div>
            </div>
			<div class='footer'>
				<button type="button" id="addItem" class="smallButton"><i class="fas fa-plus"></i> Add Item</button>
				<button type="submit" class="smallButton"><i class="fas fa-check"></i> Submit</button>
			</div>
        </form>
        <div class='report_div'>

			<table>
				<tr id="bar">
					<td>Start Date <input type="date" name="start_date" id="start_date" value="<?php echo date('Y-m-d'); ?>" class='form-control'></td>					
					<td>End Date <input type="date" name="end_date" id="end_date" value="<?php echo date('Y-m-d'); ?>" class='form-control'></td>
				    <td>
						<select id='report_type' class='form-control'>
						<option value='without_price'>Without Price</option>
							<option value='with_price'>With Price</option>
							
						</select>
					</td>
				    
					<td><input type="button" class="generate-report-button" onclick="generateReport()" value="Daily Report"></td>
					<td><input type="button" class="generate-report-button" onclick="total_wastage()" value="Between Dates"></td>
					<td><input type="button" name="button" value="Print" onClick="printDiv()"/></td>
					<td><a href='fwe.php' class="table-button" >EditDelete</a>
					</td>
				</tr>
			</table>
          
        </div>
		
		<div id="output" >
		</div>
		
		
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $(".itemname_price").select2();

            $("#addItem").on("click", function () {
                let newItem = `
                    <div class="item">
                        
                        <select id="item_id" name="item_id[]" class="itemname_price" required>
                            <option value="">Select Item</option>
                            <!-- PHP to populate items -->
                            <?php
                           $itemQuery = "SELECT i.id, i.itemname, s.unit_price,i.brand_name FROM item i INNER JOIN stock s ON s.itemid=i.id WHERE i.id > 0 ORDER BY itemname";
                            $itemResult = mysqli_query($dbconn, $itemQuery);
                            if ($itemResult) {
                                while ($itemRow = mysqli_fetch_array($itemResult)) {
                                    echo "<option value='{$itemRow['id']}:{$itemRow['unit_price']}'>{$itemRow['itemname']} - {$itemRow['brand_name']} </option>";
                                }
                            } else {
                                echo "<option value=''>No items found</option>";
                            }
                            ?>
                        </select>

                        
                        <input type="number" step="0.01" id="unit_price" name="unit_price[]" placeholder='unitPrice' class="unitPrice" size=5  readonly required>

                        
                        <input type="number" step="0.01" id="quantity" name="quantity[]" placeholder='Quantity' class="quantity" size=5  required>
                        <input type="number" step="0.01" class="totalPrice" placeholder="Total Price" size=5  readonly>

                        <button type="button" class="deleteItem"><i class="fas fa-trash-alt"></i> Remove Item</button>
                    </div>
                `;
                $("#itemSection").append(newItem);
                $(".itemname_price").select2();
            });

            $(document).on("click", ".deleteItem", function () {
                $(this).closest(".item").remove();
            });

            $(document).on("input", ".unitPrice, .quantity", function () {
                let item = $(this).closest(".item");
                let unitPrice = parseFloat(item.find(".unitPrice").val()) || 0;
                let quantity = parseFloat(item.find(".quantity").val()) || 0;
                let totalPrice = unitPrice * quantity;
                item.find(".totalPrice").val(totalPrice.toFixed(2));
            });

            $(document).on("change", ".itemname_price", function () {
                let selectedValue = $(this).val();
                if (selectedValue) {
                    let parts = selectedValue.split(":");
                    let unitPrice = parseFloat(parts[1]) || 0;
                    $(this).closest(".item").find(".unitPrice").val(unitPrice.toFixed(2));
                } else {
                    $(this).closest(".item").find(".unitPrice").val("");
                }
            });

            $("#wastageForm").on("submit", function (event) {
                event.preventDefault();
                let cnfrm = confirm("Confirm");
                if (!cnfrm) {
                    return false;
                }

                let formData = {
                    wastage_date: $("#wastage_date").val(),
                    issue_category: $("#issue_category").val(),
                    food_name: $("#food_name").val(),
					food_weight: $("#food_weight").val(),
					action: 'insert',
                    items: []
                };

                $(".item").each(function () {
                    let item = {
                        itemName: $(this).find(".itemname_price").val(),
                        unitPrice: parseFloat($(this).find(".unitPrice").val()) || 0,
                        quantity: parseFloat($(this).find(".quantity").val()) || 0,
                        totalPrice: parseFloat($(this).find(".totalPrice").val()) || 0
                    };
                    formData.items.push(item);
					
                });

                $.ajax({
                    url: 'ajax_wastage.php',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(formData),
                    success: function (response) {
                        let data = JSON.parse(response);
                        if (data.success) {
                            alert(data.msg);
                            clearItemSection();
                            resetForm();
                        } else {
                            alert(data.msg);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);
                        alert("Error: " + textStatus + " " + errorThrown);
                    }
                });
            });
			
			//dining waste submit starts
			$("#diningForm").on("submit", function (event) {
                event.preventDefault();
                let cnfrm = confirm("Confirm");
                if (!cnfrm) {
                    return false;
                }

                let formData = {
                    din_date: $("#din_date").val(),
                    din_hall_no: $("#din_hall_no").val(),
                    breakfast: $("#breakfast").val(),
					lunch: $("#lunch").val(),
					snacks: $("#snacks").val(),
					dinner: $("#dinner").val(),
					action: 'insert_din_waste'
                    
                };                

                $.ajax({
                    url: 'ajax_wastage.php',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(formData),
                    success: function (response) {
                        let data = JSON.parse(response);
                        if (data.success) {
                            alert(data.msg);
                            clearItemSection();
                            resetForm();
                        } else {
                            alert(data.msg);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);
                        alert("Error: " + textStatus + " " + errorThrown);
                    }
                });
            })
			
			//dining waste submit ends
        });
		document.getElementById('start_date').addEventListener('change', function() {
        
        document.getElementById('end_date').value = this.value;
    });
	function resetForm() {
		$("#wastageForm")[0].reset();
	}
	 function clearItemSection() {
       $("#itemSection").empty();
		// Add back the initial item entry
		$("#itemSection").html(`
			<h2>Ingredients</h2>
			<div class="item">
				<input type="text" class="serialNumber" value="1" readonly>
				<select class="itemname_price" required>
					<option value="">Select Item</option>
					<?php
					$itemQuery = "SELECT i.id, i.itemname, s.unit_price,i.brand_name FROM item i INNER JOIN stock s ON s.itemid=i.id WHERE i.id > 0 ORDER BY itemname";
					$itemResult = mysqli_query($dbconn, $itemQuery);
					if ($itemResult) {
						while ($itemRow = mysqli_fetch_array($itemResult)) {
							echo "<option value='{$itemRow['id']}:{$itemRow['unit_price']}'>{$itemRow['itemname']} - {$itemRow['brand_name']} - ({$itemRow['unit_price']})</option>";
						}
					} else {
						echo "<option value=''>No items found</option>";
					}
					?>
				</select>
				<input type="number" step="0.01" class="unitPrice" placeholder="Unit Price" readonly required>
				<input type="number" step="0.01" class="quantity" placeholder="Quantity" required>
				<input type="number" step="0.01" class="totalPrice" placeholder="Total Price" readonly>
				<button type="button" class="deleteItem"><i class="fas fa-trash-alt"></i></button>
			</div>
		`);
		$(".itemname_price").select2(); // Reinitialize Select2 for the new item entry
    }
		function total_wastage(){
			let formData = {
                start_date: $("#start_date").val(),
                end_date: $("#end_date").val(),
				report_type: $("#report_type").val()
            };

            $.ajax({
                url: 'generate_tow_report.php',
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
        function generateReport() {
            let formData = {
                start_date: $("#start_date").val(),
                end_date: $("#end_date").val(),
				report_type: $("#report_type").val()
            };

            $.ajax({
                url: 'generate_ufw_report.php',
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
    </script>
</body>
</html>
