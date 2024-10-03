document.addEventListener('DOMContentLoaded', () => {
    // Fetch and display all books when the page loads
    fetchBooks();

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
        const books = await response.json();
        displayBooks(books);
    } catch (error) {
        console.error('Error fetching books:', error);
    }
}

// Function to display books in the table
function displayBooks(books) {
    const booksTableBody = document.getElementById('booksTableBody');
    booksTableBody.innerHTML = ''; // Clear previous entries

    if (books.length === 0) {
        booksTableBody.innerHTML = '<tr><td colspan="8" class="text-center">No books found.</td></tr>';
        return;
    }

    books.forEach(book => {
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

// Function to edit a book
function editBook(bookId) {
    // Use fetch to get book details to edit
    fetch(`http://localhost:8080/Library_Management_System/backend/api/books/fetch.php?id=${bookId}`)
        .then(response => response.json())
        .then(book => {
            if (book) {
                document.getElementById('bookId').value = book.id;
                document.getElementById('bookTitle').value = book.TITLE;
                document.getElementById('bookAuthor').value = book.AUTHOR;
                document.getElementById('publishedYear').value = book.PUBLISHED_YEAR || '';
                document.getElementById('bookGenre').value = book.GENRE;
                document.getElementById('availableCopies').value = book.AVAILABLE_COPIES;
                document.getElementById('bookDescription').value = book.DESCRIPTION;
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


