<?php
include '../../includes/connection.php'; // Include your database connection
include '../../includes/functions.php'; // Include necessary functions

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:3000"); // Your frontend origin

// Specify which HTTP methods are allowed (GET, POST, etc.)
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");



// Get the filter parameters from the request
$userFilter = isset($_GET['user']) ? $_GET['user'] : '';
$bookTitleFilter = isset($_GET['book_title']) ? $_GET['book_title'] : '';


$loans = fetchLoans($userFilter, $bookTitleFilter); // Call function with filters

echo json_encode($loans);

