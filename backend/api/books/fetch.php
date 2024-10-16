<?php
include '../../includes/functions.php';

header("Access-Control-Allow-Origin: http://localhost:3000"); // Your frontend origin

// Specify which HTTP methods are allowed (GET, POST, etc.)
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

// Get search and genre from query parameters (if provided)
$search = isset($_GET['search']) ? $_GET['search'] : '';
$genre = isset($_GET['genre']) ? $_GET['genre'] : '';


$books = fetchBooks($search, $genre);


echo json_encode($books);
?>
