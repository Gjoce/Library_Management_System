<?php
include '../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Decode the JSON input
    $inputData = json_decode(file_get_contents('php://input'), true);

    // Check if the required fields are present
    if (isset($inputData['EMAIL']) && isset($inputData['PASSWORD'])) {
        $email = $inputData['EMAIL'];
        $password = $inputData['PASSWORD'];

        // Call the loginUser function
        $user = loginUser1($email, $password);

        if ($user) {
            // Successful login
            // Add user ID to the response
            echo json_encode(["message" => "Login successful!", "user_id" => $user['id']]);
        } else {
            // Invalid credentials
            echo json_encode(["message" => "Invalid email or password."]);
        }
    } else {
        echo json_encode(["message" => "Invalid input data."]);
    }
}

// Function to login user
function loginUser1($email, $password) {
    global $conn;

    // Prepare SQL query to find the user by email
    $sql = "SELECT * FROM USERS WHERE EMAIL = :email";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute([':email' => $email])) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify the password
        if ($user && password_verify($password, $user['PASSWORD'])) {
            // Return user data excluding the password
            unset($user['PASSWORD']); // Remove password from user data
            return $user;
        }
    }
    return false; // Return false if user not found or password mismatch
}
?>
