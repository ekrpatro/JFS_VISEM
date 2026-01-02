<!DOCTYPE html>
<html lang="en">
    
<!-- HEAD SECTION  -->
<head>
    <title>Home | TimeTable</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link type="text/css" rel="stylesheet" href="css/nav.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            overflow-x: hidden;
        }
.img__header {
    width: 100%;
    height: 160px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.img__header img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    mix-blend-mode: multiply;
}

        .ximg__header {
            width: 100%;
            height: 160px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .ximg__header img {
            width: 100%;
            height: 100%;
            object-fit: fill;
        }
        
        .app__nav {
            width: 100%;
            max-width: 100%;
            height: 82px;
            background-color: #1b6ef399;
        }

        .app__nav > a {
            display: none;
        }

        .nav__center {
            width: 1400px;
            max-width:100%;
            height: 100%;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.8rem;
        }

        .nav__menu {
            width: 100%;
            display: flex;
            flex-direction: flex-start;
            align-items: center;
            gap: 1.2rem;
        }

        .nav__menu a {
            color: #232323;
            text-decoration: none;
            font-family: verdana;
            font-size: 1.12rem;
            border-bottom: 1px solid transparent;
            transition: border 0.3s ease;
            padding: 0.4rem;
            outline: none;
        }

        .sm__screen {
            display: none;
        }

        @media (hover: hover) and (pointer: fine) {
            .nav__center a:is(:hover, :focus-visible) {
                border-color: #232323;
            }
        }

        @media all and (max-width: 800px) {

            .app__nav > a {
                color: #232323;
                text-decoration: none;
                font-family: verdana;
                font-size: 1.12rem;
                border-bottom: 1px solid transparent;
                transition: border 0.3s ease;
                padding: 0.4rem;
                outline: none;
                display: block;
            }

            .sm__screen {
                display: flex;
                justify-content: center;
                align-items: center;
                width: 40px;
                height: 40px;
                position: relative;
                z-index: 101;
                margin-right: 24px;
            }
            
            .sm__screen ion-icon {
                font-size: 2rem;
            }

            .app__nav {
                position: relative;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .nav__center {
                flex: 0;
            }

            .app__nav > a {
                flex: 1;
            }

            .nav__menu {
                position: absolute;
                width: 280px;
                height: 100vh;
                background-color: #BBBBBB;
                display: flex;
                flex-direction: column;
                justify-content: flex-start;
                align-items: center;
                right: 0;
                top: 0;
                padding: 0.6rem;
                transform: translateX(900px);
                transition: transform 0.3s ease;
                z-index: 100;
                padding-top: 6rem;
            }

            .nav__menu > a:nth-child(1) {
                display: none;
            }

            .nav__menu.visible {
                transform: translateX(0px);
            }
        }

     </style>
</head>
<body>

    <!-- Header Start -->

    <div class="img__header">
        <img src="./img/iare.jpg" alt="Collage Logo">
    </div>


    <nav class="app__nav">
        <a href="#">Home</a>
        <div class="nav__center">
            <div class="nav__menu">
                <a href="#">Home</a>
                <a href="html/adminlogin.php">Admin</a>
               <!-- <a href="html/adminregistration.php">Registration</a>
                <a href="html/studentregistration.php">Student Registration</a>
                <a href="html/studentlogin.php">Student Login</a>-->
            </div>
            <button class="sm__screen" type="button" onclick="toggleMenuBar()">
                <ion-icon name="menu-outline"></ion-icon>
            </button>
        </div>
    </nav>

    <!-- Header Start -->
    
     <script>
        var slideIndex = 1;
        showDivs(slideIndex);
        
        function plusDivs(n) {
            showDivs(slideIndex += n);
            }
        
        function showDivs(n) {
            var i;
            var x = document.getElementsByClassName("mySlider");
            if (n > x.length) { slideIndex = 1 }    
            if (n < 1) {slideIndex = x.length}
            for (i = 0; i < x.length; i++)
            {
                x[i].style.display = "none"; 
            }
            x[slideIndex-1].style.display = "block";  
        }
    </script>
    
    <!-- Automatic Image Slider Script-->    

    <script>
         var myIndex = 0;
         carousel();
         function carousel() {
            var i;
            var x = document.getElementsByClassName("mySlider");
            for (i = 0; i < x.length; i++) 
            {
                x[i].style.display = "none";  
            }
            myIndex++;
            if (myIndex > x.length) {myIndex = 1}    
            x[myIndex-1].style.display = "block";  
            setTimeout(carousel, 5000); // Change image every 5 seconds
        }                                 
    </script> 

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

    <!-- Automatic Image Slider Script Ends-->
    
                            <!--  Main Content  -->
     <!-- <div id="content">
      <h3> <b> About Sarojini Naidu Hall </b> </h3>
        <p>The Hall was named after the well-known freedom fighter and first lady Governor of a state in Independence India, Mrs. Sarojini Naidu. After her name Sarojini Naidu Hall was established in 1970, with the aim of providing accommodation to the girls studying in Aligarh Muslim University.</p>
        <p style="text-align: justify; margin: 10px; font-size: 17px; font-family: serif;" >Around 657 girls of different courses i.e. MBBS, BDS, MD, MS, P.G. Diploma Medical/M.Ch.Plastic Surgery, M.Sc. (Industrual Chemistry), M.Sc. (Polymer Science & Technology), Professional Courses, Diploma in Nursing, Diploma Courses (Women's and Boy's Polytechnic), B.P.Ed, M.P.Ed, M.Sc. (Biotechnology), Ph.D, M.Phil,  are staying in S.N. Hall and its Annexes. Due to increase in intake in different course, there is accommodation crises.</p>
    
    </div>  -->
    
    <!--
     <div id="content1" >
         <img src="img/provost.PNG" width="200px" height="250px" style="border: 5px black;">
         <p style="text-align: center;"> <b> Provost: </b> Prof. Seema Hakim<br />
                                         <b> Telephone: </b> 6723415907 <br/>
                                         <b> E-mail: </b> abc@gmail.com 
         </p>   
     </div>
        -->                   
                                <!-- Footer-->   
   <!-- <div id="footer" style="width:100%;margin-left:-1px; padding-bottom:20px;"><br> -->
        <!-- <center><p style="color: white; font-family: cursive; margin-top: 10px; font-size: 15px; margin-left:40%">SAROJINI NAIDU HALL<br> A.M.U, Aligarh , 202002</p></center>
        <p style="margin-left: 50px; color: white;">Copyright &copy; 2017 -All Rights Reserved</p> -->
    <!-- </div>  -->



<!-- 


<div id="header">    
        <img src="img/college_logo.jpg" alt="" class="logo_def">
    </div>                              Header Ends
    
                                        Navigation Bar Start
    
                                        <div class="navbar" style="">
        <ul>
            <a href="#">Home</a>
            
            Dropdown Menu for Registration 
            <div class="dropdown">
                <button class="dropbtn">Registration 
                    <i class="fa fa-caret-down"></i>
                </button>
                <div class="dropdown-content">
                    <a href="html/adminregistration.php">Admin</a>
                    <a href="html/studentregistration.php">Student</a>
                </div>
            </div>
                  
                 Dropdown Menu for Login 
            <div class="dropdown">
                <button class="dropbtn">Login
                    <i class="fa fa-caret-down"></i>
                </button>
                <div class="dropdown-content">
                    <a href="html/adminlogin.php">Admin</a>
                    <a href="html/studentlogin.php">Student</a>
                </div> 
            </div>
                      
            <a href="html/facilities.php">Facilities</a>
            <a href="html/help.php">Help</a>
                      
        </ul>
     </div>              Navigation Bar Ends                  

                        Image Slider 
     <img class="mySlider" src="img/sarojini.jpg">
     <img class="mySlider" src="img/s.n-hall.jpg">
     <img class="mySlider" src="img/IMG_20171017_191905.jpg">
    -->
                        <!-- button on slider-->
     <!-- <button id="button-display-left" onclick="plusDivs(-1)">&#10094;</button>
     <button id="button-display-right" onclick="plusDivs(+1)">&#10095;</button>
    -->
                     <!-- Manual Image Slider Script-->     