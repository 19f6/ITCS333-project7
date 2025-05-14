<?php
$host = "127.0.0.1";
$user = getenv("db_user");
$pass = getenv("db_pass");
$db = getenv("db_name");

function connectDB() {
    global $host, $user, $pass, $db;

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Database connection error: " . $e->getMessage());
    }
}

// Handle the edit request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $college = $_POST['college'];
    $course_title = $_POST['course'];
    $description = $_POST['description'];
    $rating = $_POST['rating'];

    try {
        $pdo = connectDB();
        $stmt = $pdo->prepare("UPDATE course_reviews SET college = ?, course_title = ?, description = ?, rating = ? WHERE id = ?");
        $stmt->execute([$college, $course_title, $description, $rating, $id]);
        echo json_encode(['success' => true, 'message' => "Review updated successfully!"]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => "Error: " . $e->getMessage()]);
    }
}
?>