<?php
include '../../includes/connection.php';  
include '../../includes/functions.php';  

header("Access-Control-Allow-Origin: http://localhost:3000"); // Your frontend origin

// Specify which HTTP methods are allowed (GET, POST, etc.)
header("Access-Control-Allow-Methods: POST, GET, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') { // Change to DELETE method
    // Get the JSON data from the request body
    $inputData = json_decode(file_get_contents('php://input'), true);

   
    if (isset($inputData['id'])) {
        $id = $inputData['id'];

      
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
