<?php
session_start();
// Check if the user is logged in
if (!isset($_SESSION['user_name']) || $_SESSION["role"] != 'STUDENT') {
    echo "Invalid User";
    exit(0);
}

// Debugging: Uncomment to check session values
// var_dump($_SESSION);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hostel Gate Pass</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Hostel Gate Pass</h2>
        <form id="gatePassForm" action="gatepass_insert.php" method="post">
            <!-- Roll Number / Username -->
            <label for="rollno">Roll No:</label>
            <input type="text" id="rollno" name="rollno" 
                   value="<?= isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : '' ?>" 
                   required readonly>

            <!-- Name Field (If needed, populate from session) -->
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" 
                   value="<?= isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : '' ?>" 
                   required readonly>

            <!-- Room Number -->
            <label for="room">Room Number:</label>
            <input type="text" id="room_no" name="room_no"   value="<?= isset($_SESSION['room_no']) ? htmlspecialchars($_SESSION['room_no']) : '' ?>"  required readonly>

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

    <!-- jQuery & Validation -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#gatePassForm').submit(function(event) {
            var isValid = true;
            
            // Client-side validation
            $('input, textarea').each(function() {
                if ($(this).val().trim() === '') {
                    isValid = false;
                    $(this).addClass('error');
                } else {
                    $(this).removeClass('error');
                }
            });
            
            if (!isValid) {
                event.preventDefault();
                alert("Please fill all fields correctly.");
            }
        });
    });
    </script>
</body>
</html>
