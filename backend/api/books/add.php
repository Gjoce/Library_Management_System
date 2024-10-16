<?php
include '../../includes/connection.php';
include '../../includes/functions.php';
header("Access-Control-Allow-Origin: http://localhost:3000"); // Your frontend origin

// Specify which HTTP methods are allowed (GET, POST, etc.)
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Read the JSON input from the request body
    $inputData = json_decode(file_get_contents('php://input'), true);

    // Check if the data is decoded successfully and all fields are present
    if (isset($inputData['TITLE'], $inputData['AUTHOR'], $inputData['PUBLISHED_YEAR'], $inputData['GENRE'], $inputData['AVAILABLE_COPIES'], $inputData['DESCRIPTION'])) {
        $title = $inputData['TITLE'];
        $author = $inputData['AUTHOR'];
        $published_year = $inputData['PUBLISHED_YEAR'];
        $genre = $inputData['GENRE'];
        $available_copies = $inputData['AVAILABLE_COPIES'];
        $description = $inputData['DESCRIPTION'];

        // Attempt to add the book
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
