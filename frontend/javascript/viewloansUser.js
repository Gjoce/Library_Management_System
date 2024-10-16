async function fetchUserLoans() {
    // Retrieve the user ID from local storage
    const userId = localStorage.getItem('userId');

    if (userId) {
        try {
            // Fetch loans for the specific user
            const response = await fetch(`http://localhost:8080/api/loans/getSpecificUser.php?user_id=${userId}`);
            const loans = await response.json();
            console.log(loans);
            
            // Display user loans
            displayUserLoans(loans);

        } catch (error) {
            console.error('Error fetching user loans:', error);
        }
    } else {
        console.log("No user ID found in local storage.");
    }
}


// Function to display user loans in the table
function displayUserLoans(data) {
const loansTableBody = document.getElementById('loansTableBody');
loansTableBody.innerHTML = ''; // Clear previous entries

// Access the loans array from the data object
const loans = data.loans;

if (Array.isArray(loans) && loans.length > 0) { // Check if loans is an array and has items
loans.forEach(loan => {
    const loanRow = document.createElement('tr');
    loanRow.innerHTML = `
        <td>${loan.id}</td>
         <td>${loan.user_id}</td>
        <td>${loan.user_name || 'Unknown'}</td>  <!-- Display user name -->
        <td>${loan.book_title || 'N/A'}</td>
        <td>${loan.LOAN_DATE || 'N/A'}</td>
        <td>${loan.RETURN_DATE || 'Not Returned'}</td>
    
    `;
    loansTableBody.appendChild(loanRow);
});
} else {
loansTableBody.innerHTML = '<tr><td colspan="5">No loans found for this user.</td></tr>'; // Message when no loans are found
}
}







// Call the function on page load
document.addEventListener('DOMContentLoaded', () => {
    fetchUserLoans();
    checkAuthToken();
    
  });

  function logout() {
    // Remove the auth token from localStorage
    localStorage.removeItem('authToken');

    // Send a request to the server to handle logout, if necessary
    fetch('http://localhost:8080/api/users/logout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({})
    }).then(response => response.json())
    .then(data => {
        alert(data.message || 'Logged out successfully!');
        // Redirect to the login page or perform any other necessary action
        window.location.href = 'loginUser.html';
    })
    .catch(error => {
        console.error('Error during logout:', error);
    });
}

function checkAuthToken() {
    const token = localStorage.getItem('jwt');

    if (!token) {
        // If the token is missing, redirect to the login page
        window.location.href = 'loginUser.html';
        return false;
    }

    // Optionally verify the token (this could be a server call to validate the token)
    // Here we simply return true for now.
    return true;
}