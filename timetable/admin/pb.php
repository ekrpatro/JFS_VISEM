<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Item Quantity Report</title>
    <link rel="stylesheet" href="pq_style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="pq_script.js"></script>
</head>
<body>
    <h1>Purchase Item Quantity Report</h1>
    <form id="dateRangeForm">
        <label for="startdate">Start Date:</label>
        <input type="date" id="startdate" name="startdate" required>
        
        <label for="enddate">End Date:</label>
        <input type="date" id="enddate" name="enddate" required>
        
        <button type="submit">Get Report</button>
    </form>
    <div id="report">
        <!-- Report will be displayed here -->
    </div>
</body>
</html>
