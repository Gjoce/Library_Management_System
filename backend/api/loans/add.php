<?php
include '../../includes/connection.php';
include '../../includes/functions.php';

header("Access-Control-Allow-Origin: http://localhost:3000"); // Your frontend origin

// Specify which HTTP methods are allowed (GET, POST, etc.)
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

header("Access-Control-Allow-Headers: Content-Type, Authorization");


header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputData = json_decode(file_get_contents('php://input'), true);

    if (isset($inputData['user_id'], $inputData['book_id'], $inputData['loan_date'])) {
        $user_id = $inputData['user_id'];
        $book_id = $inputData['book_id'];
        $loan_date = $inputData['loan_date'];
        $return_date = isset($inputData['return_date']) ? $inputData['return_date'] : null;

        if (addLoan($user_id, $book_id, $loan_date, $return_date)) {
            echo json_encode(["success" => true, "message" => "Loan added successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to add loan. No available copies left."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Missing required fields"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Only POST requests are allowed"]);
}
?>
