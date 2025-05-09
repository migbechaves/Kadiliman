<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to cancel a reservation']);
    exit;
}

// Include database connection
require_once '../includes/db_connect.php';

// Get the reservation ID
$reservationId = $_POST['reservation_id'] ?? 0;
$userId = $_SESSION['user_id'];

// Verify that this reservation belongs to the current user
$checkStmt = $conn->prepare("SELECT reservation_id FROM reservations WHERE reservation_id = ? AND user_id = ?");
$checkStmt->bind_param("ii", $reservationId, $userId);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Reservation not found or you do not have permission to cancel it']);
    $checkStmt->close();
    $conn->close();
    exit;
}

// Update the reservation status to canceled
$updateStmt = $conn->prepare("UPDATE reservations SET status = 'canceled' WHERE reservation_id = ?");
$updateStmt->bind_param("i", $reservationId);

if ($updateStmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $updateStmt->error]);
}

$checkStmt->close();
$updateStmt->close();
$conn->close();
?>