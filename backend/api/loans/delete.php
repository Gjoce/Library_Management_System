<?php
include '../../includes/connection.php';
include '../../includes/functions.php';

// Set the header to return JSON response
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: https://online-library-management-60dd26a214d9.herokuapp.com/"); // Your frontend origin

// Specify which HTTP methods are allowed (GET, POST, etc.)
header("Access-Control-Allow-Methods: POST, GET, DELETE, OPTIONS");

header("Access-Control-Allow-Headers: Content-Type, Authorization");



if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Get the raw DELETE data
    $json = file_get_contents('php://input');
    
    // Decode the JSON data into a PHP associative array
    $data = json_decode($json, true);

    // Check if required field is present
    if (isset($data['id'])) {
        $id = $data['id'];

        // Call the deleteLoan function and check if the loan is deleted successfully
        if (deleteLoan($id)) {
            echo json_encode(["success" => true, "message" => "Loan deleted successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to delete loan"]);
        }
    } else {
        // Missing required field
        echo json_encode(["success" => false, "message" => "Missing required field 'id'"]);
    }
} else {
    // Only DELETE requests are allowed
    echo json_encode(["success" => false, "message" => "Only DELETE requests are allowed"]);
}
?>
