<?php
session_start();
?>
<!DOCTYPE html>
<html data-bs-theme="light" lang="en" data-bss-forced-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>PC Top-up</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Titillium+Web:400,600,700">
    <link rel="stylesheet" href="css/topup.css">
    <link rel="icon" href="img/EYE LOGO.png" type="image/x-icon">
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
                <a class="nav-link" href="Dashboard.php">Dashboard</a>
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
              </li>
            </ul>
            <?php if (isset($_SESSION['username'])): ?>
                <div class="ms-auto dropdown">
                  <button class="btn btn-sign-in dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php echo $_SESSION['username']; ?>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                    <li><a class="dropdown-item" href="/KADILIMAN/register/logout.php">Log Out</a></li>
                  </ul>
                </div>
            <?php else: ?>
                <div class="ms-auto">
                  <a href="Registration.php" class="btn btn-sign-in">Sign In</a>
                </div>
            <?php endif; ?>
          </div>
        </div>
      </nav>
    <div class="page-container">
        <h1 class="section-title">PC Top-up</h1>

        <div class="promo-banner">
            <h4 class="promo-title">WEEKEND SPECIAL OFFER!</h4>
            <p class="promo-text">Get 10% extra time on all 3+ hour top-ups this weekend!</p>
        </div>

        <!-- PC Selection Buttons -->
        <div class="pc-selection">
            <div class="pc-select-btn" id="standardButton" onclick="showPcType('standard')">
                <div class="pc-type-title">Standard PC</div>
                <div class="pc-type-price">Starting at ₱20/hour</div>
            </div>
            <div class="pc-select-btn" id="premiumButton" onclick="showPcType('premium')">
                <div class="pc-type-title">Premium PC</div>
                <div class="pc-type-price">Starting at ₱30/hour</div>
            </div>
        </div>

        <!-- PC Pricing Sections -->
        <div id="standard-pricing" class="pc-pricing-section" style="display: none;">
            <div class="pricing-container">
                <div class="pricing-card">
                    <span class="pc-type-badge standard-badge">Standard</span>
                    <div class="pricing-time">1 Hour</div>
                    <div class="pricing-price">₱20</div>
                    <a href="#" class="top-up-btn">Top-up</a>
                </div>
                
                <div class="pricing-card">
                    <span class="pc-type-badge standard-badge">Standard</span>
                    <div class="pricing-time">2 Hours</div>
                    <div class="pricing-price">₱38</div>
                    <a href="#" class="top-up-btn">Top-up</a>
                </div>
                
                <div class="pricing-card position-relative">
                    <span class="best-value">Best Value</span>
                    <span class="pc-type-badge standard-badge">Standard</span>
                    <div class="pricing-time">3 Hours</div>
                    <div class="pricing-price">₱54</div>
                    <a href="#" class="top-up-btn">Top-up</a>
                </div>
                
                <div class="pricing-card">
                    <span class="pc-type-badge standard-badge">Standard</span>
                    <div class="pricing-time">5 Hours</div>
                    <div class="pricing-price">₱85</div>
                    <a href="#" class="top-up-btn">Top-up</a>
                </div>
                
                <div class="pricing-card">
                    <span class="pc-type-badge standard-badge">Standard</span>
                    <div class="pricing-time">10 Hours</div>
                    <div class="pricing-price">₱160</div>
                    <a href="#" class="top-up-btn">Top-up</a>
                </div>
            </div>
        </div>
        
        <div id="premium-pricing" class="pc-pricing-section" style="display: none;">
            <div class="pricing-container">
                <div class="pricing-card">
                    <span class="pc-type-badge premium-badge">Premium</span>
                    <div class="pricing-time">1 Hour</div>
                    <div class="pricing-price">₱30</div>
                    <a href="#" class="top-up-btn">Top-up</a>
                </div>
                
                <div class="pricing-card">
                    <span class="pc-type-badge premium-badge">Premium</span>
                    <div class="pricing-time">2 Hours</div>
                    <div class="pricing-price">₱57</div>
                    <a href="#" class="top-up-btn">Top-up</a>
                </div>
                
                <div class="pricing-card position-relative">
                    <span class="best-value">Best Value</span>
                    <span class="pc-type-badge premium-badge">Premium</span>
                    <div class="pricing-time">3 Hours</div>
                    <div class="pricing-price">₱81</div>
                    <a href="#" class="top-up-btn">Top-up</a>
                </div>
                
                <div class="pricing-card">
                    <span class="pc-type-badge premium-badge">Premium</span>
                    <div class="pricing-time">5 Hours</div>
                    <div class="pricing-price">₱135</div>
                    <a href="#" class="top-up-btn">Top-up</a>
                </div>
                
                <div class="pricing-card">
                    <span class="pc-type-badge premium-badge">Premium</span>
                    <div class="pricing-time">10 Hours</div>
                    <div class="pricing-price">₱250</div>
                    <a href="#" class="top-up-btn">Top-up</a>
                </div>
            </div>
        </div>
        
        <!-- Additional Info Section -->
        <div class="mt-5">
            <h3 class="section-title">Additional Information</h3>
            <div class="card" style="background-color: rgba(26, 26, 26, 0.7); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 10px; padding: 20px; margin-bottom: 20px;">
                <ul style="list-style-type: none; padding-left: 0;">
                    <li style="margin-bottom: 10px;">• Unused time will be saved in your account for future use</li>
                    <li style="margin-bottom: 10px;">• Time can be used across any of our branches</li>
                    <li style="margin-bottom: 10px;">• Get special discounts with monthly packages</li>
                    <li style="margin-bottom: 10px;">• Free 15 minutes when you refer a friend</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- JavaScript for PC type selection -->
    <script>
    // Function to show the selected PC type pricing and update active button
// Function to show the selected PC type pricing and update active button
function showPcType(type) {
    // Hide all pricing sections
    document.querySelectorAll('.pc-pricing-section').forEach(function(section) {
        section.style.display = 'none';
    });
    
    // Show selected pricing section
    document.getElementById(type + '-pricing').style.display = 'block';
    
    // Update active button
    document.querySelectorAll('.pc-select-btn').forEach(function(button) {
        button.classList.remove('active');
    });
    document.getElementById(type + 'Button').classList.add('active');
}

// Get PC type from URL parameter
function getInitialPcType() {
    const urlParams = new URLSearchParams(window.location.search);
    const pcType = urlParams.get('pcType');
    
    // Return 'standard' if pcType parameter is 'standard', otherwise return 'premium'
    return pcType === 'standard' ? 'standard' : 'premium';
}

// Handle top-up button clicks
function setupTopupButtons() {
    const topupButtons = document.querySelectorAll('.top-up-btn');
    
    topupButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get pricing details from the parent card
            const card = this.closest('.pricing-card');
            const pcType = card.querySelector('.pc-type-badge').textContent.toLowerCase();
            const timeText = card.querySelector('.pricing-time').textContent;
            const priceText = card.querySelector('.pricing-price').textContent;
            
            // Extract hours and price values
            const hours = parseInt(timeText.split(' ')[0]);
            const price = parseFloat(priceText.replace('₱', ''));
            
            // Redirect to payment processing page with parameters
            window.location.href = `payment-process.php?pcType=${pcType}&hours=${hours}&amount=${price}`;
        });
    });
}

// Initialize with the correct PC type when the page loads
document.addEventListener('DOMContentLoaded', function() {
    const initialPcType = getInitialPcType();
    showPcType(initialPcType);
    
    // Setup top-up button click handlers
    setupTopupButtons();
});
    </script>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/js/bootstrap.bundle.min.js"></script>
</body>

</html>