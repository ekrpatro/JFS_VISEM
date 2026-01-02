<?php
session_start();
include("admindbconn.php");
// Session validation
if (!isset($_SESSION['user_name']) || $_SESSION["role"] != 'ADMIN') {
    echo "Invalid User";
    //exit(0);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Admin | Hostel Stock Management System</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="../css/icon.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">


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
            max-width: 100%;
            padding: 1.2rem;
            min-height: 100vh;
            display: flex;
            justify-content: flex-start;
            gap: 1.8rem;
            align-items: flex-start;
            margin: auto;
            margin-top: 3rem;
        }
        .admin__profile {
            flex: 0 0 auto;
            width: max(15%, 300px);
            max-width: 100%;
            min-height: 420px;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: flex-start;
            align-items: flex-start;
            flex-direction: column;
            padding: 1.2rem;
            gap: 1.2rem;
            color: white;
        }
        .admin__profile>h3 {
            width: 100%;
            text-align: left;
            font-size: 1.6rem;
        }
        .admin__profile>img {
            width: 180px;
            height: 200px;
            object-fit: cover;
        }
        .admin__details {
            width: 100%;
            display: flex;
            justify-content: flex-start;
            align-items: flex-start;
            flex-direction: column;
            gap: 0.4rem;
        }
        .admin__details>p {
            font-size: 1.12rem;
        }
        .admin__details>a {
            width: auto;
            margin-top: 1.2rem;
            padding: 0.5rem 0.7rem;
            font-family: verdana;
            font-size: 1rem;
            color: #232323A0;
            background-color: transparent;
            border: 1.2px solid #232323C0;
            border-radius: 4px;
            outline: none;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.4rem;
            text-decoration: none;
            font-size: 1rem;
            transition: color 0.3s ease, background-color 0.3s ease;
        }
        .admin__right {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
            gap: 2rem;
        }
        .student__status {
            min-width: 360px;
            padding: 0.7rem;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
            gap: 0.4rem;
            border: 1.8px solid #232323;
        }
        .student__status>h4 {
            text-align: left;
            margin-bottom: 0.6rem;
            font-size: 1.12rem;
            color: darkorange;
        }
        .student__status>p {
            text-align: left;
            font-size: 0.98rem;
        }
        .food__items {
            width: 100%;
            margin-top: 2rem;
        }
        .food__items table {
            width: 100%;
            height: 100%;
            table-layout: auto;
            border-spacing: 1rem;
            border-collapse: collapse;
        }
        thead tr {
            background-color: burlywood;
        }
        thead tr th {
            color: #232323;
            border: 2px solid #232323 !important;
            padding: 0.8rem;
            text-align: center;
            font-weight: 700;
            font-size: 1rem;
            font-family: Verdana, Geneva, Tahoma, sans-serif;
        }
        tbody tr {
            background-color: azure;
        }
        tbody tr td {
            color: #232323;
            padding: 0.8rem;
            font-family: Verdana, Geneva, Tahoma, sans-serif;
            border: 1px solid #232323 !important;
            text-align: center;
            font-size: 0.98rem;
            font-weight: 400;
        }
        tbody tr td input[type="checkbox"] {
            width: 32px;
            height: 32px;
        }
        @media (hover: hover) and (pointer: fine) {
            .admin__details>a:hover,
            .admin__details>a:focus-visible {
                color: #FFF;
                background-color: #232323;
            }
        }
        td input[type="number"] {
            width: 70%;
            box-sizing: border-box;
            font-size: 1rem;
        }
        input[type="submit"],
        input[type="reset"] {
            font-size: 1rem;
        }
        input[type="text"],
        input[type="date"] {
            font-size: 1rem;
            width: 90%;
        }
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            font-size: 1rem;
            font-weight: 400;
            text-align: center;
            text-decoration: none;
            border: 1px solid transparent;
            border-radius: 0.25rem;
            cursor: pointer;
            transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, color 0.15s ease-in-out;
        }
        .btn-primary {
            color: #fff;
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <?php include 'menu.php'; ?>

    <main class="admin__main">
        <div class="admin__profile">
            <?php
            $em = $_SESSION['email'];
            $sqlget = "SELECT * FROM admin WHERE email = ?";
            if ($stmt = $dbconn->prepare($sqlget)) {
                $stmt->bind_param('s', $em);
                $stmt->execute();
                $result = $stmt->get_result();
            ?>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <h3>Admin</h3>
                <img src="../img/<?= htmlspecialchars($row['photo']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                <div class="admin__details">
                    <p>Name: <?= htmlspecialchars($row['name']) ?></p>
                    <p>E-mail: <?= htmlspecialchars($row['email']) ?></p>
                    <p>Phone number: <?= htmlspecialchars($row['phone']) ?></p>
                    <p>Address: <?= htmlspecialchars($row['address']) ?></p>
                    <a href="admin_profile.php?id=<?= htmlspecialchars($row['id']) ?>"><ion-icon name="pencil"></ion-icon> <span>Edit</span></a>
                </div>
            <?php } ?>
            <?php
                $stmt->close();
            } else {
                echo 'Error preparing query: ' . htmlspecialchars($dbconn->error);
            }
            ?>
        </div>

        <section class="admin__right">
            <h2>Dining Count Register</h2>
            <!-- Modal Structure -->
            <div class="content">
                <form id="countForm">
                    <table>
                        <tr>
                            <th colspan="8"><center><h4> <span id='tot_din'  style='color:red'></span> <span id='din_dt'  style='color:blue'></span> </h4></center></th>
                        </tr>
                        <tr>
                            <td>Date</td> 
                            <td>Boys</td> 
                            <td>Girls</td>
                            <td>Medical Staff</td>
                            <td>Mess Staff</td>
                            <td>Sports</td>
                            <td>Events</td>
                            <td>Parents</td> 
							
                        </tr>
                        <tr>
                            <td><input type='date' value='<?= date('Y-m-d') ?>' id='dining_date' class='form-control'></td> 
                            <td><input type='number' value='0' id='boys' class='form-control'></td> 
                            <td><input type='number' value='0' id='girls' class='form-control'></td> 
                            <td><input type='number' value='0' id='medical_staff' class='form-control'></td>
                            <td><input type='number' value='0' id='mess_staff' class='form-control'></td>	               
                            <td><input type='number' value='0' id='sports' class='form-control'></td>
                            <td><input type='number' value='0' id='events' class='form-control'></td>
                            <td><input type='number' value='0' id='parents' class='form-control'></td>
							
                        </tr>
                    </table>
                    <input type="submit" value='Submit' id='count_submit' class='btn btn-primary' name='count_submit'>
                    <input type="reset" value='Cancel' id='cancel_submit' class='btn btn-primary' name='cancel_submit'>
                </form>
            </div>
			
			<div id='dining_div' class="container mt-8">
				<div class="row mb-3">
					<div class="col-md-3 mb-2">
						<input type='date' value='<?= date('Y-m-d') ?>' id='start_date' class='form-control'>
					</div>
					<div class="col-md-3 mb-2">
						<input type='date' value='<?= date('Y-m-d') ?>' id='end_date' class='form-control'>
					</div>
					<div class="col-md-3 mb-2 d-flex align-items-end">
						<button type="button" class="btn btn-primary btn-block" id='all_date'>Show All</button>
					</div>
					<div class="col-md-3">
						<button type="button" class="btn btn-secondary btn-block" id='clear_date'>Clear Count</button>
					</div>
				</div>
				
			</div>
            
            <div id="allRecords">
                <h4>All Dining Counts</h4>
                <table id="recordsTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Boys</th>
                            <th>Girls</th>
                            <th>Medical Staff</th>
                            <th>Mess Staff</th>
                            <th>Sports</th>
                            <th>Events</th>
                            <th>Parents</th>
							<th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Records will be inserted here -->
                    </tbody>
                </table>
            </div>

            <div class="food__items">
                <?php
                $select_q = "SELECT * FROM menu";
                $q = $dbconn->query($select_q);
                ?>

                <table>
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Breakfast</th>
                            <th>Lunch</th>
                            <th>Snacks</th>
                            <th>Dinner</th>
                            <th>Action</th>
                        </tr>
                        <tr>
                            <th>Time</th>
                            <th>7:30 A.M - 9:30 A.M</th>
                            <th>12:00 P.M - 2:00 P.M </th>
                            <th>4:00 P.M - 5:00 P.M </th>
                            <th>8:00 P.M - 9:00 P.M</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($r = $q->fetch_assoc()) { ?>
                            <tr>
                                <td><?= htmlspecialchars($r['day']) ?></td>
                                <td><?= htmlspecialchars($r['breakfast']) ?></td>
                                <td><?= htmlspecialchars($r['lunch']) ?></td>
                                <td><?= htmlspecialchars($r['snacks']) ?></td>
                                <td><?= htmlspecialchars($r['dinner']) ?></td>
                                <td><a href="edit.php?id=<?= htmlspecialchars($r['id']) ?>" style="text-decoration:none; color:black;">
                                    <i class='material-icons'> edit</i></a></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
	
	
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
$(document).ready(function() {
    // Call fetchLatestDiningCount when the document is ready
    fetchLatestDiningCount();
	

    // Event handler for clearing records
    $("#clear_date").on("click", function() {
        $("#recordsTable tbody").empty();
    });

    // Event handler for showing all records between specified dates
    $("#all_date").on("click", function() {
        const startDate = $("#start_date").val();
        const endDate = $("#end_date").val();

        $.ajax({
            url: 'ajax_dining.php',
            type: 'POST',
            data: { action: 'fetch_all', start_date: startDate, end_date: endDate },
            success: function(response) {
                try {
                    const records = JSON.parse(response);
                    if (Array.isArray(records)) {
                        $("#recordsTable tbody").empty();
                        records.forEach(record => {
                            $("#recordsTable tbody").append(`
                                <tr>
                                    <td>${record.dining_date}</td>
                                    <td>${record.boys}</td>
                                    <td>${record.girls}</td>
                                    <td>${record.medical_staff}</td>
                                    <td>${record.mess_staff}</td>
                                    <td>${record.sports}</td>
                                    <td>${record.events}</td>
                                    <td>${record.parents}</td>
									<td>${record.all_total}</td>
                                </tr>
                            `);
                        });
                    } else {
                        console.error("Unexpected response format:", records);
                        alert("Unexpected response format.");
                    }
                } catch (e) {
                    console.error("Error parsing JSON: " + e.message);
                    alert("Error while fetching records. Please check the console.");
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error: " + status + " - " + error);
                alert("An error occurred while fetching records. Please check the console.");
            }
        });
    });

    // Event handler for form submission
    $("#countForm").on("submit", function(e) {
        e.preventDefault();

        const itemData = {
            action: 'add_dining',
            dining_date: $("#dining_date").val(),
            boys: parseInt($("#boys").val(), 10) || 0,
            girls: parseInt($("#girls").val(), 10) || 0,
            medical_staff: parseInt($("#medical_staff").val(), 10) || 0,
            mess_staff: parseInt($("#mess_staff").val(), 10) || 0,
            parents: parseInt($("#parents").val(), 10) || 0,
            events: parseInt($("#events").val(), 10) || 0,
            sports: parseInt($("#sports").val(), 10) || 0
        };

        const totalCount = itemData.boys + itemData.girls + itemData.medical_staff + itemData.mess_staff + itemData.sports + itemData.events + itemData.parents;

        if (!confirm(`Are you sure you want to submit this form?\n\nTotal count: ${totalCount} for dining date ${itemData.dining_date}`)) {
            return;
        }

        $.ajax({
            url: 'ajax_dining.php',
            type: 'POST',
            data: itemData,
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    alert(result.status === 'success' ? 'Success: ' + result.message : 'Failed: ' + result.message);
                    $("#countForm")[0].reset();
					fetchLatestDiningCount();
                } catch (e) {
                    alert("An error occurred: " + e.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error: " + status + " - " + error);
                alert("An error occurred while processing your request. Please check the console for details.");
            }
        });
    });
	
	 // Event handler for dining date change
    $("#dining_date").on("change", function() {
        // Reset values of boys, girls, sports, events, and parents to 0
        $("#boys").val(0);
        $("#girls").val(0);
        $("#medical_staff").val(0);
		$("#mess_staff").val(0);
		$("#sports").val(0);
        $("#events").val(0);
        $("#parents").val(0);
		//fetchLatestDiningCount();
    });
	
    // Function to fetch the latest dining count
    function fetchLatestDiningCount() {
		
        $.ajax({
            url: 'ajax_dining.php',
            type: 'POST',
            data: { action: 'fetch_latest' },
            success: function(response) {
                try {
					
                    const record = JSON.parse(response);
					//alert("scu");
                    console.log("Fetch Latest Dining Count Response:", record); // Better for debugging

                    if (record.status === 'error') {
                        console.error("Error from server:", record.message);
                        return;
                    }
					
                    $("#dining_date").val(record.dining_date || '');
                    $("#boys").val(record.boys || 0);
                    $("#girls").val(record.girls || 0);
                    $("#medical_staff").val(record.medical_staff || 0);
                    $("#mess_staff").val(record.mess_staff || 0);
                    $("#sports").val(record.sports || 0);
                    $("#events").val(record.events || 0);
                    $("#parents").val(record.parents || 0);					
					//$("#tot_din").val(record.all_total || 0);
					 $("#tot_din").text("Dining Count :  " +record.all_total  );
					 $("#din_dt").text( ' on Date : '+record.dining_date );
                } catch (e) {
                    console.error("Error parsing JSON: ", e.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error: ", status, error);
            }
        });
    }
});
</script>
</body>

</html>
