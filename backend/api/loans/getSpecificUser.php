<?php
include '../../includes/connection.php';
include '../../includes/functions.php';

// Set the header to return JSON response
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['user_id'])) {
        $user_id = $_GET['user_id'];

        // Call the function to get all loans for the specific user
        $loans = getUserLoans($user_id);

        if ($loans) {
            echo json_encode(["success" => true, "loans" => $loans]);
        } else {
            echo json_encode(["success" => false, "message" => "No loans found for this user"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Missing 'user_id' parameter"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Only GET requests are allowed"]);
}
?>
