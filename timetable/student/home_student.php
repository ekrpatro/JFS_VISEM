<?php
session_start();
include("../html/dbconn.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Student | Hostel Management System</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="../css/icon.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        /* General Reset and Styles */
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
        .admin__main {
            width: 100%;
            padding: 1.2rem;
            min-height: 100vh;
            display: flex;
            flex-wrap: wrap;
            gap: 1.8rem;
            align-items: flex-start;
            margin: auto;
            margin-top: 3rem;
        }
        .admin__profile {
            width: 100%;
            max-width: 300px;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 1.2rem;
            color: white;
            text-align: center;
        }
        .admin__profile img {
            width: 100%;
            max-width: 180px;
            height: auto;
            object-fit: cover;
            margin-bottom: 10px;
        }
        .admin__details p {
            font-size: 1.12rem;
        }
        .student__status {
            width: 100%;
            padding: 0.7rem;
            border: 1.8px solid #232323;
        }
        .student__status h4 {
            color: darkorange;
        }
        .food__items {
            width: 100%;
            margin-top: 2rem;
        }
        .food__items table {
            width: 100%;
            border-spacing: 1rem;
            border-collapse: collapse;
        }
        thead tr {
            background-color: burlywood;
        }
        thead th, tbody td {
            padding: 0.8rem;
            border: 1px solid #232323;
            text-align: center;
        }
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
        }
        .btn-primary {
            background-color: #007bff;
            color: #fff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .admin__main {
                flex-direction: column;
                align-items: center;
            }
            .admin__profile {
                width: 90%;
            }
        }
    </style>
</head>

<body>

    <!-- Navigation Menu -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Hostel System</a>
        
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="./gatepass.php">ApplyGatePass</a></li>
                <li class="nav-item"><a class="nav-link" href="./view_gatepass.php">GatepassStatus</a></li>
                <li class="nav-item"><a class="nav-link" href="../admin/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>
	

    <main class="admin__main">
        <div class="admin__profile">
            <?php
            $user_name = $_SESSION['user_name'];
            $photo = $user_name . ".jpg";
            $sqlget = "SELECT * FROM student WHERE rollno = ?";
            if ($stmt = $dbconn->prepare($sqlget)) {
                $stmt->bind_param('s', $user_name);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) { ?>
                    <h3>Student</h3>
                    <img src="../img/<?= $photo ?>" alt="<?= htmlspecialchars($row['rollno']) ?>">
                    <div class="admin__details">
                        <p>Name: <?= htmlspecialchars($row['name']) ?></p>
                    </div>
                <?php } 
                $stmt->close();
            } else {
                echo 'Error preparing query: ' . htmlspecialchars($dbconn->error);
            }
            ?>
        </div>

        <section class="food__items">
            <h2>Mess Timings</h2>
            <p>
                Breakfast: 7:00 AM – 8.00 AM <br>
                Lunch: As per college timetable <br>
                Tea: 4:30 PM – 5:30 PM <br>
                Dinner: Girls: 7:00 PM – 8:00 PM | Boys: 8:00 PM – 9:00 PM
            </p>
        </section>
    </main>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
