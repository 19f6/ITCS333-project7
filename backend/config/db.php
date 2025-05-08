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

function connectDB() {
    global $host, $user, $pass, $db;
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db", $$user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Database connection error: " . $e->getMessage());
    }
}
?>