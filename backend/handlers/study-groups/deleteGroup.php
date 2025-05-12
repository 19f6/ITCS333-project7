<?php
session_start();

require_once '../../config/db.php';
require_once '../../models/StudyGroup.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['success' => false, 'message' => 'You must be logged in to delete this group.']);
    exit;
}
$user_id = $_SESSION['user_id'];

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['group_id']) || !filter_var($data['group_id'], FILTER_VALIDATE_INT)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid group ID']);
    exit;
}

$group_id = $data['group_id'];

try {
    $pdo = connectDB();
    $studyGroup = new StudyGroup($pdo);
    
    // check if user is the creator of the gruop
    $groupDetails = $studyGroup->getGroupById($group_id);
    
    if (!$groupDetails) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Group not found']);
        exit;
    }
    
    if ($groupDetails['user_id'] != $user_id) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'You are not authorized to delete this group']);
        exit;
    }
    
    // delete the group
    $result = $studyGroup->deleteStudyGroup($group_id);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Study group deleted successfully'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to delete study group']);
    }
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred while deleting the group']);
}
?>