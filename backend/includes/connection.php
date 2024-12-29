<?php

require_once __DIR__ . '../vendor/autoload.php';

// Retrieve the JawsDB connection string from Heroku environment variables
$jawsdbUrl = getenv('JAWSDB_URL');

try {
    // Create a PDO connection using the JawsDB connection string
    $conn = new PDO($jawsdbUrl);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
