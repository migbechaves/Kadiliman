<?php
session_start();
header('Content-Type: application/json');

// Connect to the database
require_once('../includes/db_connection.php');

// Get form data
$date = $_POST['date'] ?? date('Y-m-d');
$start_time = $_POST['start_time'] ?? '10:00';
$duration = $_POST['duration'] ?? 3;

// Calculate end time
$end_time_obj = new DateTime($start_time);
$end_time_obj->add(new DateInterval("PT{$duration}H"));
$end_time = $end_time_obj->format('H:i');

// Query to find booked PCs for the given time slot
$stmt = $conn->prepare("
    SELECT pc_number, pc_type 
    FROM reservations 
    WHERE reservation_date = ? 
    AND ((start_time <= ? AND end_time > ?) 
        OR (start_time < ? AND end_time >= ?)
        OR (start_time >= ? AND start_time < ?))
    AND status != 'canceled'
");

$stmt->bind_param("sssssss", $date, $end_time, $start_time, $end_time, $start_time, $start_time, $end_time);
$stmt->execute();
$result = $stmt->get_result();

$booked_pcs = [];
while($row = $result->fetch_assoc()) {
    $booked_pcs[] = [
        'pc_number' => $row['pc_number'],
        'pc_type' => $row['pc_type']
    ];
}

// For this example, let's assume PCs 1, 3, and 10 are under maintenance
$maintenance_pcs = [1, 3, 10];

// Send response
$response = [
    'success' => true,
    'booked_pcs' => $booked_pcs,
    'maintenance_pcs' => $maintenance_pcs,
    'date' => $date,
    'start_time' => $start_time,
    'end_time' => $end_time
];

header('Content-Type: application/json');
echo json_encode($response);

$stmt->close();
$conn->close();
?>
