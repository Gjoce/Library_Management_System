<?php
include '../../includes/connection.php';
include '../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   
    $inputData = json_decode(file_get_contents('php://input'), true);

   
    if (isset($inputData['TITLE'], $inputData['AUTHOR'], $inputData['PUBLISHED_YEAR'], $inputData['GENRE'], $inputData['AVAILABLE_COPIES'], $inputData['DESCRIPTION'])) {
        $title = $inputData['TITLE'];
        $author = $inputData['AUTHOR'];
        $published_year = $inputData['PUBLISHED_YEAR'];
        $genre = $inputData['GENRE'];
        $available_copies = $inputData['AVAILABLE_COPIES'];
        $description = $inputData['DESCRIPTION'];

    
        if (addBook($title, $author, $published_year, $genre, $available_copies, $description)) {
            echo json_encode(["message" => "Book added successfully!"]);
        } else {
            echo json_encode(["error" => "Failed to add book."]);
        }
    } else {
        echo json_encode(["error" => "All fields are required."]);
    }
}
?>
