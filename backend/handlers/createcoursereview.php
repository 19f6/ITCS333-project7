<?php
session_start();

require_once '../config/db.php'; 
require_once '../models/User.php';   
require_once '../models/CourseReview.php'; // Required model

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // method not allowed
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // unauthorized
    echo json_encode(['success' => false, 'message' => 'You must be logged in to submit a review.']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Collect POST data
$college_id = isset($_POST['college']) ? trim($_POST['college']) : '';
$course = isset($_POST['course']) ? trim($_POST['course']) : ''; 
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$rating = isset($_POST['rating']) ? trim($_POST['rating']) : 5;

// Validation
if (empty($college_id) || empty($course) || empty($rating) || empty($description)) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => "Please fill in all required fields. $college_id"]);
    exit;
}

// College name mapping
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

$college_name = $college_mapping[$college_id] ?? 'Unknown College';

try {
    $pdo = connectDB(); 
    $courseReview = new CourseReview($pdo);

    $newReviewId = $courseReview->createCourseReview(
        $user_id,
        $college_name,
        $course,
        $description,
        $rating
    );

    if ($newReviewId) {
        http_response_code(201); 
        echo json_encode([
            'success' => true,
            'message' => 'Review created successfully!',
            'review_id' => $newReviewId
        ]);
    } else {
        http_response_code(500); 
        echo json_encode(['success' => false, 'message' => 'Failed to create review. Please try again.']);
    }
} catch (PDOException $e) {
    error_log("Database error in createcoursereview.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'A database error occurred.']);
} catch (Exception $e) {
    error_log("General error in createcoursereview.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred.']);
}
?>
