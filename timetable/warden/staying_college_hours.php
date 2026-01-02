<?php
  session_start();
  $role = isset($_SESSION['role']) ? $_SESSION['role'] : "";  
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staying at Hostel During College Hours</title>
  <link href="../bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
 

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>


<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>


<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
	  width: 100%;
	  overflow-x: hidden;
	  background-color: #f8f9fa; /* Light grayish-blue for a soft look */
	 
	}
    .print_issue__head {
      background-color: #f0f0f0;
      padding: 10px;
      margin-bottom: 20px;
      border-radius: 5px;
      text-align: center;
    }
    .print_issue__head span {
      font-size: 1.2rem;
      font-weight: bold;
      color: #333;
      text-transform: uppercase;
    }
    .issue__form {
      width: 800px;
      max-width: 100%;
      margin: 2rem auto;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    table {
      width: 100%;
      background-color: #232323;
      border-collapse: collapse;
    }
	.pdftable {
      width: 100%;
      background-color: white;
	  color:black;
      border-collapse: collapse;
    }
	.pdfdata{
		border: 1px solid black;
		padding: 0.8rem;
		color: #000;
		
      padding: 0.8rem;
	}
    thead tr th {
      color: #fff;
      border-bottom: 2px solid #fff;
      padding: 0.8rem;
      text-align: center;
      font-size: 1rem;
      background-color: #8AAAE5;
      position: sticky;
      top: 0;
      z-index: 10;
    }
    tbody tr td {
      padding: 0.8rem;
      text-align: center;
      color: #fff;
      border-bottom: 1px solid #fff;
    }
    tbody tr td input[type="checkbox"] {
      width: 17px;
      height: 17px;
    }
	/* Apply background color to table */
#stayingTable {
    width: 100%;
    border-collapse: collapse;
    background-color: #ffffff; /* White background for better contrast */
}

/* Style table headers */
#stayingTable thead th {
    background-color: #007BFF; /* Bootstrap primary color */
    color: white;
    text-align: center;
    padding: 10px;
    border-bottom: 2px solid #ddd;
}

/* Alternate row colors for readability */
#stayingTable tbody tr:nth-child(odd) {
    background-color: #f2f2f2; /* Light gray */
}

#stayingTable tbody tr:nth-child(even) {
    background-color: #ffffff; /* White */
}

/* Hover effect on rows */
#stayingTable tbody tr:hover {
    background-color: #D6EAF8 !important; /* Light blue */
    cursor: pointer;
}

/* Styling for action buttons */
.dt-buttons .btn {
    margin: 5px;
    font-size: 14px;
    padding: 6px 12px;
}

/* Table cell styling */
#stayingTable tbody td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
    color: #333; /* Dark text for readability */
}



  </style>
</head>
<!--<body onload="checkStayingStatus()">-->
<body>

<form id="stayingForm" class="issue__form">
  <div class="issue__head">
    <h1>Staying at Hostel During College Hours </h1>
  <?php
   
    $gender = isset($_SESSION['gender']) ? $_SESSION['gender'] : "";
	$block_name = isset($_SESSION['block_name']) ? $_SESSION['block_name'] : "";

		// Determine the display text for gender
	$genderText = ($gender == 'M') ? "BOYS" : (($gender == 'F') ? "GIRLS" : "");
	
	?>
	<select name="college_name" id="college_name" required>
		<option value="">Select College Name</option>
		<?php 
		if ($role == 'WARDEN') { ?>
			<option value="ALL" selected>ALL</option>
		<?php 
		} else { ?>
			
			<option value="IARE" selected>IARE</option>			
			<option value="MLRIT">MLRIT</option>
			<option value="MLRITM">MLRITM</option>
			<option value="MLRIP">MLRIP</option>
			<option value="AIMS">AIMS</option>
			<option value="ALL" >ALL</option>
		<?php 
		} ?>
	</select>
	<select name="gender_category" id="gender_category" required>
		
		
		<?php 
		if ($role == 'WARDEN') 
		{ ?>
			<option value="<?=$gender ?>" selected><?= $genderText ?></option>
		<?php 
		}
		else 
		{ ?>
			<option value="B">Both Gender</option>
			<option value="M">Male</option>
			<option value="F">FeMale</option>
			<?php
		} ?>
	</select>
	<?php 
		if ($role == 'WARDEN') 
		{ ?>
		<select name="block_name" id="block_name" required>
		  <option value="">Select Block</option>		
			
				<option value="<?=$block_name ?>" selected><?= $block_name ?></option>
		</SELECT>
		<?php 
		}
		 ?>
	 
	
   

    <input type="date" name="staying_date" id="staying_date" value="<?= date('Y-m-d') ?>" required>
	<?php 
	if ($role == 'WARDEN') 
	{?>
    <button type="button" id="filterBtn" class="btn btn-primary">Filter</button>
	<?php }?>
	<button type="button" id="showBtn" class="btn btn-primary">Show</button>
	<input type="button"  id='pdf_btn' name='pdf_btn' class="generate-report-button" onclick="generateStayingReport()" value="PDF Report">
	<td><input type="button" name="button" value="Print"  class="btn btn-primary" onClick="printDiv()"/></td>
  </div>
	<?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'WARDEN') { ?>   
	  <table id="studentTable">
		<thead>
		  <tr>
			<th>S.No</th>        
			
			<th>Room No</th>
			<th>Roll No</th>
			<th>Name</th>
			<th>Gender</th>	
			
			<th>Branch</th>
			<th>Year</th>
			<th>College Name</th>	
			
		  </tr>
		</thead>
		<tbody></tbody>
	  </table>
	  <div id='div_saveRecord_btn'>
		<button type="button" id="saveRecords" class="btn btn-success mt-3">Submit Records</button>
	  </div>
			  
			
		  <div id='div_no_absenteed_btn'>
			<button type="button" id="no_student_staying" class="btn btn-success mt-3">NO STUDENTS STAYING DURING COLLEGE HOURS</button>
		  </div>
  <?php }  ?>
