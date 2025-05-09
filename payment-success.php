<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: Registration.php");
    exit();
}

// Check if there's a transaction ID in the URL
$transactionId = isset($_GET['transaction_id']) ? $_GET['transaction_id'] : '';

// Check if there's a pending transaction in the session
if (!isset($_SESSION['pending_transaction']) || $_SESSION['pending_transaction']['transaction_id'] != $transactionId) {
    // No valid transaction found
    header("Location: topup.php");
    exit();
}

// Get transaction details from session
$transaction = $_SESSION['pending_transaction'];
$pcType = $transaction['pc_type'];
$hours = $transaction['hours'];
$amount = $transaction['amount'];
$bonusMinutes = $transaction['bonus_minutes'];

// Calculate total minutes (hours to minutes + any bonus)
$totalMinutes = ($hours * 60) + $bonusMinutes;

// Database connection
$servername = "localhost";
$username = "root"; // Change this to your DB username
$password = ""; // Change this to your DB password
$dbname = "kadiliman"; // Change this to your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update the user's balance in the database
$sql = "UPDATE user_balance SET " . ($pcType === 'standard' ? "standard_balance" : "premium_balance") . " = " . 
       ($pcType === 'standard' ? "standard_balance" : "premium_balance") . " + ? WHERE username = ?";
$stmt = $conn->prepare($sql);
$totalHours = $totalMinutes / 60; // Convert minutes to hours
$username = $_SESSION['username']; // Assign username to a variable
// THIS IS THE FIX: Create variables that can be passed by reference
$stmt->bind_param("ds", $totalHours, $username);
$stmt->execute();

// First get the user_id from the username
$userIdQuery = "SELECT id FROM users WHERE username = ?";
$stmt = $conn->prepare($userIdQuery);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("User not found!");
}

$userData = $result->fetch_assoc();
$userId = $userData['id'];

// Record the transaction in the database
$sql = "INSERT INTO balance_transactions (user_id, transaction_type, standard_change, premium_change, description) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
// Create variables that can be passed by reference
$transactionType = 'topup';
$standardChange = $pcType === 'standard' ? $totalHours : 0;
$premiumChange = $pcType === 'premium' ? $totalHours : 0;
$description = "Top-up of $totalHours hours to $pcType PC";
$stmt->bind_param("isdds", $userId, $transactionType, $standardChange, $premiumChange, $description);
$stmt->execute();

// Get the current balance from the database
$balanceQuery = "SELECT standard_balance, premium_balance FROM user_balance WHERE username = ?";
$stmt = $conn->prepare($balanceQuery);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // If no balance record exists, create one
    $createBalanceQuery = "INSERT INTO user_balance (user_id, username, standard_balance, premium_balance) VALUES (?, ?, 0, 0)";
    $stmt = $conn->prepare($createBalanceQuery);
    $stmt->bind_param("is", $userId, $username);
    $stmt->execute();
    
    $standardBalance = 0;
    $premiumBalance = 0;
} else {
    $balanceData = $result->fetch_assoc();
    $standardBalance = $balanceData['standard_balance'];
    $premiumBalance = $balanceData['premium_balance'];
}

// Close the database connection
$conn->close();

// Get the current time balance based on PC type
$currentBalance = ($pcType === 'standard') ? $standardBalance : $premiumBalance;

// Convert hours to minutes for display
$currentBalanceMinutes = $currentBalance * 60;

// Transaction is already recorded in the database, so we don't need to store it in session

// Clear the pending transaction
unset($_SESSION['pending_transaction']);

// Format the time for display
function formatMinutes($minutes) {
    $hours = floor($minutes / 60);
    $mins = $minutes % 60;
    
    if ($hours > 0) {
        return $hours . " hour" . ($hours > 1 ? "s" : "") . ($mins > 0 ? " and " . $mins . " minute" . ($mins > 1 ? "s" : "") : "");
    } else {
        return $mins . " minute" . ($mins > 1 ? "s" : "");
    }
}

