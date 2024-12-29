<?php
include '../../includes/functions.php';
require '../../vendor/autoload.php'; // Path to where Composer installed the JWT library

header("Access-Control-Allow-Origin: https://legendary-flan-1e69a2.netlify.app"); // Your frontend origin

// Specify which HTTP methods are allowed (GET, POST, etc.)
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

// Specify allowed headers (add headers as necessary)
header("Access-Control-Allow-Headers: Content-Type, Authorization");

use \Firebase\JWT\JWT;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inputData = json_decode(file_get_contents('php://input'), true);

    if (isset($inputData['email']) && isset($inputData['password'])) {
        $email = $inputData['email'];
        $password = $inputData['password'];

        $user = loginUser($email, $password); // Function to validate user credentials

        if ($user) {
            if ($user['ROLE'] === 'librarian') {
                // JWT token setup
                $secret_key = "LIBRARIANADMIN"; // Use a strong, secret key for librarian JWT
                $issuer_claim = "mydb"; // Your domain, or 'localhost' for development
                $audience_claim = "librarians";   // Audience identifier for librarians
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
                        "role" => $user['ROLE'] 
                    )
                );

                $jwt = JWT::encode($payload, $secret_key, 'HS256');

               
                echo json_encode([
                    "message" => "Login successful!",
                    "token" => $jwt,
                    "role" => "librarian" // Optionally send role back
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