<?php
include '../../includes/functions.php';

header("Access-Control-Allow-Origin: https://online-library-management-60dd26a214d9.herokuapp.com/"); // Your frontend origin

// Specify which HTTP methods are allowed (GET, POST, etc.)
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");



if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $users = fetchUsers();

    if ($users) {
        echo json_encode($users);  
    } else {
        echo json_encode(["message" => "No users found."]);
    }
}
?>
