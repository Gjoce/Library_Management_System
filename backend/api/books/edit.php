<?php
include '../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    // Get the input data from the request body
    $inputData = json_decode(file_get_contents('php://input'), true);

    // Check if all the required fields are present
    if (isset($inputData['id']) && isset($inputData['TITLE']) && isset($inputData['AUTHOR']) && isset($inputData['GENRE']) && isset($inputData['AVAILABLE_COPIES'])) {
        $bookId = $inputData['id'];
        
        // Prepare the data array to pass to the function
        $data = [
            'title' => $inputData['TITLE'],
            'author' => $inputData['AUTHOR'],
            'published_year' => $inputData['PUBLISHED_YEAR'] ?? null,
            'genre' => $inputData['GENRE'],
            'available_copies' => $inputData['AVAILABLE_COPIES'],
            'description' => $inputData['DESCRIPTION'] ?? ''
        ];

        // Call the editBook function to update the book
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
