document.addEventListener('DOMContentLoaded', function() {
    // Set today's date and select next available time slot
    const today = new Date();
    
    // Format date as YYYY-MM-DD for input[type="date"]
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    const formattedDate = `${year}-${month}-${day}`;
    
    // Set the date input value
    document.getElementById('reservationDate').value = formattedDate;
    
    // Get current hour and select the next available time slot
    const currentHour = today.getHours();
    const startTime = document.getElementById('startTime');
    
    // Find the next available time slot
    let selectedIndex = 0;
    for (let i = 0; i < startTime.options.length; i++) {
        const optionHour = parseInt(startTime.options[i].value.split(':')[0]);
        if (optionHour > currentHour) {
            selectedIndex = i;
            break;
        }
    }
    
    startTime.selectedIndex = selectedIndex;

    // Initialize the page
    generatePcGrid();
    updateDateDisplay();
    updateTimeDisplay();

    // Add event listeners
    document.getElementById('checkAvailability').addEventListener('click', checkAvailability);
    document.getElementById('reservationDate').addEventListener('change', updateDateDisplay);
    document.getElementById('startTime').addEventListener('change', updateTimeDisplay);

    // Time duration buttons
    document.querySelectorAll('.time-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            document.querySelectorAll('.time-btn').forEach(b => b.classList.remove('active'));

            // Add active class to clicked button
            this.classList.add('active');

            // Update duration display
            const hours = this.getAttribute('data-hours');
            document.getElementById('durationDisplay').textContent = hours + ' Hour' + (hours > 1 ? 's' : '');

            // Update price
            updatePrice();
        });
    });

    // Reserve button
    document.getElementById('reserveBtn').addEventListener('click', completeReservation);
});

// Add tab switching function similar to Registration.php
function switchPcTab(tabName) {
    // Hide all containers
    document.getElementById('standard-container').style.display = 'none';
    document.getElementById('premium-container').style.display = 'none';
    
    // Show selected container
    document.getElementById(tabName + '-container').style.display = 'block';
    
    // Update active tab button
    document.getElementById('standard-tab').classList.remove('active');
    document.getElementById('premium-tab').classList.remove('active');
    document.getElementById(tabName + '-tab').classList.add('active');
    
    console.log('Switched to ' + tabName + ' tab');
}

function generatePcGrid() {
    const standardGrid = document.getElementById('standardPcGrid');
    const premiumGrid = document.getElementById('premiumPcGrid');
    standardGrid.innerHTML = '';
    premiumGrid.innerHTML = '';

    // Count for each type of PC
    let standardCount = 0;
    let premiumCount = 0;

    // Generate 70 PCs - 50 standard and 20 premium
    for (let i = 1; i <= 70; i++) {
        let pcClass = 'pc-standard';
        let pcStatus = '';
        let pcType = 'S';

        // Make the last 20 PCs premium (51-70)
        if (i > 50) {
            pcClass = 'pc-premium';
            pcType = 'P';
        }

        // Create PC item
        const pcItem = document.createElement('div');
        pcItem.className = `pc-item ${pcClass} ${pcStatus}`;
        pcItem.setAttribute('data-pc', i);
        pcItem.setAttribute('data-type', pcType === 'P' ? 'Premium' : 'Standard');

        pcItem.innerHTML = `
            <span class="pc-type-indicator">${pcType}</span>
            <span style="font-size: 1.2rem; margin-bottom: 3px;">${i}</span>
            ${pcStatus === 'pc-booked' ? '<span style="font-size: 0.7rem;">Booked</span>' : ''}
            ${pcStatus === 'pc-maintenance' ? '<span style="font-size: 0.7rem;">Maintenance</span>' : ''}
        `;

        // Add click event for selectable PCs
        if (!pcStatus) {
            pcItem.addEventListener('click', selectPC);
        }

        // Append to the appropriate grid and increment counter
        if (pcType === 'P') {
            premiumGrid.appendChild(pcItem);
            premiumCount++;
        } else {
            standardGrid.appendChild(pcItem);
            standardCount++;
        }
    }
    
    // Check availability immediately when the page loads
    checkAvailability();
    
    adjustGridLayout();
    
    // Add window resize listener to adjust grid on screen size changes
    window.addEventListener('resize', adjustGridLayout);
    
    console.log(`Generated ${standardCount} standard PCs and ${premiumCount} premium PCs`);
}

