<?php
include '../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $inputData = json_decode(file_get_contents('php://input'), true);
    
    if (isset($inputData['id'])) {
        $id = $inputData['id'];

        if (deleteUser($id)) {
            echo json_encode(["message" => "User deleted successfully!"]);
        } else {
            echo json_encode(["message" => "Failed to delete user."]);
        }
    } else {
        echo json_encode(["message" => "Invalid input. User ID is required."]);
    }
}
?>
