<?php
// connection.php

$host = 'localhost'; // or your host
$db = 'lms'; // your database name
$user = 'root'; // your database username
$pass = 'geslo'; // your database password

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

