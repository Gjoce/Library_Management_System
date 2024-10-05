<?php
include '../../includes/connection.php'; // Include your database connection
include '../../includes/functions.php'; // Include necessary functions

header('Content-Type: application/json');

// Get the filter parameters from the request
$userFilter = isset($_GET['user']) ? $_GET['user'] : '';
$bookTitleFilter = isset($_GET['book_title']) ? $_GET['book_title'] : '';


$loans = fetchLoans($userFilter, $bookTitleFilter); // Call function with filters

echo json_encode($loans);