function adjustGridLayout() {
    const standardGrid = document.getElementById('standardPcGrid');
    const premiumGrid = document.getElementById('premiumPcGrid');
    
    // Get window width
    const windowWidth = window.innerWidth;
    
    // Adjust columns based on screen size
    if (windowWidth >= 1200) {
        // Large screens: 10 columns for standard, 5 for premium
        standardGrid.style.gridTemplateColumns = 'repeat(10, 1fr)';
        premiumGrid.style.gridTemplateColumns = 'repeat(5, 1fr)';
    } else if (windowWidth >= 768) {
        // Medium screens: 8 columns for standard, 4 for premium
        standardGrid.style.gridTemplateColumns = 'repeat(8, 1fr)';
        premiumGrid.style.gridTemplateColumns = 'repeat(4, 1fr)';
    } else if (windowWidth >= 576) {
        // Small screens: 5 columns for standard, 3 for premium
        standardGrid.style.gridTemplateColumns = 'repeat(5, 1fr)';
        premiumGrid.style.gridTemplateColumns = 'repeat(3, 1fr)';
    } else {
        // Extra small screens: 3 columns for standard, 2 for premium
        standardGrid.style.gridTemplateColumns = 'repeat(3, 1fr)';
        premiumGrid.style.gridTemplateColumns = 'repeat(2, 1fr)';
    }
    
    console.log(`Adjusted grid layout for window width: ${windowWidth}px`);
}

function selectPC(event) {
    // Remove selection from previously selected PC
    const previouslySelected = document.querySelector('.pc-selected');
    if (previouslySelected) {
        previouslySelected.classList.remove('pc-selected');
    }

    // Add selection to clicked PC
    this.classList.add('pc-selected');

    // Update reservation details
    const pcNumber = this.getAttribute('data-pc');
    const pcType = this.getAttribute('data-type');

    document.getElementById('selectedPC').textContent = 'PC #' + pcNumber;
    document.getElementById('pcType').textContent = pcType;

    // Enable reserve button
    document.getElementById('reserveBtn').disabled = false;

    // Update price
    updatePrice();
}

function updateDateDisplay() {
    const dateInput = document.getElementById('reservationDate');
    const dateDisplay = document.getElementById('reservationDateDisplay');

    if (dateInput.value) {
        const date = new Date(dateInput.value);
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        dateDisplay.textContent = date.toLocaleDateString('en-US', options);
    } else {
        dateDisplay.textContent = '-';
    }
}

function updateTimeDisplay() {
    const timeSelect = document.getElementById('startTime');
    const timeDisplay = document.getElementById('reservationTimeDisplay');
    const selectedTime = timeSelect.value;

    if (selectedTime) {
        // Parse the 24-hour time format
        const hour = parseInt(selectedTime.split(':')[0]);
        let period = hour >= 12 ? 'PM' : 'AM';
        let displayHour = hour % 12;
        if (displayHour === 0) displayHour = 12;

        // Get the selected duration
        const durationBtn = document.querySelector('.time-btn.active');
        const duration = parseInt(durationBtn.getAttribute('data-hours'));

        // Calculate end time
        const endHour = (hour + duration) % 24;
        let endPeriod = endHour >= 12 ? 'PM' : 'AM';
        let displayEndHour = endHour % 12;
        if (displayEndHour === 0) displayEndHour = 12;

        // Format the time display with proper padding for minutes
        timeDisplay.textContent = `${displayHour}:00 ${period} - ${displayEndHour}:00 ${endPeriod}`;
        
        // Also update the data attribute for more consistent data access
        timeDisplay.setAttribute('data-start-time', `${displayHour}:00 ${period}`);
        timeDisplay.setAttribute('data-end-time', `${displayEndHour}:00 ${endPeriod}`);
    } else {
        timeDisplay.textContent = '-';
        timeDisplay.removeAttribute('data-start-time');
        timeDisplay.removeAttribute('data-end-time');
    }
}

function updatePrice() {
    const pcType = document.getElementById('pcType').textContent;
    const durationText = document.getElementById('durationDisplay').textContent;
    const duration = parseInt(durationText);
    const priceDisplay = document.getElementById('priceDisplay');

    if (pcType === 'Standard' && duration) {
        let price = 0;
        switch (duration) {
            case 1: price = 20; break;
            case 2: price = 38; break;
            case 3: price = 54; break;
            case 5: price = 85; break;
            case 10: price = 160; break;
            default: price = duration * 20;
        }
        priceDisplay.textContent = '₱' + price;
    } else if (pcType === 'Premium' && duration) {
        let price = 0;
        switch (duration) {
            case 1: price = 30; break;
            case 2: price = 57; break;
            case 3: price = 81; break;
            case 5: price = 135; break;
            case 10: price = 250; break;
            default: price = duration * 30;
        }
        priceDisplay.textContent = '₱' + price;
    } else {
        priceDisplay.textContent = '-';
    }
}

