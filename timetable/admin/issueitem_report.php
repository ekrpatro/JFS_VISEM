<!DOCTYPE html>
<?php include("admindbconn.php") ?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>IssueRegister</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.1.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

    <link type="text/css" rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
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
    </style>
</head>

<body>

    <?= include('./menu.php') ?>
	
    <div class="container mt-5">

        <h2>Issue Register</h2>

        <div class="form-group">
            <label for="issue_date">Select Issue Date:</label>
            <input type="date" class="form-control" id="issue_date" name="issue_date" value='<?=date("Y-m-d")?>' required>
			<label for="item_id">Select Item :</label>
            <select class="item_id" id='item_id' required>
                        
						<option value="0">All</option>
                        <?php
                        $itemQuery = "SELECT id, itemname FROM item";
                        $itemResult = mysqli_query($dbconn, $itemQuery);

                        if ($itemResult) {
                            while ($itemRow = mysqli_fetch_array($itemResult)) { ?>
                                <option value="<?php echo $itemRow['id']; ?>"><?php echo $itemRow['itemname']." - ".$itemRow['id']; ?></option>
                        <?php }
                        } else {
                            echo "<option value=''>No items found</option>";
                        }
                        ?>
			</select>
            <button type="button" id='issue_btn' class="btn btn-primary">Generate Report</button>
        </div>

        <div class="mt-3">
            <table id="issueReportTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Issue Date</th>
                        <th>Item ID</th>
                        <th>Item Name</th>
                        <th>Issue Category</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var dataTable = $('#issueReportTable').DataTable({
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
                        return 'Issued Report ' + $("#issue_date").val();
                    },
                }, ],
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
                    url: './ajax_fetch_issue_report.php',
                    type: 'POST',
                    data: function() {
                        return {
                            'issue_date': $('#issue_date').val(),
							'item_id': $('#item_id').val(),
                            'action': 'ajax_fetch_issue_report'
                        };
                    },
                    dataSrc: ''
                },
                columns: [{
                        data: 'sno'
                    },
                    {
                        data: 'issue_date'
                    },
                    {
                        data: 'itemid'
                    },
                    {
                        data: 'itemname'
                    },
                    {
                        data: 'issue_category'
                    },
                    {
                        data: 'quantity'
                    },
                    {
                        data: 'unit_price'
                    },
                    {
                        data: 'total_price'
                    }
                ]
            });

            $("#issue_btn").click(function() {
                if ($('#issue_date').val() == '') {
                    Swal.fire({
                        title: 'Please Enter Date',
                        icon: 'error',
                    });
                    return false;
                }

                dataTable.ajax.reload();
            });
        });
    </script>
</body>

</html>