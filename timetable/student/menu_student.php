

<html lang="en">
    
<!-- HEAD SECTION  -->
<head>
    <title>Home | Stock Management System</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link type="text/css" rel="stylesheet" href="css/nav.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
   

<style>
/* General Styling */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Ensure full-width layout */
.container {
    width: 100%;
    max-width: 1200px;
    margin: auto;
    padding: 10px;
}

/* Logo Styling */
.img__header img {
    display: block;
    width: 100%;
    max-width: 250px;
    height: auto;
    margin: auto;
}

/* Navigation Styling */
.app__nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #333;
    padding: 15px;
}

.app__nav a {
    color: white;
    text-decoration: none;
    padding: 10px 15px;
}

/* Navigation Menu */
.nav__center {
    display: flex;
    align-items: center;
}

/* Hide menu initially on small screens */
.nav__menu {
    display: flex;
    gap: 15px;
}

.sm__screen {
    display: none;
    background: none;
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
}

/* Mobile Responsiveness */
@media screen and (max-width: 768px) {
    .nav__menu {
        display: none;
        flex-direction: column;
        background-color: #222;
        position: absolute;
        top: 60px;
        right: 20px;
        width: 200px;
        padding: 10px;
        border-radius: 5px;
    }

    .nav__menu a {
        display: block;
        padding: 10px;
        color: white;
        text-align: center;
    }

    .nav__menu.visible {
        display: flex;
    }

    .sm__screen {
        display: block;
    }
}
</style>

<script>
    function toggleMenuBar() {
        document.querySelector('.nav__menu').classList.toggle('visible');
    }
</script>

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body>
<div class="container">
    <div class="img__header">
        <img src="../img/college_logo.jpg" alt="College Logo">
    </div>    

    <nav class="app__nav">
        <a href="#">Dashboard</a>

        <div class="nav__center">
            <button class="sm__screen" type="button" onclick="toggleMenuBar()">
                <ion-icon name="menu-outline"></ion-icon>
            </button>
            <div class="nav__menu">
                <a href="./gatepass.php">Apply GatePass</a>	
                <a href="./view_gatepass.php">Gatepass Status</a>
                <a href="../admin/logout.php">Logout</a>
            </div>
        </div>
    </nav>
</div>
</body>
</html>