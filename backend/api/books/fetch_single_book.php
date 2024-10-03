<?php
include '../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Check if the 'id' query parameter is provided
    if (isset($_GET['id'])) {
        $bookId = $_GET['id'];

        // Prepare SQL to fetch the specific book by ID
        $sql = "SELECT * FROM books WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $bookId]);

        // Fetch the book details
        $book = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the book exists
        if ($book) {
            // Return the book details as JSON
            echo json_encode($book);
        } else {
            // If no book is found, return an empty object or error message
            echo json_encode(['error' => 'Book not found.']);
        }
    } else {
        echo json_encode(['error' => 'Book ID not provided.']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}
?>
