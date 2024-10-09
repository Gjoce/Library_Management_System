document.addEventListener('DOMContentLoaded', () => {
  // Fetch loans on page load
   fetchLoans();
   checkAuthToken();

    document.getElementById('userFilter').addEventListener('input', () => {
        currentPage = 1; // Reset to the first page
        fetchLoans(); // Fetch books based on filters
    });

    document.getElementById('bookTitleFilter').addEventListener('change', () => {
        currentPage = 1; // Reset to the first page
        fetchLoans(); // Fetch books based on filters
    });
});

// Fetch loans with optional user filter
async function fetchLoans() {
    const userFilter = document.getElementById('userFilter').value || '';
    const bookTitleFilter = document.getElementById('bookTitleFilter').value || '';

    let queryString = `?user=${encodeURIComponent(userFilter)}&book_title=${encodeURIComponent(bookTitleFilter)}`;

    try {
        const response = await fetch(`http://localhost:8080/Library_Management_System/backend/api/loans/fetch.php${queryString}`);
        const loans = await response.json();
        displayLoans(loans);
    } catch (error) {
        console.error('Error fetching loans:', error);
    }
}

// Display loans in the table
function displayLoans(loans) {
    const loansTableBody = document.getElementById('loansTableBody');
    loansTableBody.innerHTML = ''; // Clear previous entries

    loans.forEach(loan => {
        const loanRow = document.createElement('tr');
        loanRow.innerHTML = `
            <td>${loan.loan_id}</td>
            <td>${loan.user_id}</td>
            <td><a href="user_loans.html?user_id=${loan.user_id}">${loan.user_name || 'Unknown'}</a></td>  <!-- Link to view user loans -->
            <td>${loan.book_title}</td>
            <td>${loan.LOAN_DATE || 'N/A'}</td>
            <td>${loan.RETURN_DATE || 'Not Returned'}</td>
            <td>
                <button class="btn btn-danger btn-sm" onclick="removeLoan(${loan.loan_id})">Remove</button>
                <button class="btn btn-primary btn-sm" onclick="updateLoanReturnDate(${loan.loan_id})">Returned</button>
            </td>
        `;
        loansTableBody.appendChild(loanRow);
    });
}

// Function to update the return date of a loan
async function updateLoanReturnDate(loanId) {
    const currentDate = new Date().toISOString().split('T')[0]; // Get current date in YYYY-MM-DD format

    if (confirm('Are you sure you want to mark this loan as returned?')) {
        const requestData = { id: loanId, return_date: currentDate };
        console.log('Request data:', requestData); // Log the request data

        try {
            const response = await fetch(`http://localhost:8080/Library_Management_System/backend/api/loans/updateReturnDate.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(requestData)
            });
            const data = await response.json();
            alert(data.message || 'Return date updated successfully.');
            fetchLoans(); // Refresh the loan list
        } catch (error) {
            console.error('Error updating return date:', error);
        }
    }
}






// Function to display all loans for a specific user
async function viewUserLoans(userId) {
    try {
        const response = await fetch(`http://localhost:8080/Library_Management_System/backend/api/loans/getSpecificUser.php?user_id=${encodeURIComponent(userId)}`);
        const loans = await response.json();
        
        // Display the loans for the specific user
        displayLoans(loans);
    } catch (error) {
        console.error('Error fetching user loans:', error);
    }
}


// Function to remove a loan
async function removeLoan(loanId) {
    if (confirm('Are you sure you want to remove this loan?')) {
        try {
            const response = await fetch(`http://localhost:8080/Library_Management_System/backend/api/loans/delete.php`, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: loanId })
            });
            const data = await response.json();
            alert(data.message || 'Loan removed successfully.');
            fetchLoans(); // Refresh the loan list
        } catch (error) {
            console.error('Error removing loan:', error);
        }
    }
}

function logout() {
    // Remove the auth token from localStorage
    localStorage.removeItem('authToken');

    // Send a request to the server to handle logout, if necessary
    fetch('http://localhost:8080/Library_Management_System/backend/api/users/logout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({})
    }).then(response => response.json())
    .then(data => {
        alert(data.message || 'Logged out successfully!');
        // Redirect to the login page or perform any other necessary action
        window.location.href = 'login.html';
    })
    .catch(error => {
        console.error('Error during logout:', error);
    });
}



function checkAuthToken() {
    const token = localStorage.getItem('authToken');

    if (!token) {
        // If the token is missing, redirect to the login page
        window.location.href = 'login.html';
        return false;
    }

    // Optionally verify the token (this could be a server call to validate the token)
    // Here we simply return true for now.
    return true;
}
