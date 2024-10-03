<?php
include '../../includes/functions.php';
require '../../vendor/autoload.php'; // Path to where Composer installed the JWT library

use \Firebase\JWT\JWT;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inputData = json_decode(file_get_contents('php://input'), true);

    if (isset($inputData['email']) && isset($inputData['password'])) {
        $email = $inputData['email'];
        $password = $inputData['password'];

        $user = loginUser($email, $password); // Assuming this function checks credentials and returns user info

        if ($user) {
            if ($user['ROLE'] === 'librarian') {
                // JWT token setup
                $secret_key = "YOUR_SECRET_KEY"; // Use a strong, secret key for JWT
                $issuer_claim = "localhost"; // Your domain, or 'localhost' for development
                $audience_claim = "users";   // Could be the same as issuer, or any audience identifier
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
                        "role" => $user['ROLE'] // Add any other fields you want in the token
                    )
                );

                // Encode the payload using the secret key to generate the JWT token
                $jwt = JWT::encode($payload, $secret_key, 'HS256');

                // Return the token and user data to the client
                echo json_encode([
                    "message" => "Login successful!",
                    "token" => $jwt,
                    // You may want to remove sensitive information like passwords
                ]);
            } else {
                echo json_encode(["message" => "Access denied. Only librarians can log in."]);
            }
        } else {
            echo json_encode(["message" => "Invalid email or password."]);
        }
    } else {
        echo json_encode(["message" => "Email and password are required."]);
    }
}
