<?php
include '../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    
    $inputData = json_decode(file_get_contents('php://input'), true);

    if (isset($inputData['id']) && isset($inputData['TITLE']) && isset($inputData['AUTHOR']) && isset($inputData['GENRE']) && isset($inputData['AVAILABLE_COPIES'])) {
        $bookId = $inputData['id'];
        
     
        $data = [
            'title' => $inputData['TITLE'],
            'author' => $inputData['AUTHOR'],
            'published_year' => $inputData['PUBLISHED_YEAR'] ?? null,
            'genre' => $inputData['GENRE'],
            'available_copies' => $inputData['AVAILABLE_COPIES'],
            'description' => $inputData['DESCRIPTION'] ?? ''
        ];

       
        $result = editBook($bookId, $data);

        if ($result) {
            echo json_encode(['message' => 'Book updated successfully!']);
        } else {
            echo json_encode(['error' => 'Failed to update the book.']);
        }
    } else {
        echo json_encode(['error' => 'Invalid input data.']);
    }
} else {
    echo json_encode(['error' => 'Only PUT requests are allowed.']);
}


?>
