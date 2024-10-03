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

        // Check if 'ROLE' is provided, default to 'USER' if not
        $role = isset($inputData['ROLE']) && strtolower($inputData['ROLE']) === 'librarian' ? 'LIBRARIAN' : 'USER';

        // Call the addUser function with the role
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
