<!DOCTYPE html>
<?php
include("admindbconn.php");
?>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Issue Item</title>
  <link type="text/css" rel="stylesheet" href="../css/menu.css">  
  <link rel="stylesheet" href="../font-awesome-4.7.0/css/font-awesome.min.css">
  <link href="../bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link type="text/css" rel="stylesheet" href="../css/sc.css">
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
  <style>
    /* Add your CSS styling here */
  </style>
</head>
<body>
  <!-- Header -->
  <?= include('./menu.php') ?>

  <form id="issueForm" class="issue__form">
    <div class="issue__head">
      <h1>Issue Form</h1>
      <select name="issue_category" id="issue_category" required title="Should select a category">
        <option value="">Select Issue category</option>
        <option value="A">ALL</option>
        <option value="B">Breakfast</option>
        <option value="L">Lunch</option>
        <option value="S">Snacks</option>
        <option value="D">Dinner</option>
      </select>
      <input type="date" name="issue_date" id="issue_date" value="<?= date('Y-m-d') ?>" required>
    </div>

    <input type="hidden" name="submitted" value="true" />
    <div class="print_issue__head">
      Selected Issue Category: <span id="selectedIssueCategory"></span>
    </div>
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
              <th>S.No</th>
              <th>ITEM ID</th>
              <th>Select</th>
              <th>Item Name</th>
              <th>Unit Prices</th>
              <th>Available Stock</th>
              <th>Issue Quantity</th>
              <th>Balance</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $sqlget="SELECT c.*, p.id, p.item_category, p.itemname, p.disp_priority, 
            CASE p.item_category 
            WHEN 'milk' THEN 1 WHEN 'grocery' THEN 2 WHEN 'nonveg' THEN 3 WHEN 'veg' THEN 4 WHEN 'fruits' THEN 5 WHEN 'bakery' 
            THEN 6 WHEN 'one-use' THEN 7 ELSE 8 END AS item_category_value 
            FROM stock c JOIN item p ON c.itemid = p.id 
            WHERE c.quantity > 0.0 AND p.disp_priority > 0 ORDER BY item_category_value, p.disp_priority, p.itemname";
            $result = mysqli_query($dbconn, $sqlget);
            $count = 1;
            while ($row = mysqli_fetch_assoc($result)) {
              $itemid = $row['id'];
              $itemname = $row['itemname'];
              $available_quantity = $row['quantity'];
              $unit_price = $row['unit_price'];
              ?>
              <tr class="tr-<?=$row['item_category']?>">
                <td><?=$count?></td>
                <td><?=$itemid?></td>
                <td><input type="checkbox" name="op[]" value="<?=$itemid?>"></td>
                <td><?=$itemname?></td>
                <td>
                  <input type="text" class="unit_price" value="<?=$unit_price?>" readonly>
                </td>
                <td class="available_quantity"><?=$available_quantity?></td>
                <td>
                  <input type="text" class="issue_quantity" disabled>
                </td>
                <td class="balance_quantity"><?=$available_quantity?></td>
              </tr>
              <?php
              $count++;
            }
            ?>
          </tbody>
        </table>
        <button type="submit" class="final__btn btn btn-primary" disabled>Submit</button>
      </div>
    </div>
  </form>

  <script>
    $(document).ready(function() {
      const issueCategoryNames = {
        'A': 'ALL',
        'B': 'Breakfast',
        'L': 'Lunch',
        'S': 'Snacks',
        'D': 'Dinner'
      };

      // Update issue category display
      $('#issue_category').change(function() {
        const selectedCategoryCode = $(this).val();
        $('#selectedIssueCategory').text(issueCategoryNames[selectedCategoryCode]);
      });

      const checkedItemsBody = $('#checkedItemsBody');
      const submitBtn = $('.final__btn');
      let checkCounter = 0;

      // Handle checkbox change events
      $('input[name="op[]"]').change(function() {
        const $row = $(this).closest('tr');
        const itemId = $(this).val();
        const itemName = $row.find('td:nth-child(4)').text();
        const $issueQuantityInput = $row.find('.issue_quantity');
        const issueQuantity = $issueQuantityInput.val() || 0;

        if ($(this).is(':checked')) {
          // Add the item to the checked items table
          if (!$(`#checked_item_${itemId}`).length) {
            checkedItemsBody.append(`
              <tr id="checked_item_${itemId}">
                <td>${checkCounter + 1}</td>
                <td>${itemName}</td>
                <td>${issueQuantity}</td>
              </tr>
            `);
            checkCounter++;
          }
        } else {
          // Remove the item from the checked items table
          const $checkedRow = $(`#checked_item_${itemId}`);
          if ($checkedRow.length) {
            $checkedRow.remove();
            checkCounter--;
            // Reorder the rows after removal
            $('#checkedItemsBody tr').each(function(index) {
              $(this).find('td:first-child').text(index + 1);
            });
          }
        }

        // Update the submit button state
        submitBtn.prop('disabled', checkCounter === 0);
      });

      // Handle input changes for issue quantity
      $('.issue_quantity').on('input', function() {
        const $tr = $(this).closest('tr');
        const availableQuantity = parseFloat($tr.find('.available_quantity').text());
        const issueQuantity = parseFloat($(this).val());
        const $balanceQuantityElement = $tr.find('.balance_quantity');
        const balanceQuantity = availableQuantity - issueQuantity;

        if (balanceQuantity < 0.0) {
          alert('Invalid issue quantity');
          $(this).val('');
          $balanceQuantityElement.text(availableQuantity);
        } else {
          $balanceQuantityElement.text(isNaN(balanceQuantity) ? availableQuantity : balanceQuantity.toFixed(2));
        }

        // Update the checked items table with new quantities
        const itemId = $tr.find('input[name="op[]"]').val();
        const $checkedRow = $(`#checked_item_${itemId}`);
        if ($checkedRow.length) {
          $checkedRow.find('td:nth-child(3)').text($(this).val() || 0);
        }
      });

      // Handle form submission
      $('#issueForm').on('submit', function(e) {
        e.preventDefault();

        const mealNames = {
          'B': 'Breakfast',
          'D': 'Dinner',
          'L': 'Lunch',
          'S': 'Snacks',
          'A': 'Common'
        };

        let msg_data = `<br>Issue Category: <b>${mealNames[$('#issue_category').val()]}</b>`;
        msg_data += `<br>Issue Date: <b>${$('#issue_date').val()}</b>`;

        Swal.fire({
          title: "Do you want to issue these items?",
          html: msg_data,
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "Yes, Save it!",
        }).then((result) => {
          if (result.isConfirmed) {
            const formData = new FormData();

            $('input[name="op[]"]:checked').each(function() {
              const itemId = $(this).val();
              const issueQuantity = $(`#checked_item_${itemId} td:nth-child(3)`).text();
              const unitPrice = $(`#${itemId}_unit_price`).val();

              formData.append('op[]', itemId);
              formData.append('txt[]', issueQuantity);
              formData.append('unit_prices[]', unitPrice);
            });

            formData.append('issue_category', $('#issue_category').val());
            formData.append('issue_date', $('#issue_date').val());

            fetch('ajax_process_issue.php', {
              method: 'POST',
              body: formData
            })
            .then(response => response.json())
            .then(data => {
              if (data.status === "success") {
                Swal.fire({
                  title: 'Saved!',
                  text: data.message,
                  icon: 'success',
                  timer: 2000,
                  timerProgressBar: true
                });
                $('#issueForm').trigger('reset');
                location.reload();
              } else {
                Swal.fire('Error!', data.message, 'error');
              }
            })
            .catch(error => {
              console.error('Error:', error);
              $('#issueForm').trigger('reset');
              Swal.fire('Error!', 'An error occurred while processing the request.', 'error');
            });
          }
        });
      });
    });
  </script>
</body>
</html>

