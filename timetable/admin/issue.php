<?php
session_start();
include("admindbconn.php");
if ( $_SESSION["role"] != 'ADMIN'   || !isset($_SESSION['user_name']) ) {
  echo "Invalid User : ".$_SESSION["role"] ;
  exit(0);
}

?>
<html>
<head>
  <title>Issue Item</title>
  <link type="text/css" rel="stylesheet" href="../css/menu.css">  
  <link rel="stylesheet" href="../font-awesome-4.7.0/css/font-awesome.min.css">
  <!-- 
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  -->
  
  <!-- 
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  -->
  <link href="../bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  
  <link type="text/css" rel="stylesheet" href="../css/sc.css">
  
  <!-- enable and disable textbox -->
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
  .print_issue__head {
  background-color: #f0f0f0;
  padding: 10px;
  margin-bottom: 20px;
  border-radius: 5px;
  text-align: center;
}

.print_issue__head span {
  font-size: 1.2rem;
  font-weight: bold;
  color: #333;
  text-transform: uppercase;
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
	position: sticky;
    top: 0;
    background-color: #8AAAE5; /* Or any background color to make it stand out */
    z-index: 10; /* Ensures the header is above other content */
    border-bottom: 2px solid #ddd; /* Optional: Adds a bottom border to the header */

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

  /* Form */

  @media (hover: hover) and (pointer: fine) {

    /* .issue__form > button:is(:hover, :focus-visible) {
      color: #232323;
      background-color: #FFFFFF;
    } */

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
  
  /* ok */
  /* CSS styles */
.tr-grocery {
    background-color: black;
    color: white; /* To ensure text is visible on black background */
}

.tr-milk {
    background-color: olive;
    color: black; /* To ensure text is visible on white background */
}

/* Add more styles for other categories as needed */
.tr-nonveg {
    background-color: brown; /* Example color */
    color: black;
}

.tr-veg {
    background-color: DarkGreen; /* Example color */
    color: black;
}

.tr-fruits {
    background-color: OrangeRed; /* Example color */
    color: black;
}

.tr-bakery {
    background-color: SteelBlue; /* Example color */
    color: black;
}

.tr-single-use {
    background-color: SlateBlue; /* Example color */
    color: black;
}
.tr-gas {
    background-color: Chocolate; /* Example color */
    color: black;
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


</style>

<body>

  <!-- Header -->

  <?=include('./menu.php') ?>

  <!-- Navbar Ends -->

  <!--<form action="#" method="post" onsubmit="return checkConfirmation()" class="issue__form">-->
  <form id="issueForm" class="issue__form">

    <div class="issue__head">
      <h1>Issue Form</h1>
      <select name="issue_category" id="issue_category"  required title="Should select a category" >
        <option value="">Select Issue category</option>
		<option value="A">ALL</option>
        <option value="B">Breakfast</option>
        <option value="L">Lunch</option>
        <option value="S">Snacks</option>
        <option value="D">Dinner</option>
		 <!--<option value="HKMS">HouseKeeping MESS/option>
		 <option value="HKBA">HouseKeeping Boys-A</option>
		 <option value="HKBB">HouseKeeping Boys-B</option>
		 <option value="HKBC">HouseKeeping Boys-C</option>
		 <option value="HKBD">HouseKeeping Boys-D</option>
		 <option value="HKBE">HouseKeeping Boys-E</option>
		  <option value="HKGA">HouseKeeping Girls-A</option>
		 <option value="HKGB">HouseKeeping Girls-B</option>
		 <option value="HKGC">HouseKeeping Girls-C</option>
		 <option value="HKGD">HouseKeeping Girls-D</option>
		 <option value="HKGE">HouseKeeping Girls-E</option>-->
		 
      </select>
      <input type="date" name="issue_date" id="issue_date"  value="<?=date('Y-m-d')?>" required >
    </div>

    <input type="hidden" name="submitted" value="true" />
	<div class="print_issue__head">
		Selected Issue Category: <span id="selectedIssueCategory"></span>
	</div>
	 
			<table>

			  <thead>
				<tr>
				  <th>S.No</th>
				  <th>ITEM ID</th>
				  <th>Select</th>
				  <th>Item Name</th>
				   <th>Unit Prices</th>
				  <th>Available Stock</th>
				  <th>Issue Quantity</th>
				  <th>Balance </th>
				</tr>
			  </thead>

			  <tbody>

				<?php

				/*$sqlget = "SELECT c . * , p . *                  
						  FROM stock c, item p
						  WHERE c.itemid = p.id and c.quantity > 0.0and p.disp_priority > 0 order by p.disp_priority" ;*/
				$sqlget="SELECT c.*, p.id, p.item_category, p.itemname, p.disp_priority,p.measurement_unit,p.brand_name, 
				CASE p.item_category 
				WHEN 'milk' THEN 1 WHEN 'grocery' THEN 2 WHEN 'nonveg' THEN 3 WHEN 'veg' THEN 4 WHEN 'fruits' THEN 5 WHEN 'bakery' 
				THEN 6 WHEN 'one-use' THEN 7  ELSE 8 END AS item_category_value 
				FROM stock c JOIN item p ON c.itemid = p.id 
				WHERE c.quantity > 0.0 AND p.disp_priority > 0 ORDER BY item_category_value, p.disp_priority, p.id;";
					$sqldata = mysqli_query($dbconn, $sqlget) or die('error getting');
				?>

				<?php
				$sno=0;
				while ($row = mysqli_fetch_array($sqldata, MYSQLI_ASSOC)) {
					$class = 'tr-' . strtolower($row['item_category']);
				?>          
				  <tr class="<?=$class?>">
					<td><?=++$sno?> </td>
					<td><?php echo strtoupper($row['id']); ?> </td>
					<td><input type="checkbox" name="op[]"  id="item_<?=$row['itemid']; ?>" value="<?=$row['itemid']; ?>" style="width:17px; height:17px;"></td>
					<td style='text-align:left'><?=strtoupper($row['itemname']."(".$row['brand_name'].")"); ?> </td>			
					<td><input type="hidden" name="unit_prices[]" id="up_<?=$row['itemid']; ?>" value="<?=$row['unit_price']; ?>"  > <?=$row['unit_price']; ?></td> 
				
					<td > <span class="available_quantity" name='aq[]'><?=strtoupper($row['quantity']); ?></span> <?=strtoupper($row['measurement_unit']); ?></td>
					<td><input type="text"  id="iq<?=$row['itemid']; ?>" class="issue_quantity" pattern="^-?\d*\.?\d+$" required placeholder="quantity" disabled='disabled' name="txt[]"  size=10 title="Only decimals or numbers are allowed." /></td>
					 <td > <span name="bq[]" class="balance_quantity"><?=strtoupper($row['quantity']); ?></span> </td>
				  </tr>
				<?php
				}?>

			  </tbody>
			  
			</table>
		

    <button type="submit" class="btn btn-primary final__btn" name="submit" title="Submit" aria-label="Submit">Submit</button>

  </form>
</body>
</html>

 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 <script type="text/javascript" src="http://code.jquery.com/jquery-1.8.2.js"></script>
<script>


document.addEventListener('DOMContentLoaded', function() {
	
  const issueCategoryNames = {
    'A': 'ALL',
    'B': 'Breakfast',
    'L': 'Lunch',
    'S': 'Snacks',
    'D': 'Dinner'
  };
	
  document.getElementById('issue_category').addEventListener('change', function() {
    
	const selectedCategoryCode  = this.value;
		
	document.getElementById('selectedIssueCategory').textContent = issueCategoryNames[selectedCategoryCode];
	

  });
  
   	
	
   
  document.getElementById('issueForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent the form from submitting normally
	const mealNames = {
  'B': 'Breakfast',
  'D': 'Dinner',
  'L': 'Lunch',
  'S': 'Snacks',
  'A': 'Common'
};
    let msg_data='<br>Issue Category: <b>'+ mealNames[document.getElementById('issue_category').value]+'</b>';
	msg_data += '<br>Issue Date: <b> '+document.getElementById('issue_date').value + '</b>'

    Swal.fire({
      title: "Do you want to issue these items ?",
      html: msg_data,
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: "Yes, Save it!",
    }).then((result) => {
      if (result.isConfirmed) {
        const formData = new FormData(); // Create a new FormData object

        // Append necessary fields from checked rows
        const checkedRows = document.querySelectorAll('input[name="op[]"]:checked');
        checkedRows.forEach(row => {
          const itemId = row.value;
          const issueQuantity = document.getElementById(`iq${itemId}`).value; // Get issue quantity
          const unitPrice = document.getElementById(`up_${itemId}`).value; // Get unit price

          formData.append('op[]', itemId);
          formData.append('txt[]', issueQuantity);
          formData.append('unit_prices[]', unitPrice);
        });

        // Append other necessary form data
        formData.append('issue_category', document.getElementById('issue_category').value);
        formData.append('issue_date', document.getElementById('issue_date').value);

        fetch('ajax_process_issue.php', {
            method: 'POST',
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (data.status === "success") {
              //Swal.fire('Saved!', data.message, 'success');
			  Swal.fire({
				  title: 'Saved!',
				  text: data.message,
				  icon: 'success',
				  timer: 2000, // Time in milliseconds (2000ms = 2 seconds)
				  timerProgressBar: true, // Optional: Show a progress bar
				  willClose: () => {
					// Optional: Perform any actions when the alert is closed
				  }
				});
			  document.getElementById('issueForm').reset();
              location.reload(); // Reload the page after successful submission
            } else {
              Swal.fire('Error!', data.message, 'error');
            }
          })
          .catch(error => {
            console.error('Error:', error);
			document.getElementById('issueForm').reset();
            Swal.fire('Error!', 'An error occurred while processing the request.', 'error');
          });
      }
    });
  });
  
  
			
});
</script>


 <script>

    const valueBox = Array.from(document.querySelectorAll("input[type='text']"))
    const checkBox = Array.from(document.querySelectorAll("input[type='checkbox']"));
    
    let checkCounter = 0;

    const submitBtn =document.querySelector('.final__btn');
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
		  // window.alert("ok");
		 //valueBox[index].style.background-color=yellow;
		 valueBox[index].style.backgroundColor = 'yellow';
		// bq[index].value=aq[index].value - valueBox[index].value;
		 
		 valueBox[index].disabled=false;
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
	document.querySelectorAll('.issue_quantity').forEach(input => {
    input.addEventListener('input', function() {
      const tr = this.closest('tr');
      const availableQuantity = parseFloat(tr.querySelector('.available_quantity').textContent);
      const issueQuantity = parseFloat(this.value);
      const balanceQuantityElement = tr.querySelector('.balance_quantity');
      const balanceQuantity = availableQuantity - issueQuantity;
	  if(balanceQuantity < 0.0)
	  {
		  alert('invalid issue quantity');
	  }
      
      if (!isNaN(balanceQuantity)) {
        balanceQuantityElement.textContent = balanceQuantity.toFixed(2);
      } else {
        balanceQuantityElement.textContent = availableQuantity;
      }
    });
  });
  </script>

  <script>

    function checkConfirmation() {

      const issue_category = document.querySelector('#issue_category');
      if (!issue_category.value) {
        alert('Selct issue category');
        return false;
      }

      let ret_val = window.confirm("Do u want to submit");
     
      return ret_val

    }

  </script>

<!--
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
-->
  <script type="module" src="../icons/ionicons.esm.js"></script>
  <script type="module" src="../icons/ionicons.js"></script>
  <script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>  



