<?php 
    session_start();
    include("dbconn.php");
    mysqli_select_db($dbconn , $dbName);
    
    if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $username = strtolower(trim($_POST['username']));
        $password = strtolower(trim($_POST['password']));
       /* $sql = "SELECT * FROM admin WHERE name = '$myusername' and password = '$mypassword'";
       // $result=mysqli_query($dbconn, $sql);
        if(mysqli_num_rows($result)==1){
            $_SESSION["username"] = $myusername;
            header( "Location:../admin/home.php");
            }
        else{
            echo "<script>alert('Incorrect Username or Password');</script>";
            }*/
			if( $username=='admin' && $password =='1234' ){
            $_SESSION["username"] = $username;
            header( "Location:../admin/home.php");
            }
    }
?>

<!DOCTYPE html>
<html lang="en">
<!-- HEAD SECTION -->
<head>
    <title>Login | Timetable</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link type="text/css" rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            width: 100%;
            overflow-x: hidden;
            background-image: url(../img/IMG-20150820-WA0074.jpg);
            background-size: cover;
        }
        
        .login__form {
            width: 700px;
            max-width: 100%;
            padding: 0.8rem;
            background-color: #FFFFFF;
            box-shadow: 0 0 4px 2px #23232310;
            margin: 2.4rem auto;
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
            gap: 0.2rem;
        }

        .login__form > h2 {
            text-align: left;
            color: #232323;
            font-family: verdana;
            font-size: 1.6rem;
            margin-bottom: 1rem;
        }

        .form__field {
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .form__field label {
            text-align: left;
            color: #333333;
            font-family: verdana;
            font-size: 1rem;
        }

        .form__field input {
            width: 100%;
            padding: 0.6rem 0.6rem;
            background-color: transparent;
            outline: none;
            border: 1.2px solid #23232370;
            font-family: verdana;
            color: #232323A0;
            border-radius: 4px;
            transition: color 0.3s ease, border-color 0.3s ease;
        }

        .form__field input:focus {
            color: #232323;
            border-color: #232323;
        }

        .login__form > button {
            width: auto;
            padding: 0.5rem 0.7rem;
            font-family: verdana;
            font-size: 1rem;
            color: #232323A0;
            background-color: transparent;
            border: 1.2px solid #232323C0;
            border-radius: 4px;
            outline: none;
            cursor: pointer;
            transition: color 0.3s ease, background-color 0.3s ease;
        }

        @media (hover: hover) and (pointer: fine) {
        
            .login__form > button:is(:hover, :focus-visible) {
                color: #FFF;
                background-color: #232323;
            }
        }

    </style>
</head>
                    
<body>

    <?= include '../admin/menu.php' ?>
    
    
    <!--  Login Form  -->

    <form action="../html/adminlogin.php" method="POST" class="login__form">

        <h2>Login</h2>

        <div class="form__field">
            <label>UserName</label>
            <input id="name" name="username" value="admin" type="text" placeholder="username" required><br/>
        </div>

        <div class="form__field">
            <label>Password</label>
            <input id="password" name="password" value="1234" type="password" placeholder="Enter Password"><br/>
        </div>

        <button class="button" ><span>Submit</span></button>
    </form>

    <script>
        const menuBar = document.querySelector('.nav__menu');
        function toggleMenuBar() {
            menuBar.classList.toggle('visible');
        }
    </script>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>
</html>
    
<!--     <php include 'header.php'; > -->