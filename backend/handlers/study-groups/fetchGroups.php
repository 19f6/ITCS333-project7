<?php
session_start();

require_once '../../config/db.php';
require_once '../../models/StudyGroup.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. This endpoint only accepts GET requests.'
    ]);
    exit;
}

try {
    $pdo = connectDB();
    
    $studyGroupObj = new StudyGroup($pdo);
    
    $college = isset($_GET['college']) ? trim($_GET['college']) : null;
    $course = isset($_GET['course']) ? trim($_GET['course']) : null;
    $location = isset($_GET['location']) ? trim($_GET['location']) : null;
    $search = isset($_GET['search']) ? trim($_GET['search']) : null;
    
    $groups = $studyGroupObj->fetchAllStudyGroups();
    
    $processedGroups = [];
    foreach ($groups as $group) {
        $processedGroups[] = [
            'id' => $group['id'],
            'title' => $group['title'],
            'college' => $group['college'],
            'course' => $group['course'],
            'description' => $group['description'],
            'location' => $group['location'],
            'location_details' => $group['location_details'],
            'members' => $group['member_count'], 
            'max_members' => $group['max_members'],
            'creator' => $group['user_name'],
            'created_at' => $group['created_at']
        ];
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'groups' => $processedGroups
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in studyGroups.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'A database error occurred while fetching study groups.'
    ]);
} catch (Exception $e) {
    error_log("General error in studyGroups.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while processing your request.'
    ]);
}
?>