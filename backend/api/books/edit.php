<?php
include '../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    // Get the input data from the request body
    $inputData = json_decode(file_get_contents('php://input'), true);

    // Check if all the required fields are present
    if (isset($inputData['id']) && isset($inputData['TITLE']) && isset($inputData['AUTHOR']) && isset($inputData['GENRE']) && isset($inputData['AVAILABLE_COPIES'])) {
        $bookId = $inputData['id'];
        $title = $inputData['TITLE'];
        $author = $inputData['AUTHOR'];
        $publishedYear = isset($inputData['PUBLISHED_YEAR']) ? $inputData['PUBLISHED_YEAR'] : null;
        $genre = $inputData['GENRE'];
        $availableCopies = $inputData['AVAILABLE_COPIES'];
        $description = isset($inputData['DESCRIPTION']) ? $inputData['DESCRIPTION'] : '';

        // Update the book in the database
        $sql = "UPDATE books SET TITLE = :title, AUTHOR = :author, PUBLISHED_YEAR = :publishedYear, GENRE = :genre, AVAILABLE_COPIES = :availableCopies, DESCRIPTION = :description WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':title' => $title,
            ':author' => $author,
            ':publishedYear' => $publishedYear,
            ':genre' => $genre,
            ':availableCopies' => $availableCopies,
            ':description' => $description,
            ':id' => $bookId
        ]);

        if ($stmt->rowCount() > 0) {
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
