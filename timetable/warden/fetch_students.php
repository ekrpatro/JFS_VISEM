<?php
include("../html/dbconn.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $gender = mysqli_real_escape_string($dbconn, $_POST['gender']);
    $block_name = mysqli_real_escape_string($dbconn, $_POST['block_name']);

    $sql = "SELECT `room_no`, `rollno`, `name`, `gender`, `studying_year`, `branch`, `college_name`, `block_name` 
            FROM `student` 
            WHERE `gender` = '$gender' AND `block_name` = '$block_name'";

    $result = mysqli_query($dbconn, $sql);

    if (!$result) {
        die("Error fetching data: " . mysqli_error($dbconn));
    }

    $sno = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>" . (++$sno) . "</td>                
                <td>{$row['room_no']}</td>
                <td><input type='checkbox' name='op[]' value='{$row['rollno']}'> {$row['rollno']}</td>
                <td>{$row['name']}</td>
                <td>{$row['gender']}</td>
                <td>{$row['branch']}</td>
                <td>{$row['studying_year']}</td>
                <td>{$row['college_name']}</td>
              </tr>";
    }
}
?>
