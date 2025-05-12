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
    echo json_encode(['success' => false, 'message' => 'You must be logged in to update this group.']);
    exit;
}
$user_id = $_SESSION['user_id'];

$data = json_decode(file_get_contents('php://input'), true);

$group_id = $data['group_id'];

if (!isset($data['group_id']) || !filter_var($data['group_id'], FILTER_VALIDATE_INT)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => "Invalid group ID."]);
    exit;
}


$title = isset($data['title']) ? trim($data['title']) : null;
$college = isset($data['college']) ? trim($data['college']) : null;
$course = isset($data['course']) ? trim($data['course']) : null;
$description = isset($data['description']) ? trim($data['description']) : null;
$location = isset($data['location']) ? trim($data['location']) : null;
$location_details = isset($data['location_details']) ? trim($data['location_details']) : null;
$max_members = isset($data['max_members']) ? filter_var($data['max_members'], FILTER_VALIDATE_INT) : null;

// Input validation
if (empty($title)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Group title is required']);
    exit;
}

if ($max_members !== null && $max_members < 3) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Maximum members must be at least 3']);
    exit;
}

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
        echo json_encode(['success' => false, 'message' => 'You are not authorized to update this group']);
        exit;
    }
    
    // update the group
    $result = $studyGroup->updateStudyGroup(
        $group_id, 
        $title, 
        $college, 
        $course, 
        $description, 
        $location, 
        $location_details, 
        $max_members
    );
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Study group updated successfully',
            'group_id' => $group_id
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update study group']);
    }
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred while updating the group']);
}
?>