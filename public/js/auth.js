// auth.js - Handles login and register form submissions

document.addEventListener('DOMContentLoaded', function() {
    // Login form handler
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = loginForm.email.value.trim();
            const password = loginForm.password.value.trim();
            
            if (!email || !password) {
                alert('Please enter both email and password.');
                return;
            }

            // Create form data
            const formData = new FormData();
            formData.append('email', email);
            formData.append('password', password);

            // Send AJAX request
            fetch('../backend/auth/login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.text();
            })
            .then(data => {
                console.log('Response data:', data);
                console.log('Response data length:', data.length);
                console.log('Response data trimmed:', data.trim());
                
                // Trim the response and check for success
                const trimmedData = data.trim();
                if (trimmedData === 'success') {
                    console.log('Login successful, redirecting...');
                    window.location.href = 'home.html';
                } else {
                    console.log('Login failed with response:', trimmedData);
                    alert('Login failed: ' + trimmedData);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Login failed. Please try again.');
            });
        });
    }

    // Register form handler
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const username = registerForm.username.value.trim();
            const email = registerForm.email.value.trim();
            const password = registerForm.password.value.trim();
            
            if (!username || !email || !password) {
                alert('Please fill in all fields.');
                return;
            }

            // Create form data
            const formData = new FormData();
            formData.append('username', username);
            formData.append('email', email);
            formData.append('password', password);

            // Send AJAX request
            fetch('../backend/auth/register.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Register response status:', response.status);
                return response.text();
            })
            .then(data => {
                console.log('Register response data:', data);
                const trimmedData = data.trim();
                if (trimmedData === 'success') {
                    alert('Registration successful! Please login.');
                    window.location.href = 'login.html';
                } else {
                    alert('Registration failed: ' + trimmedData);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Registration failed. Please try again.');
            });
        });
    }
}); 