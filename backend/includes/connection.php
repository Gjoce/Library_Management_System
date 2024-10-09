<?php

<<<<<<< HEAD

$host = 'localhost'; 
$db = 'library_managament_system'; 
$user = 'root'; 
$pass = ''; 
=======
$host = 'localhost'; // or your host
$db = 'lms'; // your database name
$user = 'root'; // your database username
$pass = 'geslo'; // your database password
>>>>>>> 735a5f1359d4eea540c87b33d5bc209a5c795436

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

