<div>   
    <div class="img__header">
        <img src="../img/college_logo.jpg" alt="College Logo" >
    </div>    
    <nav class="app__nav">
        <a href="#">Dashboard</a>
       
        <div class="nav__center">
            <div class="nav__menu">
                
				<a href="./gatepass.php">ApplyGatePass</a>	
				<a href="./view_gatepass.php">GatepassStatus</a>
						
                			
                <a href="../admin/logout.php">Logout</a>
            </div>
            <button class="sm__screen" type="button" onclick="toggleMenuBar()">
                <ion-icon name="menu-outline"></ion-icon>
            </button>
        </div>
    </nav>
	 <style> img {
    display: block;
    width: 100vw; /* Full width of viewport */
    height: auto; /* Maintain aspect ratio */
  }
  </style>
    <script>
        const menuBar = document.querySelector('.nav__menu');
        function toggleMenuBar() {
            menuBar.classList.toggle('visible');
        }
    </script>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</div>