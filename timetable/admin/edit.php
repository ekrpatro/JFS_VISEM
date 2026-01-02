<?php
include("admindbconn.php");

$id = $_GET['id'];

//echo $id;
$sq = mysqli_query($dbconn, ("select * from menu where id=" . $id));
$r = mysqli_fetch_array($sq);

if (isset($_POST['submit'])) {
    $breakfast = $_POST['breakfast'];
    $lunch = $_POST['lunch'];
    $snacks = $_POST['snacks'];
    $dinner = $_POST['dinner'];


    $q = "update menu set breakfast='$breakfast', lunch='$lunch', snacks='$snacks', dinner='$dinner' where id=$id ";
    $query = mysqli_query($dbconn, ($q));
    if ($query) {
        header("location: home.php");
    }
}

?>



<html>

<head>
    <title>Home::</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="../css/menu.css">
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

        .edit__form {
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
            gap: 1rem;
        }

        .edit__form > h2 {
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
            font-family: Verdana, Geneva, Tahoma, sans-serif;
            color: #232323D0;
            border-radius: 4px;
            transition: color 0.3s ease, border-color 0.3s ease;
        }

        .form__field input:not(:read-only):focus {
            color: #232323;
            border-color: #232323;
        }

        .edit__form > button {
            width: auto;
            padding: 0.5rem 0.7rem;
            font-family: verdana;
            font-size: 1rem;
            color: #232323A0;
            background-color: transparent;
            border: 1.2px solid #232323C0;
            border-radius: 4px;
            outline: none;
            margin-top: 2rem;
            cursor: pointer;
            transition: color 0.3s ease, background-color 0.3s ease;
        }

        @media (hover: hover) and (pointer: fine) {

            .edit__form > button:is(:hover, :focus-visible) {
                color: #FFF;
                background-color: #232323;
            }
        }

    </style>
</head>

<body>
    <?php include 'menu.php'; ?>

    <!--Main Content start-->
    <form name="#" method="post" enctype="multipart/form-data" class="edit__form">
        <h2>MESS MENU</h2>

            <div class="form__field">
                <label for="day">Day</label>
                <input type="text" id="day" name="day" value="<?php echo $r['day']; ?>" readonly />
            </div>

            <div class="form__field">
                <label for="breakfast">Breakfast</label>
                <input type="text" name="breakfast" placeholder="Enter Breakfast" value="<?php echo $r['breakfast']; ?>" />
            </div>

            <div class="form__field">
                <label for="lunch">Lunch</label>
                <input type="text" name="lunch" placeholder="Enter Lunch" value="<?php echo $r['lunch']; ?>" />
            </div>

            <div class="form__field">
                <label for="snacks">Snacks</label>
                <input type="text" name="snacks" placeholder="Enter Snacks" value="<?php echo $r['snacks']; ?>" />
            </div>

            <div class="form__field">
                <label for="dinner">Dinner</label>
                <input type="text" name="dinner" placeholder="Enter Dinner" value="<?php echo $r['dinner']; ?>" />
            </div>

            <button type="submit" name="submit" class="button">Submit</button>
    </form>

</body>


</html>