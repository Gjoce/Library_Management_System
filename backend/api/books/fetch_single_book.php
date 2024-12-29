<?php
include '../../includes/functions.php';

header("Access-Control-Allow-Origin: https://online-library-management-60dd26a214d9.herokuapp.com/"); // Your frontend origin

// Specify which HTTP methods are allowed (GET, POST, etc.)
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
   
    if (isset($_GET['id'])) {
        $bookId = $_GET['id'];

     
        $sql = "SELECT * FROM books WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $bookId]);

       
        $book = $stmt->fetch(PDO::FETCH_ASSOC);

       
        if ($book) {
           
            echo json_encode($book);
        } else {
           
            echo json_encode(['error' => 'Book not found.']);
        }
    } else {
        echo json_encode(['error' => 'Book ID not provided.']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}
?>
