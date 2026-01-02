<?php
session_start();
include("../html/dbconn.php");

// Check if the user is logged in
if ( $_SESSION["role"] != 'MANAGER' && $_SESSION["role"] != 'WARDEN') {
    echo  "Invalid User";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gatepass Records</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <style>
        .container {
            margin-top: 20px;
        }
        .modal-content {
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>GatePass Records</h1>
        <table>
            <tr>
                <td><input type='date' name='gatepass_date' id='gatepass_date' value='<?=date("Y-m-d")?>'></td>
                <td><button class="btn btn-primary" id='show_btn'>Show</button></td>
            </tr>
        </table>	
		
		
        <table id="studentTable" class="table table-striped">
            <thead>
                <tr>
                    <th>S.No</th>  
					<th>Room No.</th>	
					<th>Name</th> 			
                    <th>Roll No</th> 
                    <th>College Name</th>
					
					<th>Outpass Date</th>
					<th>Outpass Time</th>
					<th>Reason</th>
					<th>In Date</th>
					<th>InTime</th>
					<th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Process Gatepass</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <input type="hidden" id="edit_id" name="id">
                        <div class="form-group">
                            <label for="edit_status">status</label>
                            
							<select id="edit_status" name="edit_status">
								<option value='Pending'>Pending</OPTION>
								<option value='Approved'>Approved</OPTION>
								<option value='Rejected'>Rejected</OPTION>
							</select>
                        </div>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Record</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this record?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmDelete" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            var table = $('#studentTable').DataTable({
                "ajax": {
                    "url": "ajax_gatepass_data.php",
                    "type": "POST",
                    "data": function(d) {                        
                        d.action = 'fetch_students';
                        d.gatepass_date = $('#gatepass_date').val();
                    },
                    "dataSrc": ""
                },
                "columns": [
                    { "data": "id" },					
                    { "data": "room_no" },
                    { "data": "name" },	
                    { "data": "rollno" }, 
                    { "data": "college_name" },
					{ "data": "out_date" },
					{ "data": "out_time" },
					{ "data": "reason" },
					{ "data": "in_date" },
					{ "data": "in_time" },
					{ "data": "status" },
                    {
                        "data": null,
                        "render": function(data) {
                            return `
                                <button class="btn btn-warning btn-edit" data-id="${data.id} data-status="${data.status}">Process</button>
                                
                            `;
                        }
                    }
                ]
            });

            $('#show_btn').on('click', function() {
                table.ajax.reload();
            });

            $('#studentTable').on('click', '.btn-edit', function() {			
                var id = $(this).data('id');
                var data = table.row($(this).parents('tr')).data();
                $('#edit_id').val(id);
                //$('#edit_status').val(data.status);
				var currentStatus = $(this).data('status');

				 var statusDropdown = $('#edit_status');
				statusDropdown.empty(); // Clear existing options
				const statuses = ["Pending", "Approved", "Rejected"];
				const otherStatuses = statuses.filter(status => status !== currentStatus);

				statusDropdown.append(`<option value="${currentStatus}" selected>${currentStatus}</option>`);
				otherStatuses.forEach(status => {
				statusDropdown.append(`<option value="${status}">${status}</option>`);});
								
                $('#editModal').modal('show');
            });

            $('#editForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'ajax_gatepass_data.php',
                    type: 'POST',
                    data: {
                        id: $('#edit_id').val(),
                        status: $('#edit_status').val(),
                        action: 'update_gatepass'
                    },
                    dataType: 'json',
                    success: function(response) {
                        Swal.fire(response.status, response.message, response.status);
                        $('#editModal').modal('hide');
                        table.ajax.reload();
                    }
                });
            });

            $('#studentTable').on('click', '.btn-delete', function() {
                var id = $(this).data('id');
                $('#confirmDelete').data('id', id);
                $('#deleteModal').modal('show');
            });

            $('#confirmDelete').on('click', function() {
                $.ajax({
                    url: 'ajax_gatepass_data.php',
                    type: 'POST',
                    data: { id: $(this).data('id'), action: 'delete_student_data' },
                    success: function(response) {
                        $('#deleteModal').modal('hide');
                        table.ajax.reload();
                    }
                });
            });
        });
    </script>
</body>
</html>