function checkAvailability() {
    const date = document.getElementById('reservationDate').value;
    const startTime = document.getElementById('startTime').value;
    const durationBtn = document.querySelector('.time-btn.active');
    const duration = parseInt(durationBtn.getAttribute('data-hours'));

    // Create form data to send
    const formData = new FormData();
    formData.append('date', date);
    formData.append('start_time', startTime);
    formData.append('duration', duration);

    // Send request to check availability
    fetch('check_availability.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Apply booking data to the grid
            updatePcGridStatus(data.booked_pcs, data.maintenance_pcs);
            
            // Update the time display
            updateTimeDisplay();
        } else {
            console.error('Error checking availability:', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function updatePcGridStatus(bookedPCs, maintenancePCs) {
    // Reset all PCs to their default state first
    resetPcGridStatus();
    
    // Mark booked PCs
    bookedPCs.forEach(pc => {
        const pcNumber = parseInt(pc.pc_number);
        const pcElements = document.querySelectorAll(`.pc-item[data-pc="${pcNumber}"]`);
        
        pcElements.forEach(pcElement => {
            pcElement.classList.add('pc-booked');
            pcElement.classList.remove('pc-standard', 'pc-premium');
            
            // Remove click event listener
            pcElement.replaceWith(pcElement.cloneNode(true));
            
            // Update the inner HTML to show "Booked" status
            const pcType = pcElement.getAttribute('data-type').charAt(0);
            pcElement.innerHTML = `
                <span class="pc-type-indicator">${pcType}</span>
                <span style="font-size: 1.2rem; margin-bottom: 3px;">${pcNumber}</span>
                <span style="font-size: 0.7rem;">Booked</span>
            `;
        });
    });
    
    // Mark fnance PCs
    maintenancePCs.forEach(pcNumber => {
        const pcElements = document.querySelectorAll(`.pc-item[data-pc="${pcNumber}"]`);
        
        pcElements.forEach(pcElement => {
            pcElement.classList.add('pc-maintenance');
            pcElement.classList.remove('pc-standard', 'pc-premium');
            
            // Remove click event listener
            pcElement.replaceWith(pcElement.cloneNode(true));
            
            // Update the inner HTML to show "Maintenance" status
            const pcType = pcElement.getAttribute('data-type').charAt(0);
            pcElement.innerHTML = `
                <span class="pc-type-indicator">${pcType}</span>
                <span style="font-size: 1.2rem; margin-bottom: 3px;">${pcNumber}</span>
                <span style="font-size: 0.7rem;">Maintenance</span>
            `;
        });
    });
}

function resetPcGridStatus() {
    const pcItems = document.querySelectorAll('.pc-item');
    
    pcItems.forEach(pcItem => {
        // Remove status classes
        pcItem.classList.remove('pc-booked', 'pc-maintenance');
        
        // Get PC number and type
        const pcNumber = parseInt(pcItem.getAttribute('data-pc'));
        const pcType = pcNumber > 50 ? 'Premium' : 'Standard';
        const pcTypeChar = pcType === 'Premium' ? 'P' : 'S';
        
        // Add appropriate type class
        if (pcType === 'Premium') {
            pcItem.classList.add('pc-premium');
            pcItem.classList.remove('pc-standard');
        } else {
            pcItem.classList.add('pc-standard');
            pcItem.classList.remove('pc-premium');
        }
        
        // Reset inner HTML
        pcItem.innerHTML = `
            <span class="pc-type-indicator">${pcTypeChar}</span>
            <span style="font-size: 1.2rem; margin-bottom: 3px;">${pcNumber}</span>
        `;
        
        // Add click event for selectable PCs
        pcItem.addEventListener('click', selectPC);
    });
}

function completeReservation() {
    fetch('../includes/check_login_status.php')
    .then(response => response.json())
    .then(data => {
        if (!data.logged_in) {
            // Redirect to login page
            window.location.href = '/KADILIMAN/Registration.php?redirect=reservation/reservation.php';
            return;
        }
        
        // User is logged in, proceed with reservation
        const pcNumberText = document.getElementById('selectedPC').textContent;
        const pcNumber = parseInt(pcNumberText.replace('PC #', ''));
        const pcType = document.getElementById('pcType').textContent;
        const dateDisplay = document.getElementById('reservationDateDisplay').textContent;
        const timeDisplay = document.getElementById('reservationTimeDisplay').textContent;
        const durationText = document.getElementById('durationDisplay').textContent;
        const price = document.getElementById('priceDisplay').textContent;
        
        // Get raw values for backend
        const date = document.getElementById('reservationDate').value;
        const startTime = document.getElementById('startTime').value;
        const durationBtn = document.querySelector('.time-btn.active');
        const duration = parseInt(durationBtn.getAttribute('data-hours'));
        
        // Create form data
        const formData = new FormData();
        formData.append('pc_number', pcNumber);
        formData.append('pc_type', pcType); // This will store the PC type (Standard or Premium)
        formData.append('date', date);
        formData.append('start_time', startTime);
        formData.append('duration', duration);
        formData.append('price', price);
        formData.append('formatted_time', timeDisplay);
        
        // Send reservation data to server
        fetch('save_reservation.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message with "pending" status
                alert(`Reservation Submitted!\n\nDetails:\n${pcNumberText} (${pcType})\n${dateDisplay}\n${timeDisplay}\nTotal: ${price}\n\nYour reservation is pending approval.`);
                // Reset form
                resetReservationForm();
                // Update availability to reflect the new reservation
                checkAvailability();
            } else {
                alert('Failed to submit reservation: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing your reservation. Please try again.');
        });
    })
    .catch(error => {
        console.error('Error checking login status:', error);
        alert('Please log in to complete your reservation.');
    });
}

function resetReservationForm() {
    // Reset selection and form fields
    const previouslySelected = document.querySelector('.pc-selected');
    if (previouslySelected) {
        previouslySelected.classList.remove('pc-selected');
    }
    
    document.getElementById('selectedPC').textContent = 'Not selected';
    document.getElementById('pcType').textContent = '-';
    document.getElementById('priceDisplay').textContent = '-';
    document.getElementById('reserveBtn').disabled = true;
}

document.addEventListener('DOMContentLoaded', () => {
    const startTimeSelect = document.getElementById('startTime');
    const durationButtons = document.querySelectorAll('.time-btn');
    const reservationTimeDisplay = document.getElementById('reservationTimeDisplay');

    let selectedDuration = 3; // Default duration in hours

    // Update duration when a button is clicked
    durationButtons.forEach(button => {
        button.addEventListener('click', () => {
            durationButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            selectedDuration = parseInt(button.getAttribute('data-hours'));
            updateReservationTime();
        });
    });

    // Update reservation time display
    const updateReservationTime = () => {
        const startTime = startTimeSelect.value;
        if (startTime) {
            const [startHour, startMinute] = startTime.split(':').map(Number);
            const endHour = (startHour + selectedDuration) % 24;
            const endTime = `${endHour.toString().padStart(2, '0')}:${startMinute.toString().padStart(2, '0')}`;
            reservationTimeDisplay.textContent = `${startTime} - ${endTime}`;
        } else {
            reservationTimeDisplay.textContent = '-';
        }
    };

    // Update time display when start time changes
    startTimeSelect.addEventListener('change', updateReservationTime);
});

function showCancelDialog(reservationId) {
    // Display the custom modal
    const modal = document.getElementById('cancelModal');
    modal.style.display = 'block';

    // Add event listeners for Yes and No buttons
    document.getElementById('confirmCancelBtn').onclick = function () {
        cancelReservation(reservationId);
        modal.style.display = 'none';
    };

    document.getElementById('closeCancelBtn').onclick = function () {
        modal.style.display = 'none';
    };
}

function fetchTransactionHistory() {
    fetch('fetch_transaction_history.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const transactionContainer = document.getElementById('transactionHistory');
                transactionContainer.innerHTML = ''; // Clear existing content

                if (data.transactions.length === 0) {
                    transactionContainer.innerHTML = '<p>No transaction history available.</p>';
                    return;
                }

                data.transactions.forEach(transaction => {
                    const transactionItem = document.createElement('div');
                    transactionItem.className = 'transaction-item';
                    transactionItem.innerHTML = `
                        <div><strong>PC #${transaction.pc_number} (${transaction.pc_type})</strong></div>
                        <div>Date: ${transaction.date}</div>
                        <div>Time: ${transaction.time}</div>
                        <div>Duration: ${transaction.duration}</div>
                        <div>Status: ${transaction.status}</div>
                        <div>Price: ${transaction.price}</div>
                        <div>Reserved On: ${transaction.created_at}</div>
                        ${
                            transaction.status.toLowerCase() !== 'canceled'
                                ? `<button class="btn btn-danger btn-sm cancel-btn" data-id="${transaction.id}">Cancel Reservation</button>`
                                : ''
                        }
                        <hr>
                    `;
                    transactionContainer.appendChild(transactionItem);
                });

                // Add event listeners for cancel buttons
                document.querySelectorAll('.cancel-btn').forEach(button => {
                    button.addEventListener('click', function () {
                        const reservationId = this.getAttribute('data-id');
                        showCancelDialog(reservationId);
                    });
                });
            } else {
                console.error('Error fetching transaction history:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function cancelReservation(reservationId) {
    const formData = new FormData();
    formData.append('reservation_id', reservationId);

    fetch('cancel_reservation.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Reservation successfully canceled.');
                fetchTransactionHistory(); // Refresh the transaction history
            } else {
                alert('Failed to cancel reservation: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while canceling the reservation. Please try again.');
        });
}

// Call fetchTransactionHistory on page load
document.addEventListener('DOMContentLoaded', fetchTransactionHistory);