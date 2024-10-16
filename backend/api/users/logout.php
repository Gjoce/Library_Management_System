<?php

header("Access-Control-Allow-Origin: http://localhost:3000"); // Your frontend origin

// Specify which HTTP methods are allowed (GET, POST, etc.)
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

header("Access-Control-Allow-Headers: Content-Type, Authorization");


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Perform any server-side token cleanup if necessary

    // Invalidate token on the client-side by responding with a success message
    echo json_encode(['message' => 'Logged out successfully.']);
} else {
    echo json_encode(['error' => 'Only POST requests are allowed.']);
}
