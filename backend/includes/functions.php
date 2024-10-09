<?php
include 'connection.php';

function addBook($title, $author, $published_year, $genre, $available_copies, $description) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO BOOKS (TITLE, AUTHOR, PUBLISHED_YEAR, GENRE, AVAILABLE_COPIES, description) VALUES (?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$title, $author, $published_year, $genre, $available_copies, $description]);
}

function fetchBooks($search = '', $genre = '') {
    global $conn;

   
    $sql = "SELECT * FROM BOOKS WHERE 1=1";

   
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

  
    $query = "UPDATE BOOKS SET ";
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

    
    $sql = "INSERT INTO USERS (NAME, EMAIL, PASSWORD, ROLE) VALUES (:name, :email, :password, :role)";
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

    
    $sql = "SELECT id, NAME, EMAIL, CREATED_AT FROM USERS";
    $stmt = $conn->prepare($sql);
    
    if ($stmt->execute()) {
       
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return false;
}



function deleteUser($id) {
    global $conn;

    
    $sql = "DELETE FROM USERS WHERE id = :id";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
      
        return $stmt->execute([':id' => $id]);
    }
    return false;
}



function editUser($id, $name, $email, $password) {
    global $conn;

   
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

   
    $sql = "UPDATE USERS SET NAME = :name, EMAIL = :email, PASSWORD = :password WHERE id = :id";
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

   
    $sql = "SELECT * FROM USERS WHERE EMAIL = :email";
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
       
        $sql = "SELECT AVAILABLE_COPIES FROM BOOKS WHERE id = :book_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':book_id' => $book_id]);
        $book = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($book && $book['AVAILABLE_COPIES'] > 0) {
        
            $sql = "INSERT INTO LOANS (USER_ID, BOOK_ID, LOAN_DATE, RETURN_DATE) 
                    VALUES (:user_id, :book_id, :loan_date, :return_date)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':user_id' => $user_id,
                ':book_id' => $book_id,
                ':loan_date' => $loan_date,
                ':return_date' => $return_date
            ]);

          
            $sql = "UPDATE BOOKS SET AVAILABLE_COPIES = AVAILABLE_COPIES - 1 WHERE id = :book_id";
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
    $query = "SELECT LOANS.id AS loan_id, USERS.id AS user_id, USERS.NAME as user_name, BOOKS.TITLE as book_title, LOANS.LOAN_DATE, LOANS.RETURN_DATE 
              FROM LOANS 
              JOIN USERS ON LOANS.USER_ID = USERS.id
              JOIN BOOKS ON LOANS.BOOK_ID = BOOKS.id
              WHERE 1=1";  // Use 1=1 to facilitate appending additional WHERE clauses

    // Append filters to the query if they are set
    $params = [];
    if (!empty($userFilter)) {
        $query .= " AND USERS.NAME LIKE ?";
        $params[] = "%$userFilter%";
    }
    if (!empty($bookTitleFilter)) {
        $query .= " AND BOOKS.TITLE LIKE ?";
        $params[] = "%$bookTitleFilter%";
    }

    $stmt = $conn->prepare($query);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Returns all matched rows as an associative array
}


function updateLoanReturnDate($loan_id, $return_date) {
    global $conn;

    $stmt = $conn->prepare("UPDATE LOANS SET RETURN_DATE = ? WHERE id = ?");
    return $stmt->execute([$return_date, $loan_id]);
}



function deleteLoan($id) {
    global $conn;

    
    $sql = "DELETE FROM LOANS WHERE id = :id";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        
        return $stmt->execute([':id' => $id]);
    }
    return false;
}

function getUserLoans($user_id) {
    global $conn;

<<<<<<< HEAD
    
    $sql = "SELECT LOANS.id, BOOKS.TITLE, BOOKS.AUTHOR, LOANS.LOAN_DATE, LOANS.RETURN_DATE 
            FROM LOANS 
            JOIN BOOKS ON LOANS.BOOK_ID = BOOKS.id 
            WHERE LOANS.USER_ID = :user_id";

    $stmt = $conn->prepare($sql);
=======
    // SQL query to retrieve all books loaned by the specific user
    $stmt = $conn->prepare("SELECT LOANS.id, USERS.id AS user_id, USERS.NAME as user_name, BOOKS.TITLE as book_title, LOANS.LOAN_DATE, LOANS.RETURN_DATE 
                             FROM LOANS 
                             JOIN USERS ON LOANS.USER_ID = USERS.id
                             JOIN BOOKS ON LOANS.BOOK_ID = BOOKS.id
                             WHERE LOANS.USER_ID = :user_id");  // Added WHERE clause to filter by user ID
>>>>>>> 735a5f1359d4eea540c87b33d5bc209a5c795436

    // Execute the statement with the user ID parameter
    if ($stmt->execute([':user_id' => $user_id])) {
        // Fetch all loaned books for the user
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    return false; // Return false if the execution fails
}









