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
			{ title: "Minimum Stock Quantity" },
            { title: "Measurement Unit" },
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
                                <button class="editBtn" data-id="${item.id}">Edit</button>
                                <button class="deleteBtn" data-id="${item.id}">Delete</button>
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

    const modal = $("#itemModal");
    const form = $("#itemForm");

    $("#addItemBtn").on("click", function() {
        modal.show();
        form[0].reset();
        form.removeData("id").removeData("action");
    });

    $(".close").on("click", function() {
        modal.hide();
    });

    $(".cancelBtn").on("click", function() {
        modal.hide();
        form[0].reset();
        form.removeData("id").removeData("action");
    });

    $(window).on("click", function(event) {
        if ($(event.target).is("#itemModal")) {
            modal.hide();
        }
    });

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
            measurement_unit: $("#measurement_unit").val(),
            disp_priority: $("#disp_priority").val(),
            min_stock_quantity: $("#min_stock_quantity").val()
        };
		alert(itemData);
        $.ajax({
            url: 'ajax_item.php',
            type: 'POST',
            data: itemData,
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.error) {
                        console.error("Error: " + result.error);
                        return;
                    }
                    alert("Operation Successful");
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
        if (isConfirmed) {
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
        $.ajax({
            url: 'ajax_item.php',
            type: 'POST',
            data: { action: 'fetch', item_id: item_id },
            success: function(response) {
                try {
                    const items = JSON.parse(response);
                    if (items.error) {
                        console.error("Error fetching item: " + items.error);
                        return;
                    }
                    const item = items[0];
                    $("#itemname").val(item.itemname);
                    $("#brand_name").val(item.brand_name);
                    $("#item_category").val(item.item_category);
                    $("#measurement_unit").val(item.measurement_unit);
                    $("#disp_priority").val(item.disp_priority);
                    $("#min_stock_quantity").val(item.min_stock_quantity);
                    form.data("id", item_id).data("action", 'update');
                    modal.show();
                } catch (e) {
                    console.error("Error parsing JSON: " + e.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error: " + status + " - " + error);
            }
        });
    });
});
