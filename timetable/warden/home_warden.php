<?php
session_start();
include("../html/dbconn.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Warden | Hostel Management System</title>
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
		.admin__right {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
            gap: 2rem;
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
                <li class="nav-item"><a class="nav-link" href="./gatepass_approve.php">ApproveGatePass</a></li>
                <li class="nav-item"><a class="nav-link" href="./staying_college_hours.php">StayingCollegeHours</a></li>
                <li class="nav-item"><a class="nav-link" href="../admin/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>
	

    <main class="admin__main">
        <div class="admin__profile">
            <?php
            $user_name = $_SESSION['user_name'];
			$photo=$user_name.".jpg";
            $sqlget = "SELECT a.*,b.* FROM admin a inner join warden_tbl b on a.user_name=b.warden_id WHERE a.user_name = ?";
			$gender="";
			$block_name="";
            if ($stmt = $dbconn->prepare($sqlget)) 
			{
                $stmt->bind_param('s', $user_name);
                $stmt->execute();
                $result = $stmt->get_result();
				while ($row = $result->fetch_assoc()) 
				{ 
					$gender=$row['gender'];
					$block_name=$row['block_name'];
					
				?>
					<h3>Warden</h3>
					<img src="../img/<?=$photo ?>" alt="<?= htmlspecialchars($row['user_name']) ?>">
					<div class="admin__details">
						<p>Name: <?= $row['name'] ?></p>
						<p>Mobile Number: <?= $row['mobile_number'] ?></p>
						<p>Block: <?= $block_name ?></p>
					   
						<!--<a href="admin_profile.php?id=<?= htmlspecialchars($row['id']) ?>"><ion-icon name="pencil"></ion-icon> <span>Edit</span></a>-->
					</div>
				<?php 
				} 
                $stmt->close();
            } 
			else 
			{
                echo 'Invalid credential ';
            }
            ?>
        </div>

        <section class="admin__right">
            <h2>Student List</h2>
            <!-- Modal Structure -->
			 <div class="food__items">
                <?php
                $select_q = "SELECT * FROM student where gender='".$gender."' and block_name='".$block_name."'";
                $q = $dbconn->query($select_q);
                ?>

                <table>
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Room_No.</th>
                            <th>RollNo</th>
                            <th>Name</th>
                            <th>Branch</th>
			    <th>College_Name</th>
			    <th>StudentPhone</th>
			    <th>ParentPhone</th>
                           
                        </tr>
                     </thead>

                    <tbody>
                        <?php 
							$sno=0;
							while($r = $q->fetch_assoc())
							{ ?>
                            <tr>
                                <td><?=++$sno ?></td>
								<td><?=$r['room_no'] ?></td>
                                <td><?=$r['rollno'] ?></td>
                                <td><?=$r['name'] ?></td>
                                <td><?=$r['branch'] ?></td>
                                <td><?=$r['college_name'] ?></td>                                
				 <td><?=$r['student_phone'] ?></td>  
				 <td><?=$r['parent_phone'] ?></td>  
                            </tr>
                        <?php 
							} ?>
                    </tbody>
                </table>
            </div>
            
			
			
            
            

           
        </section>
    </main>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
