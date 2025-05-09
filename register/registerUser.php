<?php
require_once '../login_security.php';

// Handle login request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    header('Content-Type: application/json');
    session_start();

    // Validate login fields
    if (empty($_POST['signin-username']) || empty($_POST['signin-password'])) {
        echo json_encode(['success' => false, 'message' => 'Please enter both username and password']);
        exit;
    }

    $username = trim($_POST['signin-username']);
    $password = trim($_POST['signin-password']);

    // Database connection
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $dbname = "kadiliman";

    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT id, username, password, firstname, surname FROM users WHERE username = ?");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error. Please try again later.']);
        exit;
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    $authenticated = false;

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            // Successful login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['surname'] = $user['surname'];

            $returnTo = isset($_POST['returnTo']) ? $_POST['returnTo'] : 'Dashboard.php';

            echo json_encode([
                'success' => true,
                'redirectUrl' => $returnTo,
                'message' => 'Login successful'
            ]);

            $authenticated = true;
        }
    }

    if (!$authenticated) {
        // Track failed attempt
        trackFailedLoginAttempt($_POST['signin-username']);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid username or password',
            'showModal' => true
        ]);
        exit;
    } else {
        // Successful login - reset failed attempts counter
        resetFailedLoginAttempts($_POST['signin-username']);
    }

    $stmt->close();
    $conn->close();
    exit;
}

// Handle registration request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['action']) || $_POST['action'] !== 'login')) {
    header('Content-Type: application/json');

    // Validate required fields
    if (empty($_POST['signup-username']) || empty($_POST['signup-email']) || empty($_POST['signup-firstname']) || empty($_POST['signup-surname']) || empty($_POST['signup-branch']) || empty($_POST['signup-password'])) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "kadiliman";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]);
        exit;
    }

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO users (username, email, firstname, surname, branch, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $username, $email, $firstname, $surname, $branch, $hashedPassword);

    // Set parameters and execute
    $username = $_POST['signup-username'];
    $email = $_POST['signup-email'];
    $firstname = $_POST['signup-firstname'];
    $surname = $_POST['signup-surname'];
    $branch = $_POST['signup-branch'];
    $password = $_POST['signup-password'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $accountCreated = $stmt->execute();

    if ($accountCreated) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit;
}

// If the request method is not POST, return an error
header('Content-Type: application/json');
echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
exit;
?>