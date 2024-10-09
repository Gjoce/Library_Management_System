<?php
include '../../includes/functions.php';


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
