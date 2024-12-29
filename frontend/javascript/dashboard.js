let currentPage = 1;
const booksPerPage = 4;
let books = [];

document.addEventListener("DOMContentLoaded", () => {
  fetchBooks();
  checkAuthToken();

  document
    .getElementById("bookForm")
    .addEventListener("submit", handleFormSubmission);

  document.getElementById("searchInput").addEventListener("input", () => {
    currentPage = 1;
    fetchBooks();
  });

  document.getElementById("genreFilter").addEventListener("change", () => {
    currentPage = 1;
    fetchBooks();
  });
});

async function handleFormSubmission(event) {
  event.preventDefault();
  const bookId = document.getElementById("bookId").value;
  const bookData = {
    id: bookId,
    TITLE: document.getElementById("bookTitle").value,
    AUTHOR: document.getElementById("bookAuthor").value,
    PUBLISHED_YEAR: document.getElementById("publishedYear").value || null,
    GENRE: document.getElementById("bookGenre").value,
    AVAILABLE_COPIES: document.getElementById("availableCopies").value || 1,
    DESCRIPTION: document.getElementById("bookDescription").value,
  };

  const url = bookId
    ? `https://online-library-management-60dd26a214d9.herokuapp.com/api/books/edit.php`
    : "https://online-library-management-60dd26a214d9.herokuapp.com/api/books/add.php";
  const method = bookId ? "PUT" : "POST";

  try {
    const response = await fetch(url, {
      method: method,
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(bookData),
    });

    const data = await response.json();
    document.getElementById("formFeedback").textContent =
      data.message || data.error;
    document.getElementById("formFeedback").className = data.success
      ? "text-success"
      : "text-danger";

    document.getElementById("bookForm").reset();
    document.getElementById("bookId").value = "";
    fetchBooks();
  } catch (error) {
    console.error("Error adding/updating book:", error);
  }
}

async function fetchBooks() {
  const searchInput = document.getElementById("searchInput").value;
  const genreFilter = document.getElementById("genreFilter").value;

  let queryString = `?search=${encodeURIComponent(
    searchInput
  )}&genre=${encodeURIComponent(genreFilter)}`;

  try {
    const response = await fetch(
      `https://online-library-management-60dd26a214d9.herokuapp.com/api/books/fetch.php${queryString}`
    );
    books = await response.json();
    displayBooks();
    setupPagination();
  } catch (error) {
    console.error("Error fetching books:", error);
  }
}

function displayBooks() {
  const booksTableBody = document.getElementById("booksTableBody");
  booksTableBody.innerHTML = "";

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
                <button class="btn btn-secondary btn-sm" onclick="editBook(${
                  book.id
                })">Edit</button>
                <button class="btn btn-danger btn-sm" onclick="deleteBook(${
                  book.id
                })">Delete</button>
            </td>
        `;
    booksTableBody.appendChild(bookRow);
  });
}

function setupPagination() {
  const paginationContainer = document.getElementById("pagination");
  paginationContainer.innerHTML = "";

  const totalPages = Math.ceil(books.length / booksPerPage);

  if (currentPage > 1) {
    const prevButton = document.createElement("button");
    prevButton.textContent = "Previous";
    prevButton.onclick = () => changePage(currentPage - 1);
    paginationContainer.appendChild(prevButton);
  }

  for (let i = 1; i <= totalPages; i++) {
    const pageButton = document.createElement("button");
    pageButton.textContent = i;
    pageButton.className = i === currentPage ? "active" : "";
    pageButton.onclick = () => changePage(i);
    paginationContainer.appendChild(pageButton);
  }

  if (currentPage < totalPages) {
    const nextButton = document.createElement("button");
    nextButton.textContent = "Next";
    nextButton.onclick = () => changePage(currentPage + 1);
    paginationContainer.appendChild(nextButton);
  }
}

function changePage(page) {
  currentPage = page;
  displayBooks();
  setupPagination();
}

function editBook(bookId) {
  // Fetch the specific book details by ID
  fetch(
    `https://online-library-management-60dd26a214d9.herokuapp.com/api/books/fetch_single_book.php?id=${bookId}`
  )
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.json();
    })
    .then((book) => {
      console.log("Book fetched for editing:", book);
      if (!book.error) {
        document.getElementById("bookId").value = book.id || "";
        document.getElementById("bookTitle").value = book.TITLE || "";
        document.getElementById("bookAuthor").value = book.AUTHOR || "";
        document.getElementById("publishedYear").value =
          book.PUBLISHED_YEAR || "";
        document.getElementById("bookGenre").value = book.GENRE || "";
        document.getElementById("availableCopies").value =
          book.AVAILABLE_COPIES || "";
        document.getElementById("bookDescription").value =
          book.DESCRIPTION || "";
      } else {
        alert("Book not found!");
      }
    })
    .catch((error) => console.error("Error fetching book details:", error));
}

async function deleteBook(bookId) {
  if (confirm("Are you sure you want to delete this book?")) {
    try {
      const response = await fetch(
        `https://online-library-management-60dd26a214d9.herokuapp.com/api/books/delete.php`,
        {
          method: "DELETE", // Use DELETE method
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ id: bookId }),
        }
      );

      if (!response.ok) {
        const errorText = await response.text();
        console.error(`Error response: ${errorText}`);
        throw new Error(
          `HTTP error! status: ${response.status}, message: ${errorText}`
        );
      }

      const data = await response.json();
      alert(data.message || data.error);
      fetchBooks();
    } catch (error) {
      console.error("Error deleting book:", error);
    }
  }
}

function logout() {
  localStorage.removeItem("authToken");

  // Send a request to the server to handle logout, if necessary
  fetch(
    "https://online-library-management-60dd26a214d9.herokuapp.com/api/users/logout.php",
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

      window.location.href = "login.html";
    })
    .catch((error) => {
      console.error("Error during logout:", error);
    });
}

function checkAuthToken() {
  const token = localStorage.getItem("authToken");

  if (!token) {
    window.location.href = "login.html";
    return false;
  }

  return true;
}