$formattedTime = formatMinutes($totalMinutes);
$formattedTotalBalance = formatMinutes($currentBalanceMinutes);
?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="en" data-bss-forced-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Payment Successful - PC Top-up</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Titillium+Web:400,600,700">
    <link rel="stylesheet" href="css/topup.css">
    <link rel="icon" href="img/EYE LOGO.png" type="image/x-icon">
    <style>
        .success-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 30px;
            background-color: rgba(26, 26, 26, 0.7);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }
        
        .success-icon {
            font-size: 80px;
            color: #4CAF50;
            margin-bottom: 20px;
        }
        
        .transaction-details {
            background-color: rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            padding: 20px;
            margin: 30px 0;
            text-align: left;
        }
        
        .transaction-detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        
        .transaction-detail-label {
            color: #9d9d9d;
        }
        
        .transaction-detail-value {
            color: #ffffff;
            font-weight: bold;
        }
        
        .time-balance {
            background-color: rgba(76, 175, 80, 0.1);
            border: 1px solid rgba(76, 175, 80, 0.2);
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            display: inline-block;
        }
        
        .action-buttons {
            margin-top: 30px;
        }
        
        .action-buttons .btn {
            margin: 0 10px;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animated {
            animation: fadeInUp 0.5s ease-out;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
            <img src="img/eye-removebg-preview.png" alt="Logo" height="40">
          </a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav center-nav">
              <li class="nav-item">
                <a class="nav-link" aria-current="page" href="Dashboard.php">Dashboard</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="Features.php">Features</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="contact.php">Contacts</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="branches.php">Branches</a>
              </li>
            </ul>
            <?php if (isset($_SESSION['username'])): ?>
                <!-- Dropdown button when user is logged in -->
                <div class="ms-auto dropdown">
                  <button class="btn btn-sign-in dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php echo $_SESSION['username']; ?>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                    <li><a class="dropdown-item" href="/KADILIMAN/register/logout.php">Log Out</a></li>
                  </ul>
                </div>
            <?php else: ?>
                <!-- Regular button when user is not logged in -->
                <div class="ms-auto">
                  <a href="Registration.php" class="btn btn-sign-in">Sign In</a>
                </div>
            <?php endif; ?>
        </div>
      </nav>
    
    <div class="page-container">
        <div class="success-container animated">
            <div class="success-icon">✓</div>
            <h1 class="section-title">Payment Successful!</h1>
            <p class="text-white mb-4">Your PC time has been successfully topped up</p>
            
            <div class="transaction-details">
                <h5 class="mb-4 text-center">Transaction Details</h5>
                
                <div class="transaction-detail-row">
                    <span class="transaction-detail-label">PC Type:</span>
                    <span class="transaction-detail-value"><?php echo ucfirst($pcType); ?></span>
                </div>
                
                <div class="transaction-detail-row">
                    <span class="transaction-detail-label">Time Added:</span>
                    <span class="transaction-detail-value"><?php echo $formattedTime; ?></span>
                </div>
                
                <?php if ($bonusMinutes > 0): ?>
                <div class="transaction-detail-row">
                    <span class="transaction-detail-label">Includes Weekend Bonus:</span>
                    <span class="transaction-detail-value"><?php echo $bonusMinutes; ?> minutes</span>
                </div>
                <?php endif; ?>
                
                <div class="transaction-detail-row">
                    <span class="transaction-detail-label">Amount Paid:</span>
                    <span class="transaction-detail-value">₱<?php echo number_format($amount, 2); ?></span>
                </div>
                
                <div class="transaction-detail-row">
                    <span class="transaction-detail-label">Transaction ID:</span>
                    <span class="transaction-detail-value"><?php echo $transactionId; ?></span>
                </div>
                
                <div class="transaction-detail-row">
                    <span class="transaction-detail-label">Date:</span>
                    <span class="transaction-detail-value"><?php echo date('M d, Y h:i A'); ?></span>
                </div>
            </div>
            
            <div class="time-balance">
                <h5>Your Current Time Balance</h5>
                <h3 class="text-success mb-0"><?php echo $formattedTotalBalance; ?></h3>
            </div>
            
            <div class="action-buttons">
                <a href="Dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                <a href="topup.php" class="btn btn-outline-light">Top-up Again</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/js/bootstrap.bundle.min.js"></script>
</body>

</html>