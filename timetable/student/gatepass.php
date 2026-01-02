<?php
session_start();
// Check if the user is logged in
if (!isset($_SESSION['user_name']) || $_SESSION["role"] != 'STUDENT') {
    echo "Invalid User";
    exit(0);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hostel Gate Pass</title>
	
    <style>
        /* Container Styling */
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            text-align: center;
            background-color: #f9f9f9;
        }

        /* Logo Styling */
        .logo {
            width: 100%;
            max-width: 700px; /* Adjust image size */
            height: auto;
            display: block;
            margin: 0 auto 10px;
        }

        /* Form Labels */
        label {
            display: block;
            font-weight: bold;
            margin: 10px 0 5px;
            text-align: left;
        }

        /* Input Fields */
        input[type="text"],
        input[type="date"],
        input[type="time"],
        textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        /* Textarea */
        textarea {
            resize: vertical;
            height: 80px;
        }

        /* Button */
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            display: block;
            width: 100%;
        }

        button:hover {
            background-color: #45a049;
        }

        /* Error Highlight */
        .error {
            border: 2px solid red;
            background-color: #ffecec;
        }
    </style>
	
   
	<!-- jQuery (Required for AJAX) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- SweetAlert2 (Required for alerts) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <div class="container">
        <img src="../img/college_logo.jpg" class="logo" alt="College Logo">
    </div>

    <div class="container">
        <h2>Hostel Gate Pass</h2>
        <form id="gatePassForm" action="gatepass_insert.php" method="post">
            <!-- Roll Number / Username -->
            <label for="rollno">Roll No:</label>
            <input type="text" id="rollno" name="rollno" 
                   value="<?= isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : '' ?>" 
                   required readonly>

            <!-- Name Field -->
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" 
                   value="<?= isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : '' ?>" 
                   required readonly>

            <!-- Room Number -->
            <label for="room">Room Number:</label>
            <input type="text" id="room_no" name="room_no"   
                   value="<?= isset($_SESSION['room_no']) ? htmlspecialchars($_SESSION['room_no']) : '' ?>"  
                   required readonly>

            <!-- Outpass Date -->
            <label for="out_date">Outpass Date:</label>
            <input type="date" id="out_date" name="out_date" required>

            <!-- Departure Time -->
            <label for="out_time">Time of Departure:</label>
            <input type="time" id="out_time" name="out_time" required>

            <!-- Reason for Leaving -->
            <label for="reason">Reason for Leaving:</label>
            <textarea id="reason" name="reason" rows="4" required></textarea>

            <!-- Expected Return Date -->
            <label for="in_date">Expected Return Date:</label>
            <input type="date" id="in_date" name="in_date" required>

            <!-- Time of Arrival -->
            <label for="in_time">Time of Arrival:</label>
            <input type="time" id="in_time" name="in_time" required>

            <!-- Submit Button -->
            <button type="submit">Submit</button>
        </form>
    </div>


<script>
$(document).ready(function() {
    $('#gatePassForm').submit(function(event) {
        event.preventDefault(); // Prevent default form submission
        
        // Client-side validation
        let isValid = true;
        $('#gatePassForm input[required], #gatePassForm textarea[required]').each(function() {
            if ($(this).val().trim() === '') {
                isValid = false;
                $(this).addClass('error');
            } else {
                $(this).removeClass('error');
            }
        });

        if (!isValid) {
            Swal.fire({
                icon: 'warning',
                title: 'Incomplete Form',
                text: 'Please fill all required fields before submitting.',
            });
            return;
        }

        // Show confirmation popup
        Swal.fire({
            title: "Confirm Submission",
            text: "Are you sure you want to submit this Gate Pass request?",
            icon: "question",
            showDenyButton: true, // Show explicit Yes/No buttons
            showCancelButton: false,
            confirmButtonText: "Yes",
            denyButtonText: "No"
        }).then((result) => {
            if (result.isConfirmed) {
                submitForm(); // Call separate function to process submission
            } else if (result.isDenied) {
                Swal.fire("Submission Cancelled", "Your request was not submitted.", "info");
            }
        });
    });

    function submitForm() {
        $.ajax({
            type: 'POST',
            url: 'gatepass_insert.php',
            data: $('#gatePassForm').serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === "success") {
					Swal.fire({
						icon: 'success',
						title: 'GatePass Request',
						text: 'Gatepass Request submitted successfully.'
					}).then(() => {
						window.location.href = 'home_student.php'; // Redirect after success
					});
				} else {
					Swal.fire({
						icon: 'error',
						title: 'Error',
						text: response.message
					});
				}
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong! Please try again.'
                });
            }
        });
    }
});
</script>

  	


 

</body>
</html>
