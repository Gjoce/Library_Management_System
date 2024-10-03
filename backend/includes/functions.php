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

// Function to edit a book with optional fields
function editBook($id, $data) {
    global $conn;

    // Prepare the base query
    $query = "UPDATE BOOKS SET ";
    $params = [];
    
    // Check which fields are present and build the query accordingly
    if (isset($data['title'])) {
        $query .= "TITLE = :title, ";
        $params[':title'] = $data['title'];
    }
    if (isset($data['author'])) {
        $query .= "AUTHOR = :author, ";
        $params[':author'] = $data['author'];
    }
    if (isset($data['published_year'])) {
        $query .= "PUBLISHED_YEAR = :published_year, ";
        $params[':published_year'] = $data['published_year'];
    }
    if (isset($data['genre'])) {
        $query .= "GENRE = :genre, ";
        $params[':genre'] = $data['genre'];
    }
    if (isset($data['available_copies'])) {
        $query .= "AVAILABLE_COPIES = :available_copies, ";
        $params[':available_copies'] = $data['available_copies'];
    }
    if (isset($data['description'])) {
        $query .= "DESCRIPTION = :description, ";
        $params[':description'] = $data['description'];
    }

    // Remove the trailing comma and space
    $query = rtrim($query, ', ');

    // Add the WHERE clause
    $query .= " WHERE id = :id";
    $params[':id'] = $id;

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare($query);
    return $stmt->execute($params);
}


function deleteBook($id) {
    global $conn;

    // First, delete any loans associated with this book
    $deleteLoansQuery = "DELETE FROM loans WHERE BOOK_ID = :book_id";
    $stmt = $conn->prepare($deleteLoansQuery);
    $stmt->execute([':book_id' => $id]);

    // Then, delete the book
    $deleteBookQuery = "DELETE FROM books WHERE id = :id";
    $stmt = $conn->prepare($deleteBookQuery);
    return $stmt->execute([':id' => $id]);
}



// Add a new user to the database
function addUser($name, $email, $password, $role = 'USER') {
    global $conn;

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL statement with PDO to include the role
    $sql = "INSERT INTO USERS (NAME, EMAIL, PASSWORD, ROLE) VALUES (:name, :email, :password, :role)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Bind the parameters (PDO style)
        return $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $hashed_password,
            ':role' => $role // Bind the role parameter
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

//LOANS

// Function to add a loan and decrease available copies of a book
function addLoan($user_id, $book_id, $loan_date, $return_date = null) {
    global $conn;

    // Start a transaction
    $conn->beginTransaction();

    try {
        // Check if there are available copies for the book
        $sql = "SELECT AVAILABLE_COPIES FROM BOOKS WHERE id = :book_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':book_id' => $book_id]);
        $book = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($book && $book['AVAILABLE_COPIES'] > 0) {
            // Add the loan to the LOANS table
            $sql = "INSERT INTO LOANS (USER_ID, BOOK_ID, LOAN_DATE, RETURN_DATE) 
                    VALUES (:user_id, :book_id, :loan_date, :return_date)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':user_id' => $user_id,
                ':book_id' => $book_id,
                ':loan_date' => $loan_date,
                ':return_date' => $return_date
            ]);

            // Decrease the available copies by 1
            $sql = "UPDATE BOOKS SET AVAILABLE_COPIES = AVAILABLE_COPIES - 1 WHERE id = :book_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':book_id' => $book_id]);

            // Commit the transaction
            $conn->commit();

            return true; // Loan successfully added
        } else {
            // If no copies are available, roll back the transaction and return false
            $conn->rollBack();
            return false;
        }

    } catch (Exception $e) {
        // Roll back the transaction in case of an error
        $conn->rollBack();
        return false;
    }
}


function fetchLoans() {
    global $conn;

    $stmt = $conn->prepare("SELECT LOANS.id, USERS.NAME as user_name, BOOKS.TITLE as book_title, LOANS.LOAN_DATE, LOANS.RETURN_DATE 
                            FROM LOANS 
                            JOIN USERS ON LOANS.USER_ID = USERS.id
                            JOIN BOOKS ON LOANS.BOOK_ID = BOOKS.id");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updateLoanReturnDate($loan_id, $return_date) {
    global $conn;

    $stmt = $conn->prepare("UPDATE LOANS SET RETURN_DATE = ? WHERE id = ?");
    return $stmt->execute([$return_date, $loan_id]);
}



// Function to delete a loan by ID
function deleteLoan($id) {
    global $conn;

    // Prepare SQL query to delete the loan by ID
    $sql = "DELETE FROM LOANS WHERE id = :id";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Bind the ID parameter and execute
        return $stmt->execute([':id' => $id]);
    }
    return false;
}

// Function to retrieve all loaned books for a specific user
function getUserLoans($user_id) {
    global $conn;

    // SQL query to retrieve all books loaned by the specific user
    $sql = "SELECT LOANS.id, BOOKS.TITLE, BOOKS.AUTHOR, LOANS.LOAN_DATE, LOANS.RETURN_DATE 
            FROM LOANS 
            JOIN BOOKS ON LOANS.BOOK_ID = BOOKS.id 
            WHERE LOANS.USER_ID = :user_id";

    $stmt = $conn->prepare($sql);

    if ($stmt->execute([':user_id' => $user_id])) {
        // Fetch all loaned books for the user
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return false;
}





?>
