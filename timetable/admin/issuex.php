<?php
include("admindbconn.php");
?>
<html>
<head>
  <title>Issue Item</title>
  <link type="text/css" rel="stylesheet" href="../css/menu.css">
  <link rel="stylesheet" href="../font-awesome-4.7.0/css/font-awesome.min.css">
  <link href="../bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link type="text/css" rel="stylesheet" href="../css/sc.css">

  <script type="text/javascript" src="http://code.jquery.com/jquery-1.8.2.js"></script>
  <script type="text/javascript">
    $(document).on('click', 'input[name="op[]"]', function() {
      var checked = $(this).prop('checked'); // true or false
      if (checked) {
        $(this).parents('tr').find('td input[type="text"]').removeAttr('disabled');
      } else {
        $(this).parents('tr').find('td input[type="text"]').attr('disabled', 'disabled');
      }
    });
  </script>
</head>
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

  .issue__head {
    width: 100%;
    max-width: 100%;
    margin: 2rem auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 0.8rem;
  }

  .issue__head h1 {
    text-align: left;
    font-size: 2rem;
    font-family: Verdana, Geneva, Tahoma, sans-serif;
  }

  .issue__form {
    width: 800px;
    max-width: 100%;
    margin: 2rem auto;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    align-items: flex-end;
  }

  .issue__form table {
    width: 100%;
    height: 100%;
    background-color: #232323;
    table-layout: auto;
    border-spacing: 1rem;
    border-collapse: collapse;
  }

  thead tr th {
    color: #FFFFFF;
    border-bottom: 2px solid #FFFFFF !important;
    padding: 0.8rem;
    -webkit-box-flex: 0;
    -ms-flex: 0 0 auto;
    flex: 0 0 auto;
    text-align: center;
    font-weight: 500;
    font-size: 1rem;
    font-family: Verdana, Geneva, Tahoma, sans-serif;
  }

  tbody tr td {
    padding: 0.8rem;
    font-family: Verdana, Geneva, Tahoma, sans-serif;
    border-bottom: 1px solid #FFFFFF !important;
    -webkit-box-flex: 0;
    -ms-flex: 0 0 auto;
    flex: 0 0 auto;
    text-align: center;
    font-size: 0.98rem;
    font-weight: 400;
    color: #FFFFFF;
  }

  tbody tr td input[type="checkbox"] {
    width: 32px;
    height: 32px;
  }

  tbody tr td input[type="text"], .issue__head input, .issue__head select {
    padding: 0.3rem 0.4rem;
    background-color: transparent;
    outline: none;
    font-family: verdana;
    border: 1.2px solid #FFFFFF90;
    color: #232323;
    border-radius: 4px;
    background-color: #FFFFFF;
    transition: color 0.3s ease, border-color 0.3s ease;
  }

  .issue__head input, .issue__head select {
    border: 1.2px solid #23232390;
    color: #232323;
    background-color: #FFFFFF;
  }

  tbody tr td input[type="text"]:disabled {
    border: 1.2px solid #FFFFFF90;
    background-color: transparent;
    color: #FFFFFFA0;
  }

  .issue__form > button {
    width: fit-content;
    padding: 0.7rem 0.9rem;
    margin: 1rem;
  }
  .grid-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
  }

  .box {
    padding: 20px;
    box-sizing: border-box;
  }

  .box2 {
    background-color: #d4edda;
  }

  /* Form */

  @media (hover: hover) and (pointer: fine) {

    .options > button:nth-child(1):is(:hover, :focus-visible) {
      color: #FFFFFF;
      background-color: #FF0000D0;
      border-color: #FF0000D0;
    }

    .options > button:nth-child(2):is(:hover, :focus-visible) {
      color: #232323;
      background-color: #00ff0090;
      border-color: #00ff0090;
    }
  }
</style>

