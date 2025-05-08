<?php
session_start();
require_once '../config/db.php';
require_once '../models/User.php';

$pdo = connectDB();

unset($_SESSION['register_error']);
unset($_SESSION['register_success']);

$_SESSION['form_data'] = [
    'name' => '',
    'email' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $_SESSION['form_data'] = [
        'name' => $name,
        'email' => $email
    ];

    // Validate form data
    if (empty($name) || empty($email) || empty($password) || empty($confirm)) {
        $_SESSION['register_error'] = "All fields are required.";
        header('Location: ../../register.php');
        exit;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['register_error'] = "Invalid email format.";
        header('Location: ../../register.php');
        exit;
    } elseif ($password !== $confirm) {
        $_SESSION['register_error'] = "Passwords do not match.";
        header('Location: ../../register.php');
        exit;
    } elseif (strlen($password) < 8) {
        $_SESSION['register_error'] = "Password must be at least 8 characters.";
        header('Location: ../../register.php');
        exit;
    } else {
        try {
            $user = new User($pdo, null, $name, $email, $password);
            $userId = $user->register();
            
            unset($_SESSION['form_data']);
            
            $_SESSION['register_success'] = "Registration successful!";
            
            $_SESSION['user_id'] = $userId;
            
            header('Location: ../../index.php');
            exit;
        } catch (Exception $e) {
            $_SESSION['register_error'] = $e->getMessage();
            header('Location: ../../register.php');
            exit;
        }
    }
}

header('Location: ../../register.php');
exit;
?>