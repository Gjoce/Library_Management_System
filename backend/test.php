<?php
include 'includes/connection.php';

// Fetch books
$stmt = $conn->query("SELECT * FROM books");
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Display books
echo "<h1>Books List</h1>";
foreach ($books as $book) {
    echo "ID: " . $book['id'] . "<br>";
    echo "Title: " . $book['TITLE'] . "<br>";
    echo "Author: " . $book['AUTHOR'] . "<br>";
    echo "Published Year: " . $book['PUBLISHED_YEAR'] . "<br>";
    echo "Genre: " . $book['GENRE'] . "<br>";
    echo "Available copies: " . $book['AVAILABLE_COPIES'] . "<br>";
    echo "Description: ". $book["DESCRIPTION"] . "<br>";
}
?>
