document.addEventListener('DOMContentLoaded', function() {
    // Handle sidebar item clicks for smooth scrolling and highlighting
    const sidebarItems = document.querySelectorAll('.sidebar-item');
    
    // Add active class to sidebar item based on scroll position
    function setActiveSection() {
        const scrollPosition = window.scrollY;
        
        // Get all sections
        const sections = document.querySelectorAll('.content-section');
        
        // Find the section that is currently in view
        sections.forEach(section => {
            const sectionTop = section.offsetTop - 100; // Offset for better UX
            const sectionHeight = section.offsetHeight;
            const sectionId = section.getAttribute('id');
            
            if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                // Remove active class from all sidebar items
                sidebarItems.forEach(item => item.classList.remove('active'));
                
                // Add active class to the corresponding sidebar item
                const activeItem = document.querySelector(`.sidebar-item[data-section="${sectionId}"]`);
                if (activeItem) {
                    activeItem.classList.add('active');
                }
            }
        });
    }
    
    // Handle sidebar item clicks for smooth scrolling
    sidebarItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get the section ID from the data attribute
            const sectionId = this.getAttribute('data-section');
            const section = document.getElementById(sectionId);
            
            if (section) {
                // Smooth scroll to the section
                window.scrollTo({
                    top: section.offsetTop - 80, // Offset for navbar
                    behavior: 'smooth'
                });
                
                // Instead of adding to history, replace the current state
                // This way the back button will exit the page instead of going through sections
                window.history.replaceState(null, null, `#${sectionId}`);
                
                // Update active state on sidebar
                sidebarItems.forEach(sItem => sItem.classList.remove('active'));
                this.classList.add('active');
            }
        });
    });
    
    // Handle initial hash in URL
    function checkUrlHash() {
        if (window.location.hash) {
            // Get the section ID from the URL hash (remove the # symbol)
            const sectionId = window.location.hash.substring(1);
            const section = document.getElementById(sectionId);
            
            if (section) {
                // Scroll to the section after a small delay to ensure everything is loaded
                setTimeout(() => {
                    window.scrollTo({
                        top: section.offsetTop - 80, // Offset for navbar
                        behavior: 'smooth'
                    });
                    
                    // Update active state on sidebar
                    sidebarItems.forEach(item => item.classList.remove('active'));
                    const activeItem = document.querySelector(`.sidebar-item[data-section="${sectionId}"]`);
                    if (activeItem) {
                        activeItem.classList.add('active');
                    }
                }, 300);
                
                return true;
            }
        }
        
        return false;
    }
    
    // If no hash in URL, make sure the first section is active
    if (!checkUrlHash()) {
        const firstItem = document.querySelector('.sidebar-item');
        if (firstItem) {
            firstItem.classList.add('active');
        }
    }
    
    // Listen for scroll events to update active section
    window.addEventListener('scroll', setActiveSection);
    
    // Listen for hash changes (back/forward browser navigation)
    window.addEventListener('hashchange', checkUrlHash);
    
    // 2FA verification code display toggle
    const sendButton = document.querySelector('#two-factor .btn-primary');
    if (sendButton) {
        sendButton.addEventListener('click', function() {
            document.getElementById('verification-code-form').style.display = 'block';
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';
            
            setTimeout(() => {
                this.innerHTML = 'Sent';
                this.disabled = false;
            }, 1500);
        });
    }
    
    // Auto-focus next input in verification code
    const codeInputs = document.querySelectorAll('#verification-code-form input');
    codeInputs.forEach((input, index) => {
        input.addEventListener('input', function() {
            if (this.value.length === this.maxLength && index < codeInputs.length - 1) {
                codeInputs[index + 1].focus();
            }
        });
        
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && this.value.length === 0 && index > 0) {
                codeInputs[index - 1].focus();
            }
        });
    });
});
