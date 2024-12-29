<?php
include '../../includes/functions.php';

header("Access-Control-Allow-Origin: https://legendary-flan-1e69a2.netlify.app"); // Your frontend origin

// Specify which HTTP methods are allowed (GET, POST, etc.)
header("Access-Control-Allow-Methods: POST, GET, DELETE, OPTIONS");

header("Access-Control-Allow-Headers: Content-Type, Authorization");



if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $inputData = json_decode(file_get_contents('php://input'), true);
    
    if (isset($inputData['id'])) {
        $id = $inputData['id'];

        if (deleteUser($id)) {
            echo json_encode(["message" => "User deleted successfully!"]);
        } else {
            echo json_encode(["message" => "Failed to delete user."]);
        }
    } else {
        echo json_encode(["message" => "Invalid input. User ID is required."]);
    }
}
?>
