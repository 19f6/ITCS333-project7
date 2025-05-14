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

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $college = $_POST['college'];
    $course_title = $_POST['course'];
    $description = $_POST['description'];
    $rating = $_POST['rating'];
    $college_mapping = [
        "1" => "College of Information Technology",
        "2" => "College of Arts",
        "3" => "College of Law",
        "4" => "College of Engineering",
        "5" => "College of Physical Education",
        "6" => "College of Health and Sport Sciences",
        "7" => "Languages Institute",
        "8" => "College of Applied Studies",
        "9" => "College of Science",
        "10" => "College of Business Administration"
    ];
    $college = $college_mapping[$college] ?? $college;
    if (isset($_POST['id'])) {
        // Update existing review
        $id = $_POST['id'];
        $stmt = connectDB()->prepare("UPDATE course_reviews SET college = ?, course_title = ?, description = ?, rating = ? WHERE id = ?");
        $stmt->execute([$college, $course_title, $description, $rating, $id]);
        echo json_encode(['success' => true, 'message' => "Review updated successfully!"]);
    } else {
        // Create new review
        $stmt = connectDB()->prepare("INSERT INTO course_reviews (college, course_title, description, rating) VALUES (?, ?, ?, ?)");
        $stmt->execute([$college, $course_title, $description, $rating]);
        echo json_encode(['success' => true, 'message' => "Review created successfully!"]);
    }
}
?>