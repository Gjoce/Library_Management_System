<?php
include '../../includes/functions.php';

// Get search and genre from query parameters (if provided)
$search = isset($_GET['search']) ? $_GET['search'] : '';
$genre = isset($_GET['genre']) ? $_GET['genre'] : '';

// Fetch books with optional search and genre filters
$books = fetchBooks($search, $genre);

// Return the result as a JSON response
echo json_encode($books);
?>