<body>

  <!-- Header -->

  <?=include('./menu.php') ?>

  <!-- Navbar Ends -->

  <form id="issueForm" class="issue__form">

    <div class="issue__head">
      <h1>Issue Form</h1>
      <select name="issue_category" id="issue_category" title="Should select a category">
        <option value="">Select Issue category</option>
        <option value="A">ALL</option>
        <option value="B">Breakfast</option>
        <option value="L">Lunch</option>
        <option value="S">Snacks</option>
        <option value="D">Dinner</option>
      </select>
      <input type="date" name="issue_date" id="issue_date"  value="<?=date('Y-m-d')?>" required >
    </div>

    <input type="hidden" name="submitted" value="true" />
	
	 <div id="grid-container" class="grid-container">
		  <div class="box box2">
				<h2>Checked Items</h2>
				<table id="checkedItemsTable" class="table table-striped">
				  <thead>
					<tr>
					  <th>S.No</th>
					  <th>Item Name</th>
					  <th>Issue Quantity</th>
					</tr>
				  </thead>
				  <tbody id="checkedItemsBody">
					<!-- Checked items will be appended here -->
				  </tbody>
				</table>
			</div>
			<div class="box box2">

				<table>

				  <thead>
					<tr>
					  <th>ID</th>
					  <th>Select</th>
					  <th>Item Name</th>
					  <th>Unit Prices</th>
					  <th>Available Stock</th>
					  <th>Issue Quantity</th>
					</tr>
				  </thead>

				  <tbody>

					<?php
					$sqlget = "SELECT c . * , p . *                  
							  FROM stock c, item p
							  WHERE c.itemid = p.id order by p.id" ;
					$sqldata = mysqli_query($dbconn, $sqlget) or die('error getting');
					?>

					<?php
					while ($row = mysqli_fetch_array($sqldata, MYSQLI_ASSOC)) {
					?>

					  <tr>
						<td><?php echo strtoupper($row['id']); ?> </td>
						<td><input type="checkbox" name="op[]" value="<?=$row['itemid']; ?>" style="width:17px; height:17px;"></td>
						<td style='text-align:left'><?=strtoupper($row['itemname']); ?> </td>
						<td><input type="hidden" name="unit_prices[]" value="<?=$row['unit_price']; ?>"  > <?=$row['unit_price']; ?></td> 
						<td><span class="available_quantity"><?=strtoupper($row['quantity']); ?></span> <?=strtoupper($row['qunit']); ?></td>
						<td><input type="text" class="issue_quantity" pattern="^-?\d*\.?\d+$" required placeholder="Enter quantity" disabled='disabled' name="txt[]" title="Only decimals or numbers are allowed." /></td>
					  </tr>

					<?php
					}?>
					</tbody>
				</table>
			</div>
	</div>

    <button type="submit" class="btn btn-primary final__btn" name="submit" title="Submit" aria-label="Submit">Submit</button>

  </form>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script type="text/javascript" src="http://code.jquery.com/jquery-1.8.2.js"></script>

  <script type="text/javascript">
    document.getElementById('issueForm').addEventListener('submit', function(e) {
      e.preventDefault(); // Prevent the form from submitting normally

      if($('#issue_category').val() ==''){
        Swal.fire({
          title: 'Please Select issue category(B/L/S/D)',
          icon: 'error',
        });
        return false;
      }

      Swal.fire({
        title: "Are you sure?",
        text: "Do you want to issue these items?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: "Yes, Save it!",
      }).then((result) => {
        if (result.isConfirmed) {
          const formData = new FormData(this);

          fetch('ajax_process_issue.php', {
            method: 'POST',
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (data.status === "success") {
              Swal.fire(
                'Saved!',
                data.message,
                'success'
              );
              // Optionally, reset the form or update the UI as needed
            } else {
              Swal.fire(
                'Error!',
                data.message,
                'error'
              );
            }
            location.reload();
          })
          .catch(error => {
            console.error('Error:', error);
            Swal.fire(
              'Error!',
              'An error occurred while processing the request.',
              'error'
            );
          });
        }
      });
    });
  </script>

  <script>
    const valueBox = Array.from(document.querySelectorAll("input[type='text']"))
    const checkBox = Array.from(document.querySelectorAll("input[type='checkbox']"));

    let checkCounter = 0;

    const submitBtn = document.querySelector('.final__btn');
    submitBtn.disabled = true;
    submitBtn.classList.add('cursor-not-allowed')

    checkBox.forEach((check, index) => {
      check.addEventListener('click', () => {
        if (!check.checked) {
          valueBox[index].value = '';
          valueBox[index].style.backgroundColor = ''; 
          checkCounter--;
        } else {
          checkCounter++;
          valueBox[index].style.backgroundColor = 'yellow';
          valueBox[index].disabled = false;
          valueBox[index].focus();
        }
        if (checkCounter > 0) {
          submitBtn.disabled = false;
        } else {
          submitBtn.disabled = true;
        }
      })
    })
  </script>

  <script>
    const allQuantities = Array.from(document.querySelectorAll(".available_quantity"))
    const issueQuantity = Array.from(document.querySelectorAll(".issue_quantity"))

    issueQuantity.forEach((input, index) => {
      input.addEventListener('change', () => {
        if (parseFloat(input.value) > parseFloat(allQuantities[index].textContent)) {
          alert('Invalid Quantity')
          input.value = '';
        }
      })
    })
  </script>

  <script>
    function checkConfirmation() {
      const issue_category = document.querySelector('#issue_category');
      if (!issue_category.value) {
        alert('Select issue category');
        return false;
      }

      let ret_val = window.confirm("Do u want to submit");
      return ret_val
    }
  </script>

  <script type="module" src="../icons/ionicons.esm.js"></script>
  <script type="module" src="../icons/ionicons.js"></script>
  <script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
