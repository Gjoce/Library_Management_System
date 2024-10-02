<?php
include '../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the JSON data from the request body
    $inputData = json_decode(file_get_contents('php://input'), true);

    // Check if the required fields are present
    if (isset($inputData['id'], $inputData['title'], $inputData['author'], 
              $inputData['published_year'], $inputData['genre'], 
              $inputData['available_copies'], $inputData['description'])) {
        
        $id = $inputData['id'];
        $title = $inputData['title'];
        $author = $inputData['author'];
        $published_year = $inputData['published_year'];
        $genre = $inputData['genre'];
        $available_copies = $inputData['available_copies'];
        $description = $inputData['description'];

        if (editBook($id, $title, $author, $published_year, $genre, $available_copies, $description)) {
            echo json_encode(["message" => "Book updated successfully!"]);
        } else {
            echo json_encode(["error" => "Failed to update book."]);
        }
    } else {
        echo json_encode(["error" => "All fields are required."]);
    }
}
?>
