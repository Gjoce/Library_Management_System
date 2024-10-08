<?php
include '../../includes/connection.php';  
include '../../includes/functions.php';  

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') { 
    $inputData = json_decode(file_get_contents('php://input'), true);

   
    if (isset($inputData['id'])) {
        $id = $inputData['id'];

      
        if (deleteBook($id)) {
            echo json_encode(["message" => "Book deleted successfully!"]);
        } else {
            echo json_encode(["error" => "Failed to delete book."]);
        }
    } else {
        echo json_encode(["error" => "ID is required."]);
    }
} else {
    echo json_encode(["error" => "Only DELETE requests are allowed."]);
}
