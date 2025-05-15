<?php
require_once '../config.php';

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die("Method not allowed");
}

// Validate input
if (!isset($_POST['item_id']) || !isset($_POST['comment_text'])) {
    http_response_code(400);
    die("Missing required fields");
}

$itemId = $_POST['item_id'];
$commentText = trim($_POST['comment_text']);

if (empty($commentText)) {
    http_response_code(400);
    die("Comment text cannot be empty");
}

// Insert comment into database
try {
    // Using a default user_id (1) since we're not checking login
    // You might want to change this or make it optional in your database
    $stmt = $pdo->prepare("
        INSERT INTO item_comments (item_id, user_id, comment_text, created_at)
        VALUES (?, 1, ?, NOW())
    ");
    $stmt->execute([$itemId, $commentText]);
    
    // Return success response
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    die("Error posting comment: " . $e->getMessage());
}

header("Location: ../details.php?id=" . $itemId);
exit();

?>