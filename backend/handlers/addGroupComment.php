<?php

session_start();

require_once '../config/db.php';
require_once '../models/User.php';
require_once '../models/StudyGroup.php';
require_once '../models/GroupComment.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['success' => false, 'message' => 'You must be logged in to add a comment.']);
    exit;
}

// get json data from the request
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// Validate required fields
$user_id = $_SESSION['user_id'];
$group_id = isset($data['group_id']) ? filter_var($data['group_id'], FILTER_VALIDATE_INT) : null;
$content = isset($data['content']) ? trim($data['content']) : '';

if (!$group_id || empty($content)) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
    exit;
}

// validate content length
if (strlen($content) > 1000) {
    http_response_code(400); 
    echo json_encode(['success' => false, 'message' => 'Comment is too long. Maximum 1000 characters allowed.']);
    exit;
}

try {
    $pdo = connectDB();
    
    $studyGroup = new StudyGroup($pdo);
    $group = $studyGroup->getGroupById($group_id);
    
    if (!$group) {
        http_response_code(404); 
        echo json_encode(['success' => false, 'message' => 'Study group not found.']);
        exit;
    }
    
    $groupComment = new GroupComment($pdo);
    $comment_id = $groupComment->createComment($user_id, $group_id, $content);
    
    if ($comment_id) {
        $comment = $groupComment->getCommentById($comment_id);
        
        http_response_code(201); 
        echo json_encode([
            'success' => true, 
            'message' => 'Comment added successfully.',
            'comment' => $comment
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to add comment. Please try again.']);
    }
} catch (PDOException $e) {
    error_log("Database error in addGroupComment.php: " . $e->getMessage());
    http_response_code(500); 
    echo json_encode(['success' => false, 'message' => 'A database error occurred.']);
} catch (Exception $e) {
    error_log("General error in addGroupComment.php: " . $e->getMessage());
    http_response_code(500); 
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred.']);
}
?>