document.getElementById('loginForm').addEventListener('submit', async function(event) {
    event.preventDefault();

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    const loginData = {
        email: email,
        password: password
    };

    try {
        const response = await fetch('http://localhost:8080/Library_Management_System/backend/api/users/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(loginData)
        });

        const responseText = await response.text();
        const data = JSON.parse(responseText);  // Parse the response

        // Check if login was successful
        if (response.ok && data.token) {
            // Store the JWT token in localStorage
            localStorage.setItem('authToken', data.token);

            // Redirect to dashboard after successful login
            setTimeout(() => {
                window.location.href = 'dashboard.html';  // Redirect to dashboard
            }, 500);
        } else {
            // Show error message if login failed
            document.getElementById('errorMessage').textContent = data.message || 'Login failed!';
        }

    } catch (error) {
        console.error('Error:', error);
        document.getElementById('errorMessage').textContent = 'An error occurred. Please try again.';
    }
});
