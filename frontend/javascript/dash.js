let currentPage = 1; // Current page number
const booksPerPage = 10; // Number of books to show per page
let books = []; // Array to hold all fetched books

document.addEventListener("DOMContentLoaded", () => {
  fetchBooks(); // Fetch and display books when the page loads
  checkAuthToken();

  // Event listeners for search input and genre filter
  document.getElementById("searchInput").addEventListener("input", () => {
    currentPage = 1; // Reset to the first page
    fetchBooks(); // Fetch books based on filters
  });

  document.getElementById("genreFilter").addEventListener("change", () => {
    currentPage = 1; // Reset to the first page
    fetchBooks(); // Fetch books based on filters
  });
});

// Fetch books with optional search and genre filters
async function fetchBooks() {
  const searchInput = document.getElementById("searchInput").value;
  const genreFilter = document.getElementById("genreFilter").value;

  let queryString = `?search=${encodeURIComponent(
    searchInput
  )}&genre=${encodeURIComponent(genreFilter)}`;

  try {
    const response = await fetch(
      `http://localhost:8080/Library_Management_System/backend/api/books/fetch.php${queryString}`
    );
    books = await response.json();
    displayBooks();
    setupPagination();
  } catch (error) {
    console.error("Error fetching books:", error);
  }
}

// Display books in the table based on the current page
function displayBooks() {
  const booksTableBody = document.getElementById("booksTableBody");
  booksTableBody.innerHTML = ""; // Clear previous entries

  const startIndex = (currentPage - 1) * booksPerPage;
  const endIndex = startIndex + booksPerPage;
  const booksToDisplay = books.slice(startIndex, endIndex);

  booksToDisplay.forEach((book) => {
    const bookRow = document.createElement("tr");
    bookRow.innerHTML = `
            <td>${book.id}</td>
            <td>${book.TITLE}</td>
            <td>${book.AUTHOR || "Unknown"}</td>
            <td>${book.PUBLISHED_YEAR || "N/A"}</td>
            <td>${book.GENRE}</td>
            <td>${book.AVAILABLE_COPIES}</td>
            <td>${book.DESCRIPTION}</td>
            <td>
                <button class="btn btn-primary btn-sm" onclick="loanBook(${
                  book.id
                })">Loan</button>
            </td>
        `;
    booksTableBody.appendChild(bookRow);
  });
}

// Loan a specific book
async function loanBook(bookId) {
  // Confirm with the user before proceeding
  const isConfirmed = confirm("Are you sure you want to loan this book?");

  if (isConfirmed) {
    // Retrieve user ID from localStorage
    const userId = localStorage.getItem("userId");
    const loanDate = new Date().toISOString().slice(0, 10); // Current date in YYYY-MM-DD format

    try {
      const response = await fetch(
        `http://localhost:8080/Library_Management_System/backend/api/loans/add.php`,
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            user_id: userId, // Include the user ID
            book_id: bookId, // Include the book ID
            loan_date: loanDate, // Include the loan date
          }),
        }
      );

      const data = await response.json();
      alert(data.message || data.error);
      fetchBooks(); // Refresh the book list after loaning
    } catch (error) {
      console.error("Error loaning book:", error);
    }
  }
}

// Setup pagination controls
function setupPagination() {
  const paginationContainer = document.getElementById("pagination");
  paginationContainer.innerHTML = ""; // Clear previous pagination

  const totalPages = Math.ceil(books.length / booksPerPage);

  // Create previous button
  if (currentPage > 1) {
    const prevButton = document.createElement("button");
    prevButton.textContent = "Previous";
    prevButton.onclick = () => changePage(currentPage - 1);
    paginationContainer.appendChild(prevButton);
  }

  // Create page number buttons
  for (let i = 1; i <= totalPages; i++) {
    const pageButton = document.createElement("button");
    pageButton.textContent = i;
    pageButton.className = i === currentPage ? "active" : "";
    pageButton.onclick = () => changePage(i);
    paginationContainer.appendChild(pageButton);
  }

  // Create next button
  if (currentPage < totalPages) {
    const nextButton = document.createElement("button");
    nextButton.textContent = "Next";
    nextButton.onclick = () => changePage(currentPage + 1);
    paginationContainer.appendChild(nextButton);
  }
}

// Change the current page and refresh the display
function changePage(page) {
  currentPage = page;
  displayBooks();
  setupPagination();
}

// Function to handle user logout
function logout() {
  localStorage.removeItem("jwt");
  localStorage.removeItem("userId");
  window.location.href = "index.html";
}

// Function to check for authentication token
function checkAuthToken() {
  const token = localStorage.getItem("jwt");

  if (!token) {
    // If the token is missing, redirect to the login page
    window.location.href = "loginUser.html";
    return false;
  }

  // Optionally verify the token (this could be a server call to validate the token)
  // Here we simply return true for now.
  return true;
}