</form>



<!-- Table for Staying Records -->
<table id="stayingTable" class="display nowrap" style="width:100%">
    <thead>
      <tr>
        <th>S.NO</th> 		
		<th>ROOM NO</th>		
        <th>ROLL NUMBER</th>
		<th>NAME</th>		
		<th>GENDER</th>	
        <th>YEAR</th>
		<th>BRANCH</th>
		<th>COLLEGE NAME</th>
		        
        
      </tr>
    </thead>
    <tbody></tbody>
</table>
<table><tr><td></td></tr></table>
<div id="output" >
</div>

<script>
$(document).ready(function() {
	
	 var table;

    $("#showBtn").click(function() {
        var staying_date = $("#staying_date").val();		 
	
	
	let print_staying_date = staying_date.split("-").reverse().join("-");

	var college_name = $("#college_name").val();
	var gender_category = $("#gender_category").val();

        
        if (!staying_date) {
            Swal.fire("Error", "Please select a date.", "warning");
            return;
        }

        $.ajax({
            url: "fetch_staying_records.php",
            type: "POST",
            data: { staying_date: staying_date,college_name: college_name ,gender:gender_category},
            dataType: "json",
            success: function(response) {
                if (table) {
                    table.destroy(); // Destroy old instance before re-initializing
                }

                table = $("#stayingTable").DataTable({
                    data: response,
                    columns: [
                        { data: null, render: function(data, type, row, meta) { return meta.row + 1; }, className: "text-center" },
                       
						{ data: "room_no", defaultContent: "N/A" , className: "text-center"},
						{ data: "rollno", defaultContent: "N/A", className: "text-center" },
						{ data: "name", defaultContent: "N/A" , className: "text-left"},						
						{ data: "gender", defaultContent: "N/A" , className: "text-center"},	
						{ data: "studying_year", defaultContent: "N/A" , className: "text-center"},                        
						{ data: "branch", defaultContent: "N/A" , className: "text-center"},
						{ data: "college_name", defaultContent: "N/A" , className: "text-left"},	
						
                                               
                    ],
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: 'Download Excel',
                            title: 'LIST OF STUDENTS STAYING IN HOSTEL DURING COLLEGE HOURS',
                            className: 'btn btn-success'
                        }
                        
                        /*{
                            extend: 'print',
                            text: 'Print',
                            title: 'LIST OF STUDENTS STAYING IN HOSTEL DURING COLLEGE HOURS ON '+print_staying_date,
                            className: 'btn btn-primary'
                        }*/
                    ],
                    responsive: true
                });

                Swal.fire("Success", "Records loaded successfully!", "success");
				 // Scroll to stayingTable
				$("#studentTable").hide();
				$("#div_saveRecord_btn").hide();
                $("#stayingTable").show();
                $("html, body").animate({
                    scrollTop: $("#stayingTable").offset().top - 50
                }, 500);
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", xhr.responseText);
                Swal.fire("Error", "Failed to fetch records. Check console for details.", "error");
            }
        });
    });
	//pdf_btn 
	 $("#pdf_btn").click(generateStayingReport);
    // Fetch student list based on selected filters
    $("#filterBtn").click(function() {
        var gender = $("#gender_category").val();
        var block_name = $("#block_name").val();
		//var college_name = $("#college_name").val();

        if (!gender || !block_name) {
            alert("Please select gender and block.");
            return;
        }

        $.ajax({
            url: "fetch_students.php",
            type: "POST",
            data: { gender: gender, block_name: block_name },
            success: function(response) {
                // $("#studentTable tbody").html(response);
				 $("#studentTable tbody").html(response);

                // Scroll to studentTable
				$("#stayingTable").hide();
                $("#studentTable").show();
				$("#div_saveRecord_btn").show();
                $("html, body").animate({
                    scrollTop: $("#studentTable").offset().top - 50
                }, 500);
            }
        });
    });

    // Save selected records
    $("#saveRecords").click(function() {
		
        var gender = $("#gender_category").val();
		var block_name = $("#block_name").val();
		var staying_date = $("#staying_date").val();
        var selectedRollNos = [];

        $("input[name='op[]']:checked").each(function() {
            selectedRollNos.push($(this).val());
        });

        if (selectedRollNos.length === 0) {
            Swal.fire({
                icon: "warning",
                title: "No Students Selected!",
                text: "Please select at least one student.",
            });
            return;
        }
		
        // Show SweetAlert confirmation popup
        Swal.fire({
            title: "Are you sure?",
            text: "Do you want to insert these records?",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Yes, Save it!",
            cancelButtonText: "No, Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "save_staying_records.php", // PHP script to handle saving
                    type: "POST",
                    contentType: "application/json",
                    data: JSON.stringify({ staying_date: staying_date, rollnos: selectedRollNos,block_name:block_name,gender:gender }),
                    success: function(response) {
                        Swal.fire({
                            icon: response.status ? "success" : "error",
                            title: response.status ? "Success" : "Error",
                            text: response.message,
                        }).then(() => {
                            if (response.status) {
                                location.reload(); // Reload page on success
                            }
                        });
                    },
                    error: function() {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Error processing request. Please try again.",
                        });
                    }
                });
            }
        });
    });
	// NO ABSENTEES STARTS
	// Save selected records
    $("#no_student_staying").click(function() {
		
        var staying_date = $("#staying_date").val();
		var block_name = $("#block_name").val();
		var gender = $("#gender_category").val();
		
		 
       
		if (!gender || !block_name) {
            Swal.fire({
                icon: "warning",
                title: "Select Gender/Block Name",
                text: "Select Valid Input",
            });
            return;
        }
        // Show SweetAlert confirmation popup
        Swal.fire({
            title: "Are you sure?",
            text: "Do you want to insert No staying during college hours?",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Yes, Save it!",
            cancelButtonText: "No, Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "save_staying_records.php", // PHP script to handle saving
                    type: "POST",
                    contentType: "application/json",
                    data: JSON.stringify({ staying_date: staying_date, block_name: block_name,staying_count : 'Nil',gender:gender }),
                    success: function(response) {
						//alert("ok....");
                        Swal.fire({
                            icon: response.status ? "success" : "error",
                            title: response.status ? "Success" : "Error",
                            text: response.message,
                        }).then(() => {
                            if (response.status) {
                                location.reload(); // Reload page on success
                            }
                        });
                    },
                    error: function() {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Error processing request. Please try again.",
                        });
                    }
                });
            }
        });
    });
	
	// NO ABSENTEES ENDS
	
	//checkStayingStatus();
	<?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'WARDEN') { ?>
            //checkStayingStatus(); // Correct function call in JavaScript
        <?php }  ?>
	

	
});

function checkStayingStatus() {
		var staying_date = document.getElementById("staying_date").value;
		var gender = document.getElementById("gender_category").value;
		var block_name = document.getElementById("block_name").value;

		$.ajax({
			url: "check_staying_status.php",
			type: "POST",
			data: { staying_date: staying_date,block_name:block_name,gender:gender },
			dataType: "json",
			success: function(response) {
				if (response.count > 0) {
					Swal.fire({
						icon: "info",
						title: "Entry Already Exists!",
						text: "Records for this date have already been submitted.",
						confirmButtonText: "OK"
					});

					// Optionally, disable the submit button
					$("#saveRecords").prop("disabled", true);
					$("#no_student_staying").prop("disabled", true);
				} /*else {
					Swal.fire({
						icon: "success",
						title: "No Entries Yet",
						text: "You can submit records for this date.",
						confirmButtonText: "OK"
					});
				}*/
			},
			error: function(xhr, status, error) {
				console.error("Error checking staying status:", xhr.responseText);
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
	function generateStayingReport() {
			
            let formData = {
                staying_date: $("#staying_date").val(),
                college_name: $("#college_name").val(),
				gender: $("#gender_category").val()
            };

            $.ajax({
                url: 'generate_report.php',
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
</script>

</body>
</html>
