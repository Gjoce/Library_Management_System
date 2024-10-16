document.getElementById('registerForm').addEventListener('submit', async function (event) {
    event.preventDefault(); 

   
    const username = document.getElementById('username').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

   
    const usernameRegex = /^[a-zA-Z0-9]{3,20}$/;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$/;

    if (!usernameRegex.test(username)) {
        document.getElementById('errorMessage').innerText = 'Username must be alphanumeric and 3-20 characters long.';
        return;
    }

    
    if (!emailRegex.test(email)) {
        document.getElementById('errorMessage').innerText = 'Please enter a valid email address.';
        return;
    }

 
    if (!passwordRegex.test(password)) {
        document.getElementById('errorMessage').innerText = 'Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number, and one special character.';
        return;
    }

  
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
        const response = await fetch('http://localhost:8080/api/users/add.php', {
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
