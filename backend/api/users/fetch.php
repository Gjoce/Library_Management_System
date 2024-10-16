<?php
include '../../includes/functions.php';

header("Access-Control-Allow-Origin: http://localhost:3000"); // Your frontend origin

// Specify which HTTP methods are allowed (GET, POST, etc.)
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");



if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $users = fetchUsers();

    if ($users) {
        echo json_encode($users);  // Return the users data in JSON format
    } else {
        echo json_encode(["message" => "No users found."]);
    }
}
?>
