<?php
include '../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inputData = json_decode(file_get_contents('php://input'), true);

    if (isset($inputData['email']) && isset($inputData['password'])) {
        $email = $inputData['email'];
        $password = $inputData['password'];

        $user = loginUser($email, $password);

        if ($user) {
            echo json_encode([
                "message" => "Login successful!",
                "user" => $user // You can return user data (excluding password)
            ]);
        } else {
            echo json_encode(["message" => "Invalid email or password."]);
        }
    } else {
        echo json_encode(["message" => "Email and password are required."]);
    }
}
?>
