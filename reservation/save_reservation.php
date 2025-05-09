<?php
session_start();
header('Content-Type: application/json');
require_once('../includes/db_connection.php');

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

// Get form data
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$pc_number = $_POST['pc_number'] ?? 0;
$pc_type = $_POST['pc_type'] ?? '';
$date = $_POST['date'] ?? '';
$start_time = $_POST['start_time'] ?? '';
$duration = $_POST['duration'] ?? 0;
$price_str = $_POST['price'] ?? '₱0';

// Format PC type to S or P as per database enum
$pc_type = strtolower($pc_type) === 'premium' ? 'P' : 'S';

// Calculate end time
$end_time_obj = new DateTime($start_time);
$end_time_obj->add(new DateInterval("PT{$duration}H"));
$end_time = $end_time_obj->format('H:i');

// Convert price string (₱XX.XX) to decimal
$price = floatval(str_replace('₱', '', $price_str));

// Validation
if (empty($pc_number) || empty($date) || empty($start_time) || empty($duration)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

// Check if PC is available
$availability_check = $conn->prepare("
    SELECT COUNT(*) as count
    FROM reservations 
    WHERE pc_number = ? 
    AND reservation_date = ? 
    AND ((start_time <= ? AND end_time > ?) 
        OR (start_time < ? AND end_time >= ?)
        OR (start_time >= ? AND start_time < ?))
    AND status != 'canceled'
");

$availability_check->bind_param("isssssss", $pc_number, $date, $end_time, $start_time, $end_time, $start_time, $start_time, $end_time);
$availability_check->execute();
$result = $availability_check->get_result();
$row = $result->fetch_assoc();

if ($row['count'] > 0) {
    echo json_encode(['success' => false, 'message' => 'PC is already booked for this time slot']);
    exit();
}

// Insert reservation
$stmt = $conn->prepare("
    INSERT INTO reservations 
    (user_id, username, pc_number, pc_type, reservation_date, start_time, duration_hours, end_time, status, price) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?)
");

$stmt->bind_param("isisssisi", $user_id, $username, $pc_number, $pc_type, $date, $start_time, $duration, $end_time, $price);

if ($stmt->execute()) {
    $reservation_id = $stmt->insert_id;
    echo json_encode([
        'success' => true, 
        'message' => 'Reservation submitted successfully and is pending approval',
        'reservation_id' => $reservation_id,
        'pc_type' => $pc_type
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
