<?php

//local (XAMPP server)
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'unihub';


// for replit server
// $host = "127.0.0.1";
// $user = getenv("db_user");
// $pass = getenv("db_pass");
// $db = getenv("db_name");

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host", $$user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db`");
    $pdo->exec("USE `$db`");

    // Execute the SQL file to create tables
    $sql = file_get_contents('unihub.sql');
    $pdo->exec($sql);

    echo "Database and tables created/verified successfully!";

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
