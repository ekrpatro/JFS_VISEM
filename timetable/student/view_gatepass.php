<?php
session_start();
include("../html/dbconn.php");

// Check if the user is logged in
if (!isset($_SESSION['user_name']) || $_SESSION["role"] != 'STUDENT') {
    echo json_encode(["status" => "error", "message" => "Invalid User"]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>GatePass Report | Hostel Management System</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="../css/icon.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <style>
        /* General Styling */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .admin__main {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        .admin__right {
            width: 90%;
            max-width: 1200px;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        /* Table Styling */
        #gatepassTable {
            width: 100%;
            border-collapse: collapse;
            overflow: hidden;
            border-radius: 10px;
        }

        #gatepassTable thead {
            background-color: #007bff;
            color: white;
        }

        #gatepassTable th, #gatepassTable td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        #gatepassTable tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #gatepassTable tr:hover {
            background-color: #ddd;
        }

        /* Responsive Design */
        @media screen and (max-width: 768px) {
            .admin__right {
                width: 100%;
                padding: 15px;
            }

            h2 {
                font-size: 20px;
            }

            #gatepassTable th, #gatepassTable td {
                padding: 8px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>

    <div class="admin__main">
		<div class="img__header">
			<img src="../img/college_logo.jpg" alt="College Logo" >
		</div> 
        <section class="admin__right">
            <h2>Gate Pass Requests</h2>
            <table id="gatepassTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Roll No</th>
                        <th>Name</th>
                        <th>Room No</th>
                        <th>Out Date</th>
                        <th>Out Time</th>
                        <th>Reason</th>
                        <th>In Date</th>
                        <th>In Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $user_name = $_SESSION['user_name'];
                    $sqlget = "SELECT a.*, b.name FROM gatepass_tbl a INNER JOIN student b ON a.rollno = b.rollno WHERE a.rollno = ? order by id desc";
                    
                    if ($stmt = $dbconn->prepare($sqlget)) {
                        $stmt->bind_param('s', $user_name);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $sno = 1;
                        
                        while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?= $sno++; ?></td>
                                <td><?= htmlspecialchars($row['rollno']) ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['room_no']) ?></td>
                                <td><?= htmlspecialchars($row['out_date']) ?></td>
                                <td><?= htmlspecialchars($row['out_time']) ?></td>
                                <td><?= htmlspecialchars($row['reason']) ?></td>
                                <td><?= htmlspecialchars($row['in_date']) ?></td>
                                <td><?= htmlspecialchars($row['in_time']) ?></td>
                                <td><?= htmlspecialchars($row['status']) ?></td>
                            </tr>
                    <?php } ?>
                    
                    <?php
                        $stmt->close();
                    } else {
                        echo '<tr><td colspan="10" class="text-center text-danger">Error fetching data.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
