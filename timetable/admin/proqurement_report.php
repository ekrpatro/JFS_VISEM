<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase  Quantity Report</title>
    
	<link type="text/css" rel="stylesheet" href="../css/menu.css">
	
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
	$(document).ready(function() {
    $('#dateRangeForm').submit(function(event) {
        event.preventDefault();
        let startdate = $('#startdate').val();
        let enddate = $('#enddate').val();
        $.ajax({
            url: 'generate_proqurement_report.php',
            type: 'POST',
            data: { startdate: startdate, enddate: enddate },
            success: function(response) {
                $('#report').html(response);
            },
            error: function() {
                $('#report').html('<p>An error occurred while fetching the report.</p>');
            }
        });
    });
	 $('#prnt_btn').click(function() {
               
                var reportContent = $('#report').html();

                // Create a new window for the print preview
                var printWindow = window.open('', '', 'height=600,width=800');

                // Write the HTML for the print window
                printWindow.document.write('<html><head><title>Procurement Report</title>');
                printWindow.document.write('</head><body>');
                printWindow.document.write(reportContent);
                printWindow.document.write('</body></html>');
                // Close the document to trigger rendering
                printWindow.document.close();
                // Print the content
                printWindow.print();
            });
	
	
});

</script>
<style>	
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}
#prnt_btn
{	color: #fff;
    padding: 10px;
	margin-left: 800px;
    background-color: #4CAF00;
}
/* Header styling */
h1 {
    text-align: center;
    color: #333;
    padding: 20px 0;
    background-color: #4CAF50;
    color: white;
    margin: 0;
}

/* Form styling */
#dateRangeForm {
    max-width: 600px;
    margin: 20px auto;
    padding: 20px;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

#dateRangeForm label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
}

#dateRangeForm input[type="date"] {
    width: calc(100% - 22px);
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-bottom: 15px;
    font-size: 16px;
}

#dateRangeForm button {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
}

#dateRangeForm button:hover {
    background-color: #45a049;
}

/* Report section styling */
#report {
  
    margin: 20px auto;
    padding: 20px;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

/* Table styling */
table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}

table, th, td {
    border: 1px solid #ddd;	
}

th, td {
    padding: 12px;
    text-align: left;
}

th {
    background-color: #4CAF50;
    color: white;
	text-align:center;
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

tr:nth-child(even) {
    background-color: #f2f2f2;
}

tr:hover {
    background-color: #ddd;
}

</style>
</head>
<body>
	<?= include('./menu.php') ?>
    <h1>Purchase Item Quantity Report</h1>
    <form id="dateRangeForm">
        <label for="startdate">Start Date:</label>
        <input type="date" id="startdate" name="startdate" class='form-control' value='<?=date("Y-m-d")?>' required>
        
        <label for="enddate">End Date:</label>
        <input type="date" id="enddate" name="enddate" class='form-control' value='<?=date("Y-m-d")?>' required>
        
        <button type="submit">Get Report</button> 
    </form>
	<div>
		<button type="button" id='prnt_btn'>Print</button> 
	</div>
    <div id="report">	
        
    </div>
</body>

</html>
