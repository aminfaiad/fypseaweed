<?php
// Database connection settings
$host = 'localhost'; // Update with your database host
$dbname = 'seaweed';
$username = 'admin'; // Update with your database username
$password = 'admin'; // Update with your database password;

try {
    // Establish a connection to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle database connection errors
    die("Database connection failed: " . $e->getMessage());
}
?>
