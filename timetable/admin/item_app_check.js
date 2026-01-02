$(document).ready(function() {
    let itemTable = $('#itemTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ],
        columns: [
            { title: "ID" },
            { title: "Item Name" },
			{ title: "Brand Name" },
            { title: "Item Category" },
            { title: "Measurement Unit" },
			{ title: "Minimum Stock Quantity" },
            { title: "Display Priority" },
            { title: "Actions" }
        ]
    });

    function fetchItems() {
      
        $.ajax({
            url: 'ajax_item.php',
            type: 'POST',
            data: { action: 'fetch' },
            success: function(response) {
                try {
                    alert(response)
                    const items = JSON.parse(response);
                    if (items.error) {
                        console.error("Error fetching items: " + items.error);
                        return;
                    }
                    itemTable.clear();
                    items.forEach(item => {
                        itemTable.row.add([
                            item.id,
                            item.itemname,
							item.brand_name,
                            item.item_category,
							item.min_stock_quantity,
                            item.measurement_unit,
                            item.disp_priority,
                            `
                                <button class="editBtn" data-id="${item.id}"> Edit</button>
                               <!-- <button class="deleteBtn" data-id="${item.id}">Delete</button> -->
                            `
                        ]).draw(false);
                    });
                } catch (e) {
                    console.error("Error parsing JSON: " + e.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error: " + status + " - " + error);
            }
        });
    }

    fetchItems();
     // Get the modal
     const modal = $("#itemModal");
     const form = $("#itemForm");
 
     // Get the button that opens the modal
     $("#addItemBtn").on("click", function() {
         modal.show(); // Show the modal
         form[0].reset(); // Reset form fields
         form.removeData("id").removeData("action"); // Remove data attributes
     });
      // Get the <span> element that closes the modal
    $(".close").on("click", function() {
        modal.hide(); // Hide the modal
    });
     // Handle the Cancel button
     $(".cancelBtn").on("click", function() {
        modal.hide(); // Hide the modal
        form[0].reset(); // Optionally reset form fields
        form.removeData("id").removeData("action"); // Optionally remove data attributes
    });

    // Close the modal if the user clicks outside the modal content
    $(window).on("click", function(event) {
        if ($(event.target).is("#itemModal")) {
            modal.hide(); // Hide the modal
        }
    });
//min_stock_quantity
    $("#itemForm").on("submit", function(e) {
        e.preventDefault();
        const action = $(this).data("action") || 'add';
        const item_id = $(this).data("id");
        const itemData = {
            action: action,
            item_id: item_id,
            itemname: $("#itemname").val(),
			brand_name: $("#brand_name").val(),
            item_category: $("#item_category").val(),
			min_stock_quantity:$("#min_stock_quantity").val(),
            measurement_unit: $("#measurement_unit").val(),
            disp_priority: $("#disp_priority").val()
        };

        $.ajax({
            url: 'ajax_item.php',
            type: 'POST',
            data: itemData,
            success: function(response) {
                try {
                    
                    const result = JSON.parse(response);
					alert(result);
                    if (result.error) {
                        console.error("Error: " + result.error);
                        return;
                    }
                    alert("Item Inserted..")
                    
                    modal.hide();
                    $("#itemForm")[0].reset();
                    $("#itemForm").removeData("id").removeData("action");
                    fetchItems();
                } catch (e) {
                    console.error("Error parsing JSON: " + e.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error: " + status + " - " + error);
            }
        });
    });

    $(document).on("click", ".deleteBtn", function() {
        const isConfirmed = confirm("Are you sure you want to delete this item?");

        if (isConfirmed)
        {
            const item_id = $(this).data("id");
            $.ajax({
                url: 'ajax_item.php',
                type: 'POST',
                data: { action: 'delete', item_id: item_id },
                success: function(response) {
                    try {
                        const result = JSON.parse(response);
                        if (result.error) {
                            console.error("Error: " + result.error);
                            return;
                        }
                        fetchItems();
                    } catch (e) {
                        console.error("Error parsing JSON: " + e.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX error: " + status + " - " + error);
                }
            });
        }
    });

    $(document).on("click", ".editBtn", function() {
       
        const item_id = $(this).data("id");
        const itemRow = $(this).closest("tr");
        const itemData = itemTable.row(itemRow).data();
        const itemname = itemData[1];
		const brand_name = itemData[2];
        const item_category = itemData[3];
		const min_stock_quantity=itemData[4],
        const measurement_unit = itemData[5];
        const disp_priority = itemData[6];
        

        $("#itemname").val(itemname);
		$("#brand_name").val(brand_name);
        $("#item_category").val(item_category);
		$("#min_stock_quantity").val(min_stock_quantity);
        $("#measurement_unit").val(measurement_unit);		
        $("#disp_priority").val(disp_priority);
        $("#itemForm").data("id", item_id).data("action", "update");
        modal.show();
    });
});