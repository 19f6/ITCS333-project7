<?php
$host = "127.0.0.1";
$user = getenv("db_user");
$pass = getenv("db_pass");
$db = getenv("db_name");

function connectDB() {
    global $host, $user, $pass, $db;

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Database connection error: " . $e->getMessage());
    }
}

// Fetch reviews
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $pdo = connectDB();

        // Capture the search term if provided
        $searchTerm = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%';

        // Prepare SQL query to include search functionality
        $stmt = $pdo->prepare("SELECT * FROM course_reviews WHERE course_title LIKE ? OR college LIKE ?");
        $stmt->execute([$searchTerm, $searchTerm]);

        // Fetch all matching reviews
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return results as JSON
        echo json_encode(['success' => true, 'data' => $reviews]);
    } catch (PDOException $e) {
        // Log the error instead of displaying it in production
        error_log("Database query error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => "An error occurred while fetching reviews."]);
    }
}
?>