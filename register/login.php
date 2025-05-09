<?php
// This would be your login.php file that processes the login form

session_start();
header('Content-Type: application/json');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fix the path to login_alert_handler.php
require_once __DIR__ . '/../login_alert_handler.php';
require 'C:\xampp\htdocs\KADILIMAN\phpmailer\PHPMailer-master\src\Exception.php';
require 'C:\xampp\htdocs\KADILIMAN\phpmailer\PHPMailer-master\src\PHPMailer.php';
require 'C:\xampp\htdocs\KADILIMAN\phpmailer\PHPMailer-master\src\SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Function to send login alert email
function sendLoginAlertEmail($username, $email) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'dado.aldrin2133@gmail.com';
        $mail->Password = 'nxpu moix tawy ieoy';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('dado.aldrin2133@gmail.com', 'Kadiliman Security');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Security Alert: Failed Login Attempt';
        $mail->Body = "
            <h2>Security Alert</h2>
            <p>Dear $username,</p>
            <p>We detected a failed login attempt on your account. If this was you, please make sure you're using the correct password.</p>
            <p>If this wasn't you, please secure your account immediately by changing your password.</p>
            <p>Time of attempt: " . date('Y-m-d H:i:s') . "</p>
            <p>Best regards,<br>Kadiliman Security Team</p>";

        $mail->send();
        error_log("Login alert email sent to: " . $email);
    } catch (Exception $e) {
        error_log("Failed to send login alert email: " . $mail->ErrorInfo);
    }
}

// Function to track login attempts
function trackLoginAttempt($username, $email) {
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = [];
    }
    
    if (!isset($_SESSION['login_attempts'][$username])) {
        $_SESSION['login_attempts'][$username] = 0;
    }
    
    $_SESSION['login_attempts'][$username]++;
    
    // Send login alert email if failed attempts reach threshold
    if ($_SESSION['login_attempts'][$username] >= 3) {
        sendLoginAlertEmail($username, $email);
    }
}

// Function to get failed attempts count
function getFailedAttempts($username) {
    return isset($_SESSION['login_attempts'][$username]) ? $_SESSION['login_attempts'][$username] : 0;
}

// Function to reset failed attempts
function resetFailedAttempts($username) {
    if (isset($_SESSION['login_attempts'][$username])) {
        $_SESSION['login_attempts'][$username] = 0;
    }
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Basic validation
        if (empty($_POST['signin-username']) || empty($_POST['signin-password'])) {
            throw new Exception("Please enter both username and password");
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
            throw new Exception("Database connection failed: " . $conn->connect_error);
        }

        // First check if the username exists to get user info regardless of password validity
        $check_stmt = $conn->prepare("SELECT id, username, email, login_alerts FROM users WHERE username = ?");
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        $user_exists = $check_result->num_rows === 1;
        $user_info = null;
        
        if ($user_exists) {
            $user_info = $check_result->fetch_assoc();
            $user_email = $user_info['email'];
            $alerts_enabled = (bool)$user_info['login_alerts'];
        }
        $check_stmt->close();

        // Now check password if user exists
        $stmt = $conn->prepare("SELECT id, username, password, firstname, surname, email, login_alerts FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                // Successful login
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['firstname'] = $user['firstname'];
                $_SESSION['surname'] = $user['surname'];
                $_SESSION['is_admin'] = false;

                // Reset failed login counter
                if (isset($_SESSION['login_attempts'][$username])) {
                    $_SESSION['login_attempts'][$username] = [];
                }

                $returnTo = isset($_POST['returnTo']) ? $_POST['returnTo'] : 'Dashboard.php';

                echo json_encode([
                    'success' => true,
                    'redirectUrl' => $returnTo,
                    'message' => 'Login successful'
                ]);
            } else {
                // Failed login - track attempt with email
                if ($user_exists && $alerts_enabled) {
                    trackLoginAttempt($username, $user_email);
                }
                
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid username or password',
                    'showModal' => true
                ]);
            }
        } else {
            // User not found - don't track since no email to send to
            echo json_encode([
                'success' => false,
                'message' => 'Invalid username or password',
                'showModal' => true
            ]);
        }

        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        // Log the error
        error_log("Login error: " . $e->getMessage());

        // Return error message
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'showModal' => true
        ]);
    }
    exit;
}

// If we get here, it's an invalid request
echo json_encode([
    'success' => false,
    'message' => 'Invalid request',
    'showModal' => true
]);
exit;
?>