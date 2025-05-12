<?php
session_start();

require_once '../../config/db.php';
require_once '../../models/StudyGroup.php';
require_once '../../models/GroupComment.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['success' => false, 'message' => 'You must be logged in to view this group.']);
    exit;
}
$user_id = $_SESSION['user_id'];

$group_id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : null;
if (!$group_id) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Group ID is required']);
    exit;
}


try {
    $pdo = connectDB();
    $studyGroup = new StudyGroup($pdo);
    $groupComment = new GroupComment($pdo);

    $isMember = false;
    foreach ($studyGroup->getGroupMembers($group_id) as $member) {
        if ($member['id'] == $user_id) {
            $isMember = true;
            break;
        }
    }

    if (!$isMember) {
        http_response_code(403); // Forbidden
        echo json_encode(['success' => false, 'message' => 'Access denied. You are not a member of this group.']);
        exit;
    }

    // fetch group details
    $groupDetails = $studyGroup->getGroupById($group_id);
    if (!$groupDetails) {
        http_response_code(404); // Not Found
        echo json_encode(['success' => false, 'message' => 'Group not found']);
        exit;
    }

    // fetch group members
    $members = $studyGroup->getGroupMembers($group_id);

    // fetch comments 
    $comments = $groupComment->getCommentsByGroupId($group_id);

    $isAdmin = $_SESSION['user_id'] == $groupDetails->user_id;

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'group' => $groupDetails,
        'members' => $members,
        'comments' => $comments,
        "current_userid" =>  $_SESSION['user_id'],
    ]);

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'Failed to fetch group details']);
}
?>