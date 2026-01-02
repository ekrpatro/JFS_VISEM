<?php
// Include database connection
include("../html/dbconn.php");

// Validate and fetch input parameters
$staying_date = isset($_POST['staying_date']) ? trim($_POST['staying_date']) : null;
$college_name = isset($_POST['college_name']) ? trim($_POST['college_name']) : null;

// Check if required parameters are provided
if (!$staying_date || !$college_name) {
    die("<p style='text-align:center;color:red;'>Invalid input parameters.</p>");
}

// Convert date format
$formatted_staying_date = date('d-m-Y', strtotime($staying_date));

// Prepare SQL Query with placeholders (Prevents SQL Injection)
if ($college_name == 'ALL') {
    $sql = "SELECT a.rollno, a.staying_date, room_no, UPPER(name) AS name, gender, college_name, branch, studying_year  
            FROM staying_college_hours a 
            INNER JOIN student b ON a.rollno = b.rollno  
            WHERE staying_date = ?";
    $stmt = mysqli_prepare($dbconn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $staying_date);
} 
else 
{
    $sql = "SELECT a.rollno, a.staying_date, room_no, UPPER(name) AS name, gender, college_name, branch, studying_year  
            FROM staying_college_hours a 
            INNER JOIN student b ON a.rollno = b.rollno  
            WHERE staying_date = ? AND college_name = ?";
    $stmt = mysqli_prepare($dbconn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $staying_date, $college_name);
}

// Execute the query
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Check if rows exist
if (mysqli_num_rows($result) > 0) {
    echo "<table class='pdftable' align='center' width='800px'>";
	if($college_name=='IARE')
	{
		echo "<tr><th colspan='6' style='border: none; padding-bottom: 15px;' ><img src='../img/iare_logo.jpg' alt='Logo' style='display: block; margin: 0 auto;'></th></tr>";
		echo "<tr><th colspan='6' align='center' class='pdfdata' style='font-size:18px; border: none; color:red;'>List of students staying in hostel during college hours</th></tr>";
    }
	else
	{
		//$college_name = $college_name === '0' ? 'All' : $college_name;
		 $college_display_name = ($college_name == 'ALL' || empty($college_name)) ? 'All' : $college_name;
      
		echo "<tr><th colspan='6' class='pdfdata' align='center' style='font-size:18px; border: none; color: red; padding-bottom: 15px;'>List of ".$college_display_name." students staying in hostel during college hours</th></tr>";
  
	}
	
	echo "<tr><th colspan='6' style='text-align:right; font-weight:bold; border: none;'>Date: " . htmlspecialchars($formatted_staying_date) . "</th></tr>";
	echo "</table>";
	
    echo "<table class='pdftable'  align='center' border='1' width='800px' style='border-collapse:collapse; margin-top:10px;'>";
  echo "<tr style='background-color: #fc9864;' >
            <th style='padding: 8px;'>S.No</th> 
            <th style='padding: 8px;'>Room No</th> 
            <th style='padding: 8px;'>Roll No.</th> 
            <th style='padding: 8px;'>Name</th> 
            <th style='padding: 8px;'>Year</th> 
            <th style='padding: 8px;'>Branch</th>
          </tr>";

    $i = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td style='text-align:center; padding: 8px; color: #000;'>" . ++$i . "</td>";
        echo "<td style='text-align:center; padding: 8px; color: #000;'>" . htmlspecialchars($row['room_no']) . "</td>";
        echo "<td style='text-align:center; padding: 8px; color: #000;'>" . htmlspecialchars($row['rollno']) . "</td>";
        echo "<td style='text-align:left; padding: 8px; color: #000;'>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td style='text-align:center; padding: 8px; color: #000;'>" . htmlspecialchars($row['studying_year']) . "</td>";
        echo "<td style='text-align:center; padding: 8px; color: #000;'>" . htmlspecialchars($row['branch']) . "</td>";
        echo "</tr>";
    }
	 if ($college_name === "IARE") {
        echo "<tr>
                <th colspan='6' style='text-align:right; font-weight:bold; padding: 30px;border-bottom: none;'>PRINCIPAL</th>
              </tr>";
    }
    echo "</table>";
	 
	
} else {
    echo "<p style='text-align:center;color:red;'>No data found for the selected date and college.</p>";
}

// Close statement and database connection
mysqli_stmt_close($stmt);
mysqli_close($dbconn);
?>
