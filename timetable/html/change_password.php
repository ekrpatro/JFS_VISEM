<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("./dbconn.php");

if (!isset($_SESSION["temp_user"])) {
    header("Location: adminlogin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old_password = md5(trim($_POST['old_password'])); // Hash old password
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);
    $user_name = $_SESSION["temp_user"];

    // Check if old password is correct
    $sql = "SELECT password FROM admin WHERE user_name = ?";
    $stmt = $dbconn->prepare($sql);
    $stmt->bind_param("s", $user_name);
    $stmt->execute();
    $stmt->bind_result($stored_password);
    $stmt->fetch();
    $stmt->close();

    if ($stored_password !== $old_password) {
        echo "<script>alert('Incorrect old password!');</script>";
    } elseif ($new_password !== $confirm_password) {
        echo "<script>alert('New passwords do not match!');</script>";
    } else {
        $hashed_new_password = md5($new_password); // Hash new password

        $update_sql = "UPDATE admin SET password = ? WHERE user_name = ?";
        $update_stmt = $dbconn->prepare($update_sql);
        $update_stmt->bind_param("ss", $hashed_new_password, $user_name);
        $update_stmt->execute();
        $update_stmt->close();

        unset($_SESSION["temp_user"]);
        echo "<script>alert('Password changed successfully! Please log in.'); window.location.href = 'adminlogin.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
</head>
<body>
    <h2>Change Password</h2>
    <form action="change_password.php" method="POST">
        <label for="old_password">Old Password:</label>
        <input type="password" name="old_password" required>
        <br>
        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" required>
        <br>
        <label for="confirm_password">Confirm New Password:</label>
        <input type="password" name="confirm_password" required>
        <br>
        <button type="submit">Update Password</button>
    </form>
</body>
</html>
