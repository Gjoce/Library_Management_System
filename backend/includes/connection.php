<?php

require_once(__DIR__ . '/../vendor/autoload.php');

// Retrieve the JawsDB connection string from Heroku environment variables
$jawsdbUrl = getenv('JAWSDB_URL');

// Parse the connection string into its components
$parsedUrl = parse_url($jawsdbUrl);

// Extract the host, username, password, and database name
$host = $parsedUrl['host'];
$port = isset($parsedUrl['port']) ? $parsedUrl['port'] : 3306; // Default to 3306 if no port is specified
$username = $parsedUrl['user'];
$password = $parsedUrl['pass'];
$dbname = ltrim($parsedUrl['path'], '/'); // Remove the leading '/' from the database name

try {
    // Create a PDO connection using the parsed components
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname";
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected successfully!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
