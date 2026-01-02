<?php
include("admindbconn.php");

// Ensure error reporting is enabled (for development purposes)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action == 'add_dining') {
    $dining_date = $dbconn->real_escape_string($_POST['dining_date']);
    $boys = intval($_POST['boys']);
    $girls = intval($_POST['girls']);
    $parents = intval($_POST['parents']);
    $mess_staff = intval($_POST['mess_staff']);
    $sports = intval($_POST['sports']);
    $events = intval($_POST['events']);
    $medical_staff = intval($_POST['medical_staff']);

    $sql = "INSERT INTO `dining_count_status` 
            (`dining_date`, `boys`, `girls`, `medical_staff`, 
             `mess_staff`, `sports`, `events`, `parents`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            `boys` = VALUES(`boys`), 
            `girls` = VALUES(`girls`), 
            `medical_staff` = VALUES(`medical_staff`), 
            `mess_staff` = VALUES(`mess_staff`), 
            `sports` = VALUES(`sports`), 
            `events` = VALUES(`events`), 
            `parents` = VALUES(`parents`)";

    if ($stmt = $dbconn->prepare($sql)) {
        $stmt->bind_param('siiiiiii', $dining_date, $boys, $girls, $medical_staff, $mess_staff, $sports, $events, $parents);
        if ($stmt->execute()) {
            echo json_encode(["status" => 'success', "message" => "Count inserted/updated successfully"]);
        } else {
            echo json_encode(["status" => "fail", "message" => "Error: " . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "fail", "message" => "Error preparing statement: " . $dbconn->error]);
    }
}
 else if ($action == 'fetch_all') {
	$start_date=$_POST['start_date'];
	$end_date=$_POST['end_date'];
	
    $sql = "SELECT `id`, `dining_date`, `boys`, `girls`, `medical_staff`, `mess_staff`, `sports`, `events`, `parents`,
    (boys + girls + medical_staff + mess_staff + sports + events + parents) AS all_total
FROM dining_count_status
WHERE dining_date BETWEEN '$start_date' AND '$end_date'
ORDER BY dining_date DESC"; // Ensure this matches your actual table name
    $result = $dbconn->query($sql);

    $records = array();

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $records[] = $row;
        }
        echo json_encode($records);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to fetch records']);
    }
}
else if ($action == 'fetch_latest') {
    // Fetch the latest dining count
    $sql = "SELECT `id`, `dining_date`, `boys`, `girls`, `medical_staff`, `mess_staff`, `sports`, `events`, `parents`,
    (boys + girls + medical_staff + mess_staff + sports + events + parents) AS all_total
FROM dining_count_status
WHERE dining_date = (
    SELECT MAX(dining_date) FROM dining_count_status
)
ORDER BY dining_date DESC";
    $result = $dbconn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        echo json_encode($row);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No records found.']);
    }
    exit;
}
 else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}

$dbconn->close();
?>