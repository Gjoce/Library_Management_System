let currentPage = 1; // Current page number
const booksPerPage = 4; // Number of books to show per page
let books = []; // Array to hold all fetched books

document.addEventListener('DOMContentLoaded', () => {
    // Fetch and display all books when the page loads
    fetchBooks();
    checkAuthToken();

    // Event listener for the book form submission
    document.getElementById('bookForm').addEventListener('submit', async (event) => {
        event.preventDefault();

        const bookId = document.getElementById('bookId').value;
        const bookData = {
            id: bookId, // Include ID in the payload for editing
            TITLE: document.getElementById('bookTitle').value,
            AUTHOR: document.getElementById('bookAuthor').value,
            PUBLISHED_YEAR: document.getElementById('publishedYear').value || null,
            GENRE: document.getElementById('bookGenre').value,
            AVAILABLE_COPIES: document.getElementById('availableCopies').value || 1,
            DESCRIPTION: document.getElementById('bookDescription').value
        };

        const url = bookId ? `http://localhost:8080/Library_Management_System/backend/api/books/edit.php` : 'http://localhost:8080/Library_Management_System/backend/api/books/add.php';
        const method = bookId ? 'PUT' : 'POST';

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(bookData)
            });

            const data = await response.json();
            document.getElementById('formFeedback').textContent = data.message || data.error;
            document.getElementById('formFeedback').className = data.success ? 'text-success' : 'text-danger';

            // Reset form
            document.getElementById('bookForm').reset();
            document.getElementById('bookId').value = '';
            fetchBooks(); // Refresh the book list
        } catch (error) {
            console.error('Error adding/updating book:', error);
        }
    });
});

// Function to fetch and display all books
async function fetchBooks() {
    try {
        const response = await fetch('http://localhost:8080/Library_Management_System/backend/api/books/fetch.php');
        books = await response.json();
        displayBooks();
        setupPagination();
    } catch (error) {
        console.error('Error fetching books:', error);
    }
}

// Function to display books in the table based on the current page
function displayBooks() {
    const booksTableBody = document.getElementById('booksTableBody');
    booksTableBody.innerHTML = ''; // Clear previous entries

    const startIndex = (currentPage - 1) * booksPerPage;
    const endIndex = startIndex + booksPerPage;
    const booksToDisplay = books.slice(startIndex, endIndex);

    booksToDisplay.forEach(book => {
        const bookRow = document.createElement('tr');
        bookRow.innerHTML = `
            <td>${book.id}</td>
            <td>${book.TITLE}</td>
            <td>${book.AUTHOR}</td>
            <td>${book.PUBLISHED_YEAR || 'N/A'}</td>
            <td>${book.GENRE}</td>
            <td>${book.AVAILABLE_COPIES}</td>
            <td>${book.DESCRIPTION}</td>
            <td>
                <button class="btn btn-warning btn-sm" onclick="editBook(${book.id})">Edit</button>
                <button class="btn btn-danger btn-sm" onclick="deleteBook(${book.id})">Delete</button>
            </td>
        `;
        booksTableBody.appendChild(bookRow);
    });
}

// Function to setup pagination controls
function setupPagination() {
    const paginationContainer = document.getElementById('pagination');
    paginationContainer.innerHTML = ''; // Clear previous pagination

    const totalPages = Math.ceil(books.length / booksPerPage);

    // Create previous button
    if (currentPage > 1) {
        const prevButton = document.createElement('button');
        prevButton.textContent = 'Previous';
        prevButton.onclick = () => changePage(currentPage - 1);
        paginationContainer.appendChild(prevButton);
    }

    // Create page number buttons
    for (let i = 1; i <= totalPages; i++) {
        const pageButton = document.createElement('button');
        pageButton.textContent = i;
        pageButton.className = (i === currentPage) ? 'active' : ''; // Highlight the active page
        pageButton.onclick = () => changePage(i);
        paginationContainer.appendChild(pageButton);
    }

    // Create next button
    if (currentPage < totalPages) {
        const nextButton = document.createElement('button');
        nextButton.textContent = 'Next';
        nextButton.onclick = () => changePage(currentPage + 1);
        paginationContainer.appendChild(nextButton);
    }
}

// Function to change the current page and refresh the display
function changePage(page) {
    currentPage = page;
    displayBooks();
    setupPagination();
}

// Function to edit a book
function editBook(bookId) {
    // Use fetch to get book details to edit
    fetch(`http://localhost:8080/Library_Management_System/backend/api/books/fetch.php?id=${bookId}`)
        .then(response => response.json())
        .then(books => {
            console.log(books); // Log the array to ensure it contains the expected data

            if (books && books.length > 0) {
                // Since books is an array, access the first element
                const book = books[0];

                // Populate the form with book details
                document.getElementById('bookId').value = book.id || '';
                document.getElementById('bookTitle').value = book.TITLE || '';
                document.getElementById('bookAuthor').value = book.AUTHOR || '';
                document.getElementById('publishedYear').value = book.PUBLISHED_YEAR || '';
                document.getElementById('bookGenre').value = book.GENRE || '';
                document.getElementById('availableCopies').value = book.AVAILABLE_COPIES || '';
                document.getElementById('bookDescription').value = book.DESCRIPTION || '';
            } else {
                alert('Book not found!');
            }
        })
        .catch(error => console.error('Error fetching book details:', error));
}

// Function to delete a book
async function deleteBook(bookId) {
    if (confirm('Are you sure you want to delete this book?')) {
        try {
            const response = await fetch(`http://localhost:8080/Library_Management_System/backend/api/books/delete.php`, {
                method: 'DELETE', // Use DELETE method
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: bookId }) // Send ID as JSON
            });

            // Check if the response is ok
            if (!response.ok) {
                const errorText = await response.text(); // Get the raw response text
                console.error(`Error response: ${errorText}`); // Log the error
                throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
            }

            const data = await response.json();
            alert(data.message || data.error);
            fetchBooks(); // Refresh the book list
        } catch (error) {
            console.error('Error deleting book:', error);
        }
    }
}

// Function to handle user logout
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

// Add a button or link that triggers the logout function
document.getElementById('logoutButton').addEventListener('click', logout);

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

$(document).ready(function() {
    // Event handler for search input
    $('#searchInput').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        const filteredBooks = books.filter(book => 
            book.TITLE.toLowerCase().includes(searchTerm) || 
            book.AUTHOR.toLowerCase().includes(searchTerm)
        );
        displayFilteredBooks(filteredBooks);
    });

    // Event handler for genre filter
    $('#genreFilter').on('change', function() { // Change to '#genreFilter'
        const selectedGenre = $(this).val();
        const filteredBooks = selectedGenre === '' ? books : books.filter(book => book.GENRE === selectedGenre);
        displayFilteredBooks(filteredBooks);
    });
});

// Function to display filtered books
function displayFilteredBooks(filteredBooks) {
    // Reset the global books array with filtered results
    books = filteredBooks; 
    currentPage = 1; // Reset to the first page
    displayBooks(); // Display filtered books
    setupPagination(); // Setup pagination for filtered results
}

