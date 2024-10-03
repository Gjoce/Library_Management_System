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


// Add a new user to the database
function addUser($name, $email, $password) {
    global $conn;

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL statement with PDO
    $sql = "INSERT INTO USERS (NAME, EMAIL, PASSWORD) VALUES (:name, :email, :password)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Bind the parameters (PDO style)
        return $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $hashed_password
        ]);
    }
    return false;
}


// Fetch all users from the database
function fetchUsers() {
    global $conn;

    // Prepare the SQL query to get all users
    $sql = "SELECT id, NAME, EMAIL, CREATED_AT FROM USERS";
    $stmt = $conn->prepare($sql);
    
    if ($stmt->execute()) {
        // Fetch all users as an associative array
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return false;
}


// Delete a user from the database
function deleteUser($id) {
    global $conn;

    // Prepare SQL query to delete user by ID
    $sql = "DELETE FROM USERS WHERE id = :id";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Bind the ID parameter and execute
        return $stmt->execute([':id' => $id]);
    }
    return false;
}

//Edit user

function editUser($id, $name, $email, $password) {
    global $conn;

    // Hash the new password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL query to update the user
    $sql = "UPDATE USERS SET NAME = :name, EMAIL = :email, PASSWORD = :password WHERE id = :id";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Bind the parameters and execute
        return $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $hashed_password,
            ':id' => $id
        ]);
    }
    return false;
}

//Login

function loginUser($email, $password) {
    global $conn;

    // Prepare SQL query to find the user by email
    $sql = "SELECT * FROM USERS WHERE EMAIL = :email";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute([':email' => $email])) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify the password
        if ($user && password_verify($password, $user['PASSWORD'])) {
            // Return user data excluding the password
            unset($user['PASSWORD']);
            return $user;
        }
    }
    return false;
}




?>
