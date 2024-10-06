<?php
include '../../includes/functions.php';
require '../../vendor/autoload.php'; // Path to where Composer installed the JWT library

use \Firebase\JWT\JWT;

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
            // JWT token setup for users
            $secret_key = "USERADMIN"; // Use a strong, secret key for user JWT
            $issuer_claim = "localhost"; // Your domain, or 'localhost' for development
            $audience_claim = "users";   // Audience identifier for users
            $issued_at = time();         // Current timestamp
            $expiration_time = $issued_at + 3600; // Token valid for 1 hour (3600 seconds)

            // The payload that will be signed into the token
            $payload = array(
                "iss" => $issuer_claim,
                "aud" => $audience_claim,
                "iat" => $issued_at,
                "exp" => $expiration_time,
                "data" => array(
                    "id" => $user['id'],
                    "email" => $user['EMAIL'],
                    "role" => 'user' // Assign role as user
                )
            );

            // Encode the payload using the secret key to generate the JWT token
            $jwt = JWT::encode($payload, $secret_key, 'HS256');

            // Return the token and user data to the client
            echo json_encode([
                "message" => "Login successful!",
                "token" => $jwt,
                "user_id" => $user['id'] // Send back the user ID as well
            ]);
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
