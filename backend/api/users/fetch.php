<?php
include '../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $users = fetchUsers();

    if ($users) {
        echo json_encode($users);  // Return the users data in JSON format
    } else {
        echo json_encode(["message" => "No users found."]);
    }
}
?>