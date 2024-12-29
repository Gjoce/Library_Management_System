<?php
include '../../includes/functions.php';

header("Access-Control-Allow-Origin: https://online-library-management-60dd26a214d9.herokuapp.com/"); // Your frontend origin

// Specify which HTTP methods are allowed (GET, POST, etc.)
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

header("Access-Control-Allow-Headers: Content-Type, Authorization");



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   
    $inputData = json_decode(file_get_contents('php://input'), true);

    
    if (isset($inputData['NAME']) && isset($inputData['EMAIL']) && isset($inputData['PASSWORD'])) {
        $name = $inputData['NAME']; 
        $email = $inputData['EMAIL'];
        $password = $inputData['PASSWORD'];

       
        $role = isset($inputData['ROLE']) && strtolower($inputData['ROLE']) === 'librarian' ? 'LIBRARIAN' : 'USER';

  
        if (addUser($name, $email, $password, $role)) {
            echo json_encode(["message" => "User added successfully!"]);
        } else {
            echo json_encode(["message" => "Failed to add user."]);
        }
    } else {
        echo json_encode(["message" => "Invalid input data."]);
    }
}
?>
