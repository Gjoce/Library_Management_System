<?php
include '../../includes/connection.php';  // Ensure you have this connection file
include '../../includes/functions.php';  // Ensure you have this functions file

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') { // Change to DELETE method
    // Get the JSON data from the request body
    $inputData = json_decode(file_get_contents('php://input'), true);

    // Check if the ID is provided
    if (isset($inputData['id'])) {
        $id = $inputData['id'];

        // Call the deleteBook function
        if (deleteBook($id)) {
            echo json_encode(["message" => "Book deleted successfully!"]);
        } else {
            echo json_encode(["error" => "Failed to delete book."]);
        }
    } else {
        echo json_encode(["error" => "ID is required."]);
    }
} else {
    echo json_encode(["error" => "Only DELETE requests are allowed."]);
}
