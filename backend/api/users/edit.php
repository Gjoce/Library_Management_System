<?php
include '../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $inputData = json_decode(file_get_contents('php://input'), true);

    if (isset($inputData['id']) && isset($inputData['name']) && isset($inputData['email']) && isset($inputData['password'])) {
        $id = $inputData['id'];
        $name = $inputData['name'];
        $email = $inputData['email'];
        $password = $inputData['password'];

        if (editUser($id, $name, $email, $password)) {
            echo json_encode(["message" => "User updated successfully!"]);
        } else {
            echo json_encode(["message" => "Failed to update user."]);
        }
    } else {
        echo json_encode(["message" => "Invalid input. All fields are required."]);
    }
}
?>
