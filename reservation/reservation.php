<?php
session_start();
?>
<!DOCTYPE html>
<html data-bs-theme="light" lang="en" data-bss-forced-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>PC Reservation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Titillium+Web:400,600,700">
    <link rel="stylesheet" href="/Kadiliman/css/reservation.css">
    <link rel="icon" href="/Kadiliman/img/EYE LOGO.png" type="image/x-icon">
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid">
          <a class="navbar-brand" href="/KADILIMAN/index.php">
            <!-- Replace with your actual logo -->
            <img src="/Kadiliman/img/eye-removebg-preview.png" alt="Logo" height="40">
          </a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav center-nav">
              <li class="nav-item">
                <a class="nav-link" href="/KADILIMAN/Dashboard.php">Dashboard</a>
              </li>
              <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="/KADILIMAN/Features.php">Features</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="../contact.php">Contacts</a>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Branches
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                    <li><a class="dropdown-item" href="branches/manila.html">Manila</a></li>
                    <li><a class="dropdown-item" href="branches/quezon-city.html">Quezon City</a></li>
                    <li><a class="dropdown-item" href="branches/makati.html">Makati</a></li>
                    <li><a class="dropdown-item" href="branches/pasig.html">Pasig</a></li>
                    <li><a class="dropdown-item" href="branches/alabang.html">Alabang</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="branches/all-locations.html">All Locations</a></li>
                </ul>
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
              </ul>
            </div>
          </div>
        </div>
      </nav>

    <div class="page-container">
        <h1 class="section-title">PC Reservation</h1>

        <!-- Branch and Date Selection -->
        <div class="selection-container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="reservationDate" class="form-label">Reservation Date</label>
                    <input type="date" class="form-control" id="reservationDate">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="startTime" class="form-label">Start Time</label>
                    <select class="form-select" id="startTime">
                        <option value="08:00">08:00 AM</option>
                        <option value="09:00">09:00 AM</option>
                        <option value="10:00">10:00 AM</option>
                        <option value="11:00">11:00 AM</option>
                        <option value="12:00">12:00 PM</option>
                        <option value="13:00">01:00 PM</option>
                        <option value="14:00">02:00 PM</option>
                        <option value="15:00">03:00 PM</option>
                        <option value="16:00">04:00 PM</option>
                        <option value="17:00">05:00 PM</option>
                        <option value="18:00">06:00 PM</option>
                        <option value="19:00">07:00 PM</option>
                        <option value="20:00">08:00 PM</option>
                        <option value="21:00">09:00 PM</option>
                        <option value="22:00">10:00 PM</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <button class="btn btn-primary" id="checkAvailability" style="background-color: #ff6b00; border: none;">Check Availability</button>
                </div>
            </div>
        </div>

        <!-- PC Map -->
        <div class="pc-map-container">
            <div class="map-header">
                <h3 style="margin-bottom: 0;">PC Layout</h3>
                <div class="legend">
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: rgba(70, 130, 180, 0.3); border: 5px solid rgba(70, 130, 180, 0.6);"></div>
                        <span>Standard</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: rgba(255, 215, 0, 0.2); border: 5px solid rgba(255, 215, 0, 0.6);"></div>
                        <span>Premium</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: rgba(255, 0, 0, 0.2); border: 5px solid rgba(255, 0, 0, 0.6);"></div>
                        <span>Booked</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: rgba(128, 128, 128, 0.3); border: 5px solid rgba(128, 128, 128, 0.6);"></div>
                        <span>Maintenance</span>
                    </div>
                </div>
            </div>

            <!-- Replace the Bootstrap tab system with the custom tab selector from Registration.php -->
            <div class="tab-selector">
                <button class="tab-btn active" id="standard-tab" onclick="switchPcTab('standard')">Standard PCs</button>
                <button class="tab-btn" id="premium-tab" onclick="switchPcTab('premium')">Premium PCs</button>
            </div>

            <!-- Update tab content structure -->
            <div class="pc-tab-content">
                <div class="pc-tab-container" id="standard-container">
                    <div class="pc-grid" id="standardPcGrid">
                        <!-- Standard PC Grid will be generated here by JavaScript -->
                    </div>
                </div>
                <div class="pc-tab-container" id="premium-container" style="display: none;">
                    <div class="pc-grid" id="premiumPcGrid">
                        <!-- Premium PC Grid will be generated here by JavaScript -->
                    </div>
                </div>
            </div>

            <div class="time-selection">
                <span class="me-2">Duration:</span>
                <button class="time-btn" data-hours="1">1 Hour</button>
                <button class="time-btn" data-hours="2">2 Hours</button>
                <button class="time-btn active" data-hours="3">3 Hours</button>
                <button class="time-btn" data-hours="5">5 Hours</button>
                <button class="time-btn" data-hours="10">10 Hours</button>
            </div>
        </div>

        <!-- Reservation Details -->
        <div class="reservation-details">
            <h3>Reservation Details</h3>
            <div class="detail-row">
                <div class="detail-label">PC Number:</div>
                <div class="detail-value" id="selectedPC">Not selected</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">PC Type:</div>
                <div class="detail-value" id="pcType">-</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Date:</div>
                <div class="detail-value" id="reservationDateDisplay">-</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Time:</div>
                <div class="detail-value" id="reservationTimeDisplay">-</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Duration:</div>
                <div class="detail-value" id="durationDisplay">3 Hours</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Total Price:</div>
                <div class="detail-value" id="priceDisplay">-</div>
            </div>
            <button class="reserve-btn" id="reserveBtn" disabled>Complete Reservation</button>
        </div>

        <!-- Transaction History -->
        <div class="transaction-history">
            <h3>Transaction History</h3>
            <div id="transactionHistory" class="transaction-history-wrapper">
                <!-- Transaction history will be dynamically loaded here -->
            </div>
        </div>

        <!-- Additional Information -->
        <div>
            <h3 class="section-title">Reservation Policy</h3>
            <div class="card" style="background-color: rgba(26, 26, 26, 0.7); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 10px; padding: 20px; margin-bottom: 20px;">
                <ul style="list-style-type: none; padding-left: 0;">
                    <li style="margin-bottom: 10px;">• Reservations must be made at least 1 hour in advance</li>
                    <li style="margin-bottom: 10px;">• Cancellations are allowed up to 1 hour before reservation time</li>
                    <li style="margin-bottom: 10px;">• Late arrivals over 15 minutes may forfeit the reservation</li>
                    <li style="margin-bottom: 10px;">• Weekend reservations require a 50% deposit</li>
                    <li style="margin-bottom: 10px;">• Premium PCs include high-end graphics cards and gaming peripherals</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Cancel Confirmation Modal -->
    <div id="cancelModal" class="modal">
        <div class="modal-content">
            <h3>Are you sure you want to cancel this reservation?</h3>
            <button id="confirmCancelBtn" class="btn btn-confirm">Yes</button>
            <button id="closeCancelBtn" class="btn btn-cancel">No</button>
        </div>
    </div>

    <script src="/Kadiliman/js/reservation.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/js/bootstrap.bundle.min.js"></script>
</body>

</html>