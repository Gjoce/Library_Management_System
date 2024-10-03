<?php
include '../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Decode the JSON input
    $inputData = json_decode(file_get_contents('php://input'), true);

    // Check if the required fields are present
    if (isset($inputData['NAME']) && isset($inputData['EMAIL']) && isset($inputData['PASSWORD'])) {
        $name = $inputData['NAME']; // Use uppercase to match the JSON keys
        $email = $inputData['EMAIL'];
        $password = $inputData['PASSWORD'];

        // Call the addUser function
        if (addUser($name, $email, $password)) {
            echo json_encode(["message" => "User added successfully!"]);
        } else {
            echo json_encode(["message" => "Failed to add user."]);
        }
    } else {
        echo json_encode(["message" => "Invalid input data."]);
    }
}
?>
