<?php
include '../../includes/functions.php';

header("Access-Control-Allow-Origin: https://legendary-flan-1e69a2.netlify.app"); // Your frontend origin

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
