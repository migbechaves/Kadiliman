<?php
session_start();

// Check if user is logged in
$response = array(
    'logged_in' => false,
    'user_id' => 0,
    'username' => ''
);

if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    $response['logged_in'] = true;
    $response['user_id'] = $_SESSION['user_id'];
    $response['username'] = $_SESSION['username'];
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
