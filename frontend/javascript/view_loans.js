// Function to fetch and display user loans
async function fetchUserLoans() {
  const params = new URLSearchParams(window.location.search);
  const userId = params.get("user_id");

  if (userId) {
    try {
      const response = await fetch(
        `http://localhost:8080/Library_Management_System/backend/api/loans/getSpecificUser.php?user_id=${userId}`
      );
      const loans = await response.json();
      console.log(loans);

      displayUserLoans(loans);
    } catch (error) {
      console.error("Error fetching user loans:", error);
    }
  }
}

// Function to display user loans in the table
function displayUserLoans(data) {
  const loansTableBody = document.getElementById("loansTableBody");
  loansTableBody.innerHTML = ""; // Clear previous entries

  // Access the loans array from the data object
  const loans = data.loans;

  if (Array.isArray(loans) && loans.length > 0) {
    // Check if loans is an array and has items
    loans.forEach((loan) => {
      const loanRow = document.createElement("tr");
      loanRow.innerHTML = `
        <td>${loan.id}</td>
         <td>${loan.user_id}</td>
        <td>${loan.user_name || "Unknown"}</td>  <!-- Display user name -->
        <td>${loan.book_title || "N/A"}</td>
        <td>${loan.LOAN_DATE || "N/A"}</td>
        <td>${loan.RETURN_DATE || "Not Returned"}</td>
          <td>
                <button class="btn btn-danger btn-sm" onclick="removeLoan(${
                  loan.id
                })">Remove</button>
                <button class="btn btn-primary btn-sm" onclick="updateLoanReturnDate(${
                  loan.id
                })">Returned</button>
                
            </td>
    `;
      loansTableBody.appendChild(loanRow);
    });
  } else {
    loansTableBody.innerHTML =
      '<tr><td colspan="5">No loans found for this user.</td></tr>'; // Message when no loans are found
  }
}
async function removeLoan(loanId) {
  if (confirm("Are you sure you want to remove this loan?")) {
    try {
      const response = await fetch(
        `http://localhost:8080/Library_Management_System/backend/api/loans/delete.php`,
        {
          method: "DELETE",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ id: loanId }),
        }
      );
      const data = await response.json();
      alert(data.message || "Loan removed successfully.");
      fetchUserLoans(); // Refresh the loan list
    } catch (error) {
      console.error("Error removing loan:", error);
    }
  }
}

async function updateLoanReturnDate(loanId) {
  const currentDate = new Date().toISOString().split("T")[0]; // Get current date in YYYY-MM-DD format

  if (confirm("Are you sure you want to mark this loan as returned?")) {
    const requestData = { id: loanId, return_date: currentDate };
    console.log("Request data:", requestData); // Log the request data

    try {
      const response = await fetch(
        `http://localhost:8080/Library_Management_System/backend/api/loans/updateReturnDate.php`,
        {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(requestData),
        }
      );
      const data = await response.json();
      alert(data.message || "Return date updated successfully.");
      fetchUserLoans(); // Refresh the loan list
    } catch (error) {
      console.error("Error updating return date:", error);
    }
  }
}

// Call the function on page load
document.addEventListener("DOMContentLoaded", () => {
  fetchUserLoans();
  checkAuthToken();
});

function logout() {
  // Remove the auth token from localStorage
  localStorage.removeItem("authToken");

  // Send a request to the server to handle logout, if necessary
  fetch(
    "http://localhost:8080/Library_Management_System/backend/api/users/logout.php",
    {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({}),
    }
  )
    .then((response) => response.json())
    .then((data) => {
      alert(data.message || "Logged out successfully!");
      // Redirect to the login page or perform any other necessary action
      window.location.href = "login.html";
    })
    .catch((error) => {
      console.error("Error during logout:", error);
    });
}

function checkAuthToken() {
  const token = localStorage.getItem("authToken");

  if (!token) {
    // If the token is missing, redirect to the login page
    window.location.href = "login.html";
    return false;
  }

  // Optionally verify the token (this could be a server call to validate the token)
  // Here we simply return true for now.
  return true;
}
