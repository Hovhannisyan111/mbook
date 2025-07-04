// admin_auth.js - Handles admin authentication

document.addEventListener('DOMContentLoaded', function() {
    const adminLoginForm = document.getElementById('adminLoginForm');
    
    adminLoginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(adminLoginForm);
        
        // Show loading state
        const submitBtn = adminLoginForm.querySelector('.login-btn');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Logging in...';
        submitBtn.disabled = true;
        
        // Send login request
        fetch('../backend/admin/admin_login.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redirect to admin panel
                window.location.href = 'admin.html';
            } else {
                alert('Login failed: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Login error:', error);
            alert('Login failed. Please try again.');
        })
        .finally(() => {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
    });
}); 