<?php
include '../../includes/functions.php';
require '../../vendor/autoload.php'; // Path to where Composer installed the JWT library

use \Firebase\JWT\JWT;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inputData = json_decode(file_get_contents('php://input'), true);

    if (isset($inputData['email']) && isset($inputData['password'])) {
        $email = $inputData['email'];
        $password = $inputData['password'];

        $user = loginUser($email, $password); 

        if ($user) {
            if ($user['ROLE'] === 'librarian') {
               
                $secret_key = "YOUR_SECRET_KEY"; 
                $issuer_claim = "localhost"; 
                $audience_claim = "users";   
                $issued_at = time();        
                $expiration_time = $issued_at + 3600; 
              
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
