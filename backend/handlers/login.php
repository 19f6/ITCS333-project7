<?php
session_start(); 

require_once '../config/db.php'; 
require_once '../models/User.php'; 

$pdo = connectDB();

unset($_SESSION['login_error']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = "Email and password are required.";
        header('Location: ../../login.php');
        exit;
    } else {
        try {
            $user = new User($pdo);
            $loginSuccess = $user->login($email, $password);

            if ($loginSuccess) {
               // store user info in session variables
               $_SESSION['user_id'] = $user->getId();
         
               header('Location: ../../index.php'); 
               exit;
            } else {
                $_SESSION['login_error'] = "Invalid credentials. Please try again.";
                header('Location: ../../login.php');
                exit;
            }
        } catch (Exception $e) {
            $_SESSION['login_error'] = "An error occurred: " . $e->getMessage();
            header('Location: ../../login.php');
            exit;
        }
    }
}

header('Location: ../../login.php');
exit;
?>