<?php
// generate_wastage_report.php
include("admindbconn.php");

$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$item_id=$_POST['item_id'];
//$item_id = 206; // Example item_id

// Fetch data from issueitem table
$item_cond = ($item_id == 0) ? "" : ' AND k.id=' . $item_id;

 $sql = "SELECT i.itemid, i.disp_priority,i.itemname, d.issue_date, COALESCE(SUM(i.quantity), 0) AS quantity 
	FROM ( SELECT DISTINCT issue_date FROM issueitem WHERE issue_date BETWEEN '$start_date' AND '$end_date' ) AS d 
	LEFT JOIN ( SELECT j.itemid, itemname,disp_priority, issue_date, SUM(quantity) AS quantity FROM issueitem j 
	inner join item k on k.id=j.itemid WHERE issue_date BETWEEN '$start_date' AND '$end_date' ". $item_cond. "
	GROUP BY itemid, issue_date ) AS i ON d.issue_date = i.issue_date 
	GROUP BY d.issue_date, i.itemid ORDER BY i.disp_priority,i.itemid, d.issue_date;";

$result = mysqli_query($dbconn, $sql);

if (!$result) {
    echo "Error: " . mysqli_error($dbconn);
} else {
    // Collect all unique issue_dates and itemids
    $issue_dates = [];
    $itemids = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $issue_dates[$row['issue_date']] = 1;
        $itemids[$row['itemid']] = 1;
    }

    // Sort the issue_dates and itemids
    ksort($issue_dates);
    ksort($itemids);
	$no_cols=sizeof($itemids)+1;//1 for s.no
    // Create a table header with issue_dates as columns
    $table = "<table border='1' style='border-collapse:collapse;' align='center' cellpadding='10'>
				<thead>
				<tr><th colspan='".$no_cols."'>ISSUE REGISTER</th></tr>
                <tr>
                    <th>S.No</th>
					<th>Item ID</th>
                    <th>Item Name</th>";

					foreach ($issue_dates as $issue_date => $value) {
						$table .= "<th>$issue_date</th>";
					}

				$table .= "<th> Total </th></tr> </thead><tbody>";

    // Fetch data again to populate the table body
    mysqli_data_seek($result, 0); // Reset result pointer

    // Initialize variables to track current itemid and table body
    $current_itemid = null;
    $table_body = "";
	$sno=0;
    while ($row = mysqli_fetch_assoc($result)) {
        // Check if itemid has changed to start a new row
		$row_tot=0;
        if ($row['itemid'] != $current_itemid) {
            // If it's not the first row, close the previous row
            if ($current_itemid !== null) {
                // Fill in missing issue_dates with 0s for the previous itemid
				
                foreach ($issue_dates as $issue_date => $value) {
                    if (!isset($current_row[$issue_date]) ||  $current_row[$issue_date] == 0) {
                        $table_body .= "<td>-</td>";
                    } else {
                        $table_body .= "<td>{$current_row[$issue_date]}</td>";
						$row_tot += $current_row[$issue_date];
						
                    }
                }
                $table_body .= "<td style='font-weight:bold;text-align:right'>$row_tot</td></tr>";
            }

            // Start new row for new itemid
            $table_body .= "<tr><td>".++$sno."</td><td>{$row['itemid']}</td><td style='text-align:left''>{$row['itemname']}</td>";
            $current_itemid = $row['itemid'];
            // Initialize current row with issue_dates and quantities
            $current_row = [];
            foreach ($issue_dates as $issue_date => $value) {
                $current_row[$issue_date] = 0; // Initialize all quantities to 0
            }
        }

        // Store the quantity for the current issue_date in the current row
        $current_row[$row['issue_date']] = $row['quantity'];
    }

    // After looping through all results, add the last row to the table body
    if (!empty($current_row)) {
		$last_row_tot=0;
        foreach ($issue_dates as $issue_date => $value) {
            if (!isset($current_row[$issue_date]) || $current_row[$issue_date] == 0) {
                $table_body .= "<td>-</td>";
            } else {
                $table_body .= "<td>{$current_row[$issue_date]}</td>";
				$last_row_tot += $current_row[$issue_date];
				
				
            }
        }
        $table_body .= "<td style='font-weight:bold;text-align:right'>$last_row_tot</td></tr>";
    }

    // Combine header and body to complete the table
    $table .= $table_body . "</tbody></table>";

    echo $table;
}

// Close database connection
mysqli_close($dbconn);
?>
