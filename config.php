<?php
// config.php

// Database configuration
$host = 'localhost';       // Database host (usually localhost)
$dbname = 'dunonglingo_final';   // Your database name
$username = 'root';        // Your database username
$password = '';            // Your database password (leave empty for XAMPP)

// Create a new PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error connecting to database: " . $e->getMessage());
}
?>
