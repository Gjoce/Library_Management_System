<?php
include '../../includes/functions.php';

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
