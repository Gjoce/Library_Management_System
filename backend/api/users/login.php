<?php
include '../../includes/functions.php';
require '../../vendor/autoload.php'; // Path to where Composer installed the JWT library

use \Firebase\JWT\JWT;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inputData = json_decode(file_get_contents('php://input'), true);

    if (isset($inputData['email']) && isset($inputData['password'])) {
        $email = $inputData['email'];
        $password = $inputData['password'];

<<<<<<< HEAD
        $user = loginUser($email, $password); 

        if ($user) {
            if ($user['ROLE'] === 'librarian') {
               
                $secret_key = "YOUR_SECRET_KEY"; 
                $issuer_claim = "localhost"; 
                $audience_claim = "users";   
                $issued_at = time();        
                $expiration_time = $issued_at + 3600; 
              
=======
        $user = loginUser($email, $password); // Function to validate user credentials

        if ($user) {
            if ($user['ROLE'] === 'librarian') {
                // JWT token setup
                $secret_key = "LIBRARIANADMIN"; // Use a strong, secret key for librarian JWT
                $issuer_claim = "localhost"; // Your domain, or 'localhost' for development
                $audience_claim = "librarians";   // Audience identifier for librarians
                $issued_at = time();         // Current timestamp
                $expiration_time = $issued_at + 3600; // Token valid for 1 hour (3600 seconds)
                
                // The payload that will be signed into the token
>>>>>>> 735a5f1359d4eea540c87b33d5bc209a5c795436
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
<<<<<<< HEAD
                   
=======
                    "role" => "librarian" // Optionally send role back
>>>>>>> 735a5f1359d4eea540c87b33d5bc209a5c795436
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