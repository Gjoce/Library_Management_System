<?php
include '../../includes/functions.php';

$books = fetchBooks();
echo json_encode($books);
?>
