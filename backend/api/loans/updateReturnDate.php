<?php
include '../../includes/connection.php'; // Include your database connection
header('Content-Type: application/json');

// Get the JSON input
$data = json_decode(file_get_contents("php://input"));

// Check if the necessary data is provided
if (isset($data->id) && isset($data->return_date)) {
    $loanId = $data->id;
    $returnDate = $data->return_date;

    // Prepare the SQL statement
    $query = "UPDATE LOANS SET RETURN_DATE = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    
    // Execute the statement
    if ($stmt->execute([$returnDate, $loanId])) {
        echo json_encode(['message' => 'Return date updated successfully.']);
    } else {
        echo json_encode(['message' => 'Failed to update return date.']);
    }
} else {
    echo json_encode(['message' => 'Invalid input.']);
}

