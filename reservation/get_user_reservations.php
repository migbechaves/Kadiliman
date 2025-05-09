<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to view reservations']);
    exit;
}

// Include database connection
require_once '../includes/db_connect.php';

$userId = $_SESSION['user_id'];

// Query to get user's reservations
$stmt = $conn->prepare("
    SELECT reservation_id, pc_number, pc_type, reservation_date, 
           start_time, duration_hours, end_time, status, price, created_at
    FROM reservations 
    WHERE user_id = ? 
    ORDER BY reservation_date DESC, start_time DESC
");

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$reservations = [];
while ($row = $result->fetch_assoc()) {
    // Format date and times for display
    $reservationDate = date('F j, Y', strtotime($row['reservation_date']));
    $startTime = date('h:i A', strtotime($row['start_time']));
    $endTime = date('h:i A', strtotime($row['end_time']));
    
    $reservations[] = [
        'id' => $row['reservation_id'],
        'pc_number' => $row['pc_number'],
        'pc_type' => ($row['pc_type'] === 'S' ? 'Standard' : 'Premium'), // Map 'S' to 'Standard' and 'P' to 'Premium'
        'date' => $reservationDate,
        'time' => "$startTime - $endTime",
        'duration' => $row['duration_hours'] . ' hour' . ($row['duration_hours'] > 1 ? 's' : ''),
        'status' => ucfirst($row['status']), // Will now show "Pending" for new reservations
        'price' => '₱' . $row['price'],
        'created_at' => date('M j, Y g:i A', strtotime($row['created_at']))
    ];
}

echo json_encode([
    'success' => true,
    'reservations' => $reservations
]);

$stmt->close();
$conn->close();
?>
