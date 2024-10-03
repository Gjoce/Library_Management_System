<?php
include '../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the JSON data from the request body
    $inputData = json_decode(file_get_contents('php://input'), true);

    // Check if the required field (id) is present
    if (isset($inputData['id'])) {
        $id = $inputData['id'];

        // Call the editBook function with the book ID and the input data
        if (editBook($id, $inputData)) {
            echo json_encode(["message" => "Book updated successfully!"]);
        } else {
            echo json_encode(["error" => "Failed to update book."]);
        }
    } else {
        echo json_encode(["error" => "ID is required."]);
    }
} else {
    echo json_encode(["error" => "Only POST requests are allowed."]);
}
?>
