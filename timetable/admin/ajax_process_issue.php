<?php

session_start();
include("admindbconn.php");
if ( $_SESSION["role"] != 'ADMIN'   || !isset($_SESSION['user_name']) ) {
  echo "Invalid User : ".$_SESSION["role"] ;
  exit(0);
}


if(isset($_POST['op']) && isset($_POST['txt']) && isset($_POST['unit_prices']) && isset($_POST['issue_date']) && isset($_POST['issue_category'])) {
  
  $options = $_POST['op'];
  $qun = $_POST['txt'];  
  $unit_prices = $_POST['unit_prices'];
  $size = count($options);
  $issue_date = $_POST['issue_date']; 
  $issue_category = $_POST['issue_category'];
  $issue_total = 0.0;

  $values = array();
  for ($i = 0; $i < $size; $i++) {
      $itemid = mysqli_real_escape_string($dbconn, $options[$i]);
      $quantity = mysqli_real_escape_string($dbconn, $qun[$i]);
	  $unit_price = mysqli_real_escape_string($dbconn, $unit_prices[$i]);
      $values[] = "('$itemid', '$quantity','$issue_date','$issue_category','$unit_price')";	  
	  $issue_total += $quantity * $unit_price;	  
  }

  $valuesString = implode(", ", $values);
 // $insertQuery = "INSERT INTO issueitem (itemid, quantity, issue_date,issue_category,unit_price) VALUES $valuesString";
 $insertQuery = "
    INSERT INTO issueitem (itemid, quantity, issue_date, issue_category, unit_price)
    VALUES $valuesString
    ON DUPLICATE KEY UPDATE
    quantity = quantity + VALUES(quantity)
  ";
  
  mysqli_query($dbconn, $insertQuery) or die(mysqli_error($dbconn));

  $updateQuery = "UPDATE stock SET quantity = CASE ";
  for ($i = 0; $i < $size; $i++) {
      $itemid = mysqli_real_escape_string($dbconn, $options[$i]);
      $quantity = mysqli_real_escape_string($dbconn, $qun[$i]);      
      $updateQuery .= " WHEN itemid = '$itemid' AND quantity >= '$quantity' THEN quantity - '$quantity' ";
  }
  $updateQuery .= " ELSE quantity END WHERE itemid IN ('" . implode("','", $options) . "')";
  mysqli_query($dbconn, $updateQuery) or die(mysqli_error($dbconn));
  
  echo json_encode(["status" => "success", "message" => "Total Cost = $insertQuery"]);
} else {
  echo json_encode(["status" => "error", "message" => "Invalid input"]);
}
?>
