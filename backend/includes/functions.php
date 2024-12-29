<?php
include 'connection.php';

header("Access-Control-Allow-Origin: https://online-library-management-60dd26a214d9.herokuapp.com/"); // Your frontend origin

// Specify which HTTP methods are allowed (GET, POST, etc.)
header("Access-Control-Allow-Methods: POST, PUT, DELETE, GET, OPTIONS");

function addBook($title, $author, $published_year, $genre, $available_copies, $description) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO books (TITLE, AUTHOR, PUBLISHED_YEAR, GENRE, AVAILABLE_COPIES, description) VALUES (?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$title, $author, $published_year, $genre, $available_copies, $description]);
}

function fetchBooks($search = '', $genre = '') {
    global $conn;

    // Start with the base query
    $sql = "SELECT * FROM books WHERE 1=1";

   
    if (!empty($search)) {
        $sql .= " AND (TITLE LIKE :search OR AUTHOR LIKE :search)";
    }

  
    if (!empty($genre)) {
        $sql .= " AND GENRE = :genre";
    }

   
    $stmt = $conn->prepare($sql);

 
    if (!empty($search)) {
        $searchParam = "%" . $search . "%";
        $stmt->bindParam(':search', $searchParam);
    }

  
    if (!empty($genre)) {
        $stmt->bindParam(':genre', $genre);
    }

    
    $stmt->execute();

   
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function editBook($id, $data) {
    global $conn;

    // Prepare the base query
    $query = "UPDATE books SET ";
    $params = [];
    
    
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

    
    $query = rtrim($query, ', ');

    
    $query .= " WHERE id = :id";
    $params[':id'] = $id;

  
    $stmt = $conn->prepare($query);
    return $stmt->execute($params);
}


function deleteBook($id) {
    global $conn;

    
    $deleteLoansQuery = "DELETE FROM loans WHERE BOOK_ID = :book_id";
    $stmt = $conn->prepare($deleteLoansQuery);
    $stmt->execute([':book_id' => $id]);

  
    $deleteBookQuery = "DELETE FROM books WHERE id = :id";
    $stmt = $conn->prepare($deleteBookQuery);
    return $stmt->execute([':id' => $id]);
}




function addUser($name, $email, $password, $role = 'USER') {
    global $conn;

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL statement with PDO to include the role
    $sql = "INSERT INTO users (NAME, EMAIL, PASSWORD, ROLE) VALUES (:name, :email, :password, :role)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
       
        return $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $hashed_password,
            ':role' => $role 
        ]);
    }
    return false;
}



function fetchUsers() {
    global $conn;

    // Prepare the SQL query to get all users
    $sql = "SELECT id, NAME, EMAIL, CREATED_AT FROM users";
    $stmt = $conn->prepare($sql);
    
    if ($stmt->execute()) {
       
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return false;
}



function deleteUser($id) {
    global $conn;

    // Prepare SQL query to delete user by ID
    $sql = "DELETE FROM users WHERE id = :id";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
      
        return $stmt->execute([':id' => $id]);
    }
    return false;
}



function editUser($id, $name, $email, $password) {
    global $conn;

   
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL query to update the user
    $sql = "UPDATE users SET NAME = :name, EMAIL = :email, PASSWORD = :password WHERE id = :id";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
       
        return $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $hashed_password,
            ':id' => $id
        ]);
    }
    return false;
}



function loginUser($email, $password) {
    global $conn;

    // Prepare SQL query to find the user by email
    $sql = "SELECT * FROM users WHERE EMAIL = :email";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute([':email' => $email])) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

       
        if ($user && password_verify($password, $user['PASSWORD'])) {
           
            unset($user['PASSWORD']);
            return $user;
        }
    }
    return false;
}

function addLoan($user_id, $book_id, $loan_date, $return_date = null) {
    global $conn;

   
    $conn->beginTransaction();

    try {
        // Check if there are available copies for the book
        $sql = "SELECT AVAILABLE_COPIES FROM books WHERE id = :book_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':book_id' => $book_id]);
        $book = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($book && $book['AVAILABLE_COPIES'] > 0) {
            // Add the loan to the LOANS table
            $sql = "INSERT INTO loans (USER_ID, BOOK_ID, LOAN_DATE, RETURN_DATE) 
                    VALUES (:user_id, :book_id, :loan_date, :return_date)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':user_id' => $user_id,
                ':book_id' => $book_id,
                ':loan_date' => $loan_date,
                ':return_date' => $return_date
            ]);

            // Decrease the available copies by 1
            $sql = "UPDATE books SET AVAILABLE_COPIES = AVAILABLE_COPIES - 1 WHERE id = :book_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':book_id' => $book_id]);

       
            $conn->commit();

            return true; 
        } else {
           
            $conn->rollBack();
            return false;
        }

    } catch (Exception $e) {
       
        $conn->rollBack();
        return false;
    }
}
function fetchLoans($userFilter = '', $bookTitleFilter = '') {
    global $conn;

    // Base query to fetch loans, including the user ID
    $query = "SELECT loans.id AS loan_id, users.id AS user_id, users.NAME as user_name, books.TITLE as book_title, loans.LOAN_DATE, loans.RETURN_DATE 
              FROM loans
              JOIN users ON loans.USER_ID = users.id
              JOIN books ON loans.BOOK_ID = books.id
              WHERE 1=1";  // Use 1=1 to facilitate appending additional WHERE clauses

    // Append filters to the query if they are set
    $params = [];
    if (!empty($userFilter)) {
        $query .= " AND users.NAME LIKE ?";
        $params[] = "%$userFilter%";
    }
    if (!empty($bookTitleFilter)) {
        $query .= " AND books.TITLE LIKE ?";
        $params[] = "%$bookTitleFilter%";
    }

    $stmt = $conn->prepare($query);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Returns all matched rows as an associative array
}


function updateLoanReturnDate($loan_id, $return_date) {
    global $conn;

    $stmt = $conn->prepare("UPDATE loans SET RETURN_DATE = ? WHERE id = ?");
    return $stmt->execute([$return_date, $loan_id]);
}



function deleteLoan($id) {
    global $conn;

    // Prepare SQL query to delete the loan by ID
    $sql = "DELETE FROM loans WHERE id = :id";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        
        return $stmt->execute([':id' => $id]);
    }
    return false;
}

function getUserLoans($user_id) {
    global $conn;

    $stmt = $conn->prepare("SELECT loans.id, users.id AS user_id, users.NAME as user_name, books.TITLE as book_title, loans.LOAN_DATE, loans.RETURN_DATE 
                             FROM loans
                             JOIN users ON loans.USER_ID = users.id
                             JOIN books ON loans.BOOK_ID = books.id
                             WHERE loans.USER_ID = :user_id");  // Added WHERE clause to filter by user ID


    // Execute the statement with the user ID parameter
    if ($stmt->execute([':user_id' => $user_id])) {
        // Fetch all loaned books for the user
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    return false; // Return false if the execution fails
}









