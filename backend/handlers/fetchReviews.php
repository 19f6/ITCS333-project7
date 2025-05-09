<?php
session_start();

require_once '../config/db.php';
require_once '../models/CourseReview.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. This endpoint only accepts GET requests.'
    ]);
    exit;
}

try {
    $pdo = connectDB();
    $courseReviewsObj = new CourseReview($pdo);

    $reviews = $courseReviewsObj->fetchAllCourseReviews();

    $processedReviews = [];
    foreach ($reviews as $review) {
        $processedReviews[] = [
            'id' => $review['id'],
            'college' => $review['college'],
            'course' => $review['course_title'],   // updated
            'comment' => $review['review_comment'], // updated
            'rating' => $review['rating'],
            'creator' => $review['user_name'] ?? 'Anonymous',
            'created_at' => $review['created_at'] ?? null
        ];
    }

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'reviews' => $processedReviews
    ]);
} catch (PDOException $e) {
    error_log("Database error in fetchReviews.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'A database error occurred while fetching course reviews.'
    ]);
} catch (Exception $e) {
    error_log("General error in fetchReviews.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while processing your request.'
    ]);
}
?>
