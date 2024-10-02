<?php
include 'connection.php';

function addBook($title, $author, $published_year, $genre, $available_copies, $description) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO BOOKS (TITLE, AUTHOR, PUBLISHED_YEAR, GENRE, AVAILABLE_COPIES, description) VALUES (?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$title, $author, $published_year, $genre, $available_copies, $description]);
}

function fetchBooks() {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM BOOKS");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function editBook($id, $title, $author, $published_year, $genre, $available_copies, $description) {
    global $conn;
    $stmt = $conn->prepare("UPDATE BOOKS SET title=?, author=?, published_year=?, genre=?, available_copies=?, description=? WHERE id=?");
    return $stmt->execute([$title, $author, $published_year, $genre, $available_copies, $description, $id]);
}

function deleteBook($id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM BOOKS WHERE id=?");
    return $stmt->execute([$id]);
}
?>
