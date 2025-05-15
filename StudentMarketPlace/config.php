<?php
// Database configuration for XAMPP
$host = "localhost";
$user = "root";      // Default XAMPP username
$pass = "";          // Default XAMPP password (empty)
$db = "unihub";      // Your database name

// Establish database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Make $pdo available to other files that include this config
    return $pdo;
} catch (PDOException $e) {
    die("Database connection error: " . $e->getMessage());
}
?>