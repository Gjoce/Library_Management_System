document.addEventListener('DOMContentLoaded', () => {
  // Fetch loans on page load
   fetchLoans();

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
            <td>${loan.id}</td>
            <td>${loan.user_name || 'Unknown'}</a></td>  <!-- Link to view user loans -->
            <td>${loan.book_title}</td>
            <td>${loan.LOAN_DATE || 'N/A'}</td>
            <td>${loan.RETURN_DATE || 'Not Returned'}</td>
            <td>
                <button class="btn btn-danger btn-sm" onclick="removeLoan(${loan.id})">Remove</button>
                <button class="btn btn-primary btn-sm" onclick="updateLoan(${loan.id})">Update</button>
            </td>
        `;
        loansTableBody.appendChild(loanRow);
    });
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
