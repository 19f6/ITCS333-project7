<?php
$host = "127.0.0.1";
$user = getenv("db_user");
$pass = getenv("db_pass");
$db = getenv("db_name");
$reviewsPerPage = 4;

// Function to connect to the database
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

        $searchTerm = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $reviewsPerPage;

        // Prepare SQL query
        $stmt = $pdo->prepare("SELECT * FROM course_reviews WHERE course_title LIKE ? OR college LIKE ? LIMIT ?, ?");
        $stmt->bindParam(1, $searchTerm, PDO::PARAM_STR);
        $stmt->bindParam(2, $searchTerm, PDO::PARAM_STR);
        $stmt->bindParam(3, $offset, PDO::PARAM_INT);
        $stmt->bindParam(4, $reviewsPerPage, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch reviews
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Count total reviews for pagination
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM course_reviews WHERE course_title LIKE ? OR college LIKE ?");
        $countStmt->bindParam(1, $searchTerm, PDO::PARAM_STR);
        $countStmt->bindParam(2, $searchTerm, PDO::PARAM_STR);
        $countStmt->execute();
        $totalReviews = $countStmt->fetchColumn();
        $totalPages = ceil($totalReviews / $reviewsPerPage);

        // Return results as JSON
        echo json_encode(['success' => true, 'data' => $reviews, 'totalPages' => $totalPages]);
    } catch (PDOException $e) {
        error_log("Database query error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => "An error occurred while fetching reviews."]);
    }
}
?>