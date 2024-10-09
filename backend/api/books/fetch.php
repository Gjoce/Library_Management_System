<?php
include '../../includes/functions.php';


$search = isset($_GET['search']) ? $_GET['search'] : '';
$genre = isset($_GET['genre']) ? $_GET['genre'] : '';


$books = fetchBooks($search, $genre);


echo json_encode($books);
?>
