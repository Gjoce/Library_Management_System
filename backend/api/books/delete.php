<?php
include '../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];

    if (deleteBook($id)) {
        echo "Book deleted successfully!";
    } else {
        echo "Failed to delete book.";
    }
}
?>
