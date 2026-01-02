<!DOCTYPE html>
<?php include("admindbconn.php") ?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>IssueRegister</title>
    <!-- Bootstrap CSS -->
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

        #output {
            padding: 20px;
            border-radius: 4px;
            margin: 50px auto; /* Center vertically and horizontally */
            max-width: 800px; /* Limit the width of the div */
            position: relative; /* Enable positioning context */
            text-align: center; /* Center text inside the div */
            background-color: #f8f9fa; /* Light background color */
            border: 1px solid #dee2e6; /* Light border color */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Optional: Add a subtle shadow */
        }

        #output table {
            margin: 0 auto; /* Center the table within the div */
            width: auto; /* Allow table to take up necessary width */
            border-collapse: collapse; /* Optional: makes table borders appear as single lines */
        }

        #output th,
        #output td {
            padding: 10px; /* Space around table cells */
            border: 1px solid #dee2e6; /* Light border color */
        }

        #output thead {
            background-color: #8AAAE5; /* Header background color */
            color: #FFFFFF; /* Header text color */
            font-weight: 500; /* Header font weight */
            text-align: center; /* Center align header text */
            position: sticky; /* Sticky header */
            top: 0; /* Stick to the top */
            z-index: 10; /* Ensure it's above other content */
            border-bottom: 2px solid #ddd; /* Optional: Adds a bottom border to the header */
        }

        .btn-excel {
            background-color: #ffc107; /* Yellow color */
            color: #fff; /* White text */
            border-color: #ffc107; /* Matching border color */
            transition: background-color 0.3s ease; /* Smooth transition for background color */
        }

        .btn-excel:hover {
            background-color: #dc3545; /* Red color on hover */
            border-color: #dc3545; /* Matching border color on hover */
        }
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
        <h2>ISSUE REGISTER</h2>


        <div class="form-group row">
            <div class="col-md-2">
                <label for="issue_start_date">Start Date:</label>
                <input type="date" class="form-control" id="issue_start_date" name="issue_start_date" value='<?= date("Y-m-d") ?>' required>
            </div>
            <div class="col-md-2">
                <label for="issue_end_date">End Date:</label>
                <input type="date" class="form-control" id="issue_end_date" name="issue_end_date" value='<?= date("Y-m-d") ?>' required>
            </div>
            <div class="col-md-2">
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
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" id='issue_btn' class="btn btn-primary w-100"> (BLSDA)Report</button>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" id='abstract_btn' class="btn btn-primary w-100">Abstract Report</button>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" id='cost_btn' class="btn btn-primary w-100">Food Cost</button>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" id='issue_between_date' class="btn btn-primary w-100">Issue Between Dates</button>
            </div>
        </div>

        <div class="mt-3">
            <table id="issueReportTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Issue Date</th>
                        <th>Item ID</th>
                        <th>Item Name</th>
                        <th>Brand Name</th>
                        <th>Issue<br>Category</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                        <th>Actions</th> <!-- New column for actions -->
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>


    <div id="output" class="mt-3"></div>

    <!-- Centered print button -->
    <div class="text-center mt-3">
        <input type="button" id="print_btn" name="button" value="Print" class="btn btn-primary" />
    </div>


    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Issue Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <input type="hidden" id="edit_id" name="id">
                        <div class="form-group">
                            <label for="edit_issue_date">Issue Date</label>
                            <input type="date" class="form-control" id="edit_issue_date" name="issuedate">
                        </div>
                        <div class="form-group">
                            <label for="edit_item_id">Item ID</label>
                            <input type="text" class="form-control" id="edit_item_id" name="itemid">
                        </div>
                        <div class="form-group">
                            <label for="edit_item_name">Item Name</label>
                            <input type="text" class="form-control" id="edit_item_name" name="itemname">
                        </div>
                        <div class="form-group">
                            <label for="edit_brand_name">Brand Name</label>
                            <input type="text" class="form-control" id="edit_brand_name" name="brandname">
                        </div>
                        <div class="form-group">
                            <label for="edit_issue_category">Issue Category</label>
                            <input type="text" class="form-control" id="edit_issue_category" name="issuecategory">
                        </div>
                        <div class="form-group">
                            <label for="edit_quantity">Quantity</label>
                            <input type="text" class="form-control" id="edit_quantity" name="quantity">
                        </div>
                        <div class="form-group">
                            <label for="edit_unit_price">Unit Price</label>
                            <input type="text" class="form-control" id="edit_unit_price" name="unitprice">
                        </div>
                        <div class="form-group">
                            <label for="edit_total">Total</label>
                            <input type="text" class="form-control" id="edit_total" name="total">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" id="update_btn" class="btn btn-primary">Update</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewModalLabel">View Issue Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="viewForm">
                        <div class="form-group">
                            <label for="view_issue_date">Issue Date</label>
                            <input type="text" class="form-control" id="view_issue_date" name="issuedate" readonly>
                        </div>
                        <div class="form-group">
                            <label for="view_item_id">Item ID</label>
                            <input type="text" class="form-control" id="view_item_id" name="itemid" readonly>
                        </div>
                        <div class="form-group">
                            <label for="view_item_name">Item Name</label>
                            <input type="text" class="form-control" id="view_item_name" name="itemname" readonly>
                        </div>
                        <div class="form-group">
                            <label for="view_brand_name">Brand Name</label>
                            <input type="text" class="form-control" id="view_brand_name" name="brandname" readonly>
                        </div>
                        <div class="form-group">
                            <label for="view_issue_category">Issue Category</label>
                            <input type="text" class="form-control" id="view_issue_category" name="issuecategory" readonly>
                        </div>
                        <div class="form-group">
                            <label for="view_quantity">Quantity</label>
                            <input type="text" class="form-control" id="view_quantity" name="quantity" readonly>
                        </div>
                        <div class="form-group">
                            <label for="view_unit_price">Unit Price</label>
                            <input type="text" class="form-control" id="view_unit_price" name="unitprice" readonly>
                        </div>
                        <div class="form-group">
                            <label for="view_total">Total</label>
                            <input type="text" class="form-control" id="view_total" name="total" readonly>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this record?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" id="delete_btn" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Issue Register JavaScript -->
    <script>
        $(document).ready(function () {
            // DataTable initialization
            var table = $('#issueReportTable').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: 'getIssueRegisterData.php',
                    type: 'POST',
                    data: function (d) {
                        d.issue_start_date = $('#issue_start_date').val();
                        d.issue_end_date = $('#issue_end_date').val();
                        d.item_id = $('#item_id').val();
                    }
                },
                "columns": [
                    { "data": "SNo" },
                    { "data": "IssueDate" },
                    { "data": "ItemID" },
                    { "data": "ItemName" },
                    { "data": "BrandName" },
                    { "data": "IssueCategory" },
                    { "data": "Quantity" },
                    { "data": "UnitPrice" },
                    { "data": "Total" },
                    {
                        "data": null,
                        "render": function (data, type, row) {
                            return '<button type="button" class="btn btn-sm btn-primary edit_btn" data-id="' + row.id + '">Edit</button>' +
                                ' <button type="button" class="btn btn-sm btn-danger delete_btn" data-id="' + row.id + '">Delete</button>';
                        }
                    }
                ]
            });

            // Function to refresh DataTable
            function refreshTable() {
                table.ajax.reload(null, false);
            }

            // Fetch data and show in modal for edit
            $('#issueReportTable tbody').on('click', '.edit_btn', function () {
                var id = $(this).data('id');
                $.ajax({
                    url: 'fetch_issue_data.php',
                    method: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function (data) {
                        $('#edit_id').val(data.id);
                        $('#edit_issue_date').val(data.issuedate);
                        $('#edit_item_id').val(data.itemid);
                        $('#edit_item_name').val(data.itemname);
                        $('#edit_brand_name').val(data.brandname);
                        $('#edit_issue_category').val(data.issuecategory);
                        $('#edit_quantity').val(data.quantity);
                        $('#edit_unit_price').val(data.unitprice);
                        $('#edit_total').val(data.total);

                        $('#editModal').modal('show');
                    }
                });
            });

            // Update record using ajax
            $('#update_btn').on('click', function () {
                var id = $('#edit_id').val();
                var issue_date = $('#edit_issue_date').val();
                var item_id = $('#edit_item_id').val();
                var item_name = $('#edit_item_name').val();
                var brand_name = $('#edit_brand_name').val();
                var issue_category = $('#edit_issue_category').val();
                var quantity = $('#edit_quantity').val();
                var unit_price = $('#edit_unit_price').val();
                var total = $('#edit_total').val();

                $.ajax({
                    url: 'update_issue_data.php',
                    method: 'POST',
                    data: {
                        id: id,
                        issuedate: issue_date,
                        itemid: item_id,
                        itemname: item_name,
                        brandname: brand_name,
                        issuecategory: issue_category,
                        quantity: quantity,
                        unitprice: unit_price,
                        total: total
                    },
                    success: function (response) {
                        $('#editModal').modal('hide');
                        refreshTable();
                        Swal.fire(
                            'Updated!',
                            'Record has been updated successfully.',
                            'success'
                        );
                    }
                });
            });

            // Fetch data and show in modal for view
            $('#issueReportTable tbody').on('click', '.view_btn', function () {
                var id = $(this).data('id');
                $.ajax({
                    url: 'fetch_issue_data.php',
                    method: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function (data) {
                        $('#view_issue_date').val(data.issuedate);
                        $('#view_item_id').val(data.itemid);
                        $('#view_item_name').val(data.itemname);
                        $('#view_brand_name').val(data.brandname);
                        $('#view_issue_category').val(data.issuecategory);
                        $('#view_quantity').val(data.quantity);
                        $('#view_unit_price').val(data.unitprice);
                        $('#view_total').val(data.total);

                        $('#viewModal').modal('show');
                    }
                });
            });

            // Delete record using ajax
            $('#issueReportTable tbody').on('click', '.delete_btn', function () {
                var id = $(this).data('id');
                $('#deleteModal').modal('show');

                $('#delete_btn').on('click', function () {
                    $.ajax({
                        url: 'delete_issue_data.php',
                        method: 'POST',
                        data: { id: id },
                        success: function (response) {
                            $('#deleteModal').modal('hide');
                            refreshTable();
                            Swal.fire(
                                'Deleted!',
                                'Record has been deleted successfully.',
                                'success'
                            );
                        }
                    });
                });
            });

            // Issue Report Form Submission
            $('#issueReportForm').on('submit', function (event) {
                event.preventDefault();
                $.ajax({
                    url: 'add_issue_data.php',
                    method: 'POST',
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        $('#issueReportForm')[0].reset();
                        refreshTable();
                        $('#issueReportModal').modal('hide');
                        Swal.fire(
                            'Added!',
                            'Record has been added successfully.',
                            'success'
                        );
                    }
                });
            });

            // Date Range Picker Initialization
            $('#date_range').daterangepicker({
                "locale": {
                    "format": "DD/MM/YYYY",
                    "separator": " - ",
                    "applyLabel": "Apply",
                    "cancelLabel": "Cancel",
                    "fromLabel": "From",
                    "toLabel": "To",
                    "customRangeLabel": "Custom",
                    "weekLabel": "W",
                    "daysOfWeek": [
                        "Su",
                        "Mo",
                        "Tu",
                        "We",
                        "Th",
                        "Fr",
                        "Sa"
                    ],
                    "monthNames": [
                        "January",
                        "February",
                        "March",
                        "April",
                        "May",
                        "June",
                        "July",
                        "August",
                        "September",
                        "October",
                        "November",
                        "December"
                    ],
                    "firstDay": 1
                },
                "opens": "left"
            });

            // Trigger data reload on date range selection
            $('#date_range').on('apply.daterangepicker', function (ev, picker) {
                $('#issue_start_date').val(picker.startDate.format('YYYY-MM-DD'));
                $('#issue_end_date').val(picker.endDate.format('YYYY-MM-DD'));
                refreshTable();
            });

            // Initialize date range picker with a default range (last 30 days)
            $('#date_range').data('daterangepicker').setStartDate(moment().subtract(29, 'days'));
            $('#date_range').data('daterangepicker').setEndDate(moment());
            $('#issue_start_date').val(moment().subtract(29, 'days').format('YYYY-MM-DD'));
            $('#issue_end_date').val(moment().format('YYYY-MM-DD'));
            refreshTable();
        });
    </script>
</body>

</html>
