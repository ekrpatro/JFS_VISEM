<?php
include("admindbconn.php");?>

<!DOCTYPE html>
<html>

<head>
    <title>Dashboard</title>
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
            background-size: cover;
        }

        /* Form */

        h1 {
            width: 700px;
            max-width: 100%;
            margin: 2rem auto;
            font-family: Verdana, Geneva, Tahoma, sans-serif;
        }

        .purchase__form {
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

        .purchase__form > h2 {
            text-align: left;
            color: #232323;
            font-family: verdana;
            font-size: 1.6rem;
            margin-bottom: 0.7rem;
        }

        .form__field {
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
            gap: 0.6rem;
        }

        .form__field label {
            text-align: left;
            color: #333333;
            font-family: verdana;
            font-size: 1rem;
        }

        .form__field input, .form__field select {
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

        .form__field input:focus, .form__field select:focus {
            color: #232323;
            border-color: #232323;
        }

        .multi {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.8rem;
        }

        .other__items {
            display: none;
        }

        .purchase__form > button {
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

        /* Form */

        @media (hover: hover) and (pointer: fine) {

            .purchase__form > button:is(:hover, :focus-visible) {
                color: #FFF;
                background-color: #232323;
            }
        }

    </style>
    <script>
        function multiply() {
            var a = Number(document.getElementById('qty').value);
            var b = Number(document.getElementById('rate').value);
            var c = a * b;
            document.getElementById('total').value = c;
        }

        function CheckItem(val) {
            var element = document.getElementById('new_item_name');
            if ( val == 'others')
                element.style.display = 'block';
            else
                element.style.display = 'none';
        }

        function submitForm(event) {
            event.preventDefault();

           // var formData = new FormData(document.querySelector('.purchase__form'));
           var form = document.querySelector('.purchase__form');
           var formData = new FormData(form);

            fetch('ajax_purchase_form.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Item added successfully');
                    form.reset();
                } else {
                    alert('Item couldn\'t be added');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
            
        }
    </script>
</head>

<body>
    <!-- Your existing HTML code -->

    <?= include('./menu.php') ?>

    <h1>Purchase Form</h1>

    <form onsubmit="submitForm(event)" class="purchase__form">
        <input type="hidden" name="submitted" value="true" />

        <h2>Add Purchase Item</h2>

        <div class="form__field">
            <label style="margin-top:12px">Invoice Number</label>
            <input type='text' name='invoice_no' id='invoice_no' size=50>
        </div>

        <div class="form__field">
            <label style="margin-top:12px">Supplier Name</label>
            <select id="seller_id" name="seller_id" required>
              
				  <?php
                $query = "SELECT seller_id, shopname FROM seller"; // Corrected SQL query
                $result1 = mysqli_query($dbconn, $query);

                if ($result1) {
                    while ($row1 = mysqli_fetch_array($result1)) { ?>
                        <option value="<?php echo $row1['seller_id']; ?>"><?php echo $row1['shopname']; ?></option>
                    <?php }
                } else {
                    echo "<option value=''>No suppliers found</option>";
                }
                ?>
            </select>
        </div>

        <div class="form__field">
            <label>Item Name</label>
            <select class="itemname" value="itemid" name="itemid" onchange='CheckItem(this.value);'>
                <option>--Item Name--</option>
                <?php
                $query = "SELECT * FROM `item`";
                $result1 = mysqli_query($dbconn, $query);
                ?>
                <?php while ($row1 = mysqli_fetch_array($result1)) :; ?>
                    <option value="<?php echo $row1[0]; ?>"><?php echo $row1[1]; ?></option>
                <?php endwhile ?>
                <option value="others">others</option>
                <input type="text" name="new_item_name" id="new_item_name" class="other__items" placeholder="Enter New Item Name" />
            </select>
        </div>

        <div class="form__field">
            <label>Quantity</label>
            <div class="multi">
                <input type="text" placeholder="Enter Quantity" name="qty" id="qty" value="" onkeyup="multiply()" />
                <select class="unit" name="qunit">
                    <option value="kg">Kg</option>
                    <option value="Ltr">Litre</option>                   
                    <option value="Pkts">Packets</option>
					<option value="Nos">Nos</option>
					<option value="Btl">Bottle</option>
                </select>
            </div>
        </div>

        <div class="form__field">
            <label>Rate</label>
            <div class="multi">
                <input type="text" placeholder="Enter Rate" name="rate" value="" id="rate" onkeyup="multiply()">
                <select class="unit" name="runit">
                    <option value="/Kg">/Kg</option>
                    <option value="/Ltr">/Litre</option>
                    <option value="/Pkts">/Packets</option>
                    <option value="/Nos">/Nos</option>
                </select>
            </div>
        </div>

        <div class="form__field">
            <label style="margin-top: 20px;">Total Price</label>
            <input type="text" placeholder="Total Cost" id="total" name="total" readonly>
        </div>

        <div class="form__field">
            <label>Purchased Date</label>
            <input type="date" placeholder="Enter purchasing Date" name="date" required>
        </div>

        <button type="submit">Submit</button>
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
