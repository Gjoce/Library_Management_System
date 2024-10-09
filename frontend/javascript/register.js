document.getElementById('registerForm').addEventListener('submit', async function (event) {
    event.preventDefault(); // Prevent the form from submitting the traditional way

    // Get form values
    const username = document.getElementById('username').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    // Simple validation for password match
    if (password !== confirmPassword) {
        document.getElementById('errorMessage').innerText = 'Passwords do not match.';
        return;
    }

    // Prepare data to send
    const userData = {
        NAME: username,
        EMAIL: email,
        PASSWORD: password
    };

    try {
        const response = await fetch('http://localhost:8080/Library_Management_System/backend/api/users/add.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(userData)
        });

        const result = await response.json();

        if (response.ok) {
            // Optionally redirect to login or homepage
            window.location.href = 'loginUser.html'; 
        } else {
            // Handle error
            document.getElementById('errorMessage').innerText = result.message;
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('errorMessage').innerText = 'An error occurred. Please try again later.';
    }
});
