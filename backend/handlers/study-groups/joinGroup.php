<?php

session_start();

require_once '../../config/db.php';
require_once '../../models/StudyGroup.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode([
        'success' => false,
        'message' => 'You must be logged in to join a study group.'
    ]);
    exit;
}

// Handle HTTP method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. This endpoint only accepts POST requests.'
    ]);
    exit;
}

// Get request body
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['group_id']) || empty($input['group_id'])) {
    http_response_code(400); // Bad Request
    echo json_encode([
        'success' => false,
        'message' => 'Group ID is required.'
    ]);
    exit;
}

$group_id = $input['group_id'];
$user_id = $_SESSION['user_id'];

try {
    $pdo = connectDB();
    
    // check if group exists and is not full
    $stmt = $pdo->prepare("SELECT sg.*, COUNT(gm.id) as current_members 
                          FROM study_groups sg 
                          LEFT JOIN group_members gm ON sg.id = gm.group_id 
                          WHERE sg.id = ? 
                          GROUP BY sg.id");
    $stmt->execute([$group_id]);
    $group = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$group) {
        http_response_code(404); // Not Found
        echo json_encode([
            'success' => false,
            'message' => 'Study group not found.'
        ]);
        exit;
    }
    
    // check if group is full
    if ($group['current_members'] >= $group['max_members']) {
        http_response_code(400); // Bad Request
        echo json_encode([
            'success' => false,
            'message' => 'This study group is already full.'
        ]);
        exit;
    }
    
    // check if user already joined this group
    $stmt = $pdo->prepare("SELECT id FROM group_members WHERE group_id = ? AND user_id = ?");
    $stmt->execute([$group_id, $user_id]);
    
    if ($stmt->rowCount() > 0) {
        http_response_code(400); // Bad Request
        echo json_encode([
            'success' => false,
            'message' => 'You have already joined this study group.'
        ]);
        exit;
    }
    
    // add user to group
    $stmt = $pdo->prepare("INSERT INTO group_members (group_id, user_id, joined_at) VALUES (?, ?, NOW())");
    $result = $stmt->execute([$group_id, $user_id]);
    
    if ($result) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'You have successfully joined the study group!'
        ]);
    } else {
        throw new Exception("Failed to join the study group");
    }
    
} catch (PDOException $e) {
    error_log("Database error in joinGroup.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'A database error occurred while joining the study group.'
    ]);
} catch (Exception $e) {
    error_log("General error in joinGroup.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while processing your request.'
    ]);
}
?>