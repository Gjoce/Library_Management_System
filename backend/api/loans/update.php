<?php
include '../../includes/connection.php';
include '../../includes/functions.php';

// Set the header to return JSON response
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:3000"); // Your frontend origin

// Specify which HTTP methods are allowed (GET, POST, etc.)
header("Access-Control-Allow-Methods: POST, GET, PUT, OPTIONS");

header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Get the raw PUT data
    $json = file_get_contents('php://input');
    
    // Decode the JSON data into a PHP associative array
    $data = json_decode($json, true);

    // Check if required fields are present
    if (isset($data['id'], $data['return_date'])) {
        $id = $data['id'];
        $return_date = $data['return_date'];

        // Call the updateLoan function and check if the loan is updated successfully
        if (updateLoanReturnDate($id, $return_date)) {
            echo json_encode(["success" => true, "message" => "Loan updated successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to update loan"]);
        }
    } else {
        // Missing required fields in the JSON payload
        echo json_encode(["success" => false, "message" => "Missing required fields"]);
    }
} else {
    // Only PUT requests are allowed
    echo json_encode(["success" => false, "message" => "Only PUT requests are allowed"]);
}
?>
