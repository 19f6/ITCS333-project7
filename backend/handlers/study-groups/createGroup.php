<?php

session_start();

require_once '../../config/db.php'; 
require_once '../../models/User.php';   
require_once '../../models/StudyGroup.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // method not allowed
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // unauthorized
    echo json_encode(['success' => false, 'message' => 'You must be logged in to create a group.']);
    exit;
}

$user_id = $_SESSION['user_id'];

$group_name = isset($_POST['group-name']) ? trim($_POST['group-name']) : '';
$college_id = isset($_POST['college']) ? trim($_POST['college']) : '';
$course = isset($_POST['course']) ? trim($_POST['course']) : ''; 
$location_type = isset($_POST['location-type']) ? trim($_POST['location-type']) : '';
$location_details = isset($_POST['location']) ? trim($_POST['location']) : ''; 
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$max_members = isset($_POST['max-members']) ? filter_var($_POST['max-members'], FILTER_VALIDATE_INT) : null; 

if (empty($group_name) || empty($college_id) || empty($course) || empty($location_type) || empty($description)) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}

$course = strtoupper(preg_replace('/\s+/', '', $course)); 

if (!preg_match('/^[A-Z]{4}\d{3}$/', $course)) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Invalid course code.']);
    exit;
}


if ($max_members !== null && ($max_members < 3 || $max_members > 50)) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Maximum members must be between 3 and 50.']);
    exit;
}

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

$college_name = isset($college_mapping[$college_id]) ? $college_mapping[$college_id] : 'Unknown College';

$db_location = ucfirst($location_type);

if (($location_type === 'in-person' || $location_type === 'Both') && empty($location_details)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please provide location details for in-person meetings.']);
    exit;
}

$db_location_details = $location_details;

try {
    $pdo = connectDB(); 
    $studyGroup = new StudyGroup($pdo);

    $newGroupId = $studyGroup->createStudyGroup(
        $user_id,
        $group_name,
        $college_name,
        $course,
        $description,
        $db_location,
        $db_location_details,
        $max_members  
    );

    if ($newGroupId) {
        http_response_code(201); 
        echo json_encode([
            'success' => true,
            'message' => 'Study group created successfully!',
            'group_id' => $newGroupId
        ]);
    } else {
        http_response_code(500); 
        echo json_encode(['success' => false, 'message' => 'Failed to create study group. Please try again.']);
    }
} catch (PDOException $e) {
    error_log("Database error in createGroup.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'A database error occurred.']);
} catch (Exception $e) {
    error_log("General error in createGroup.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred.']);
}
?>