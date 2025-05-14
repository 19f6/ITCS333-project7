<?php
header("Content-Type: application/json");
require 'db.php';

// Get JSON body from request
$data = json_decode(file_get_contents("php://input"), true);

// Basic validation
if (
    !isset($data['course']) ||
    !isset($data['title']) ||
    !isset($data['content'])
) {
    http_response_code(400);
    echo json_encode(["error" => "Missing required fields"]);
    exit;
}

// Prepare and execute insert query
try {
    $stmt = $pdo->prepare("INSERT INTO notes (course_code, title, summary, content, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([
        $data['course'],
        $data['title'],
        $data['summary'],
        $data['content']
    ]);

    echo json_encode(["message" => "Note created successfully"]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to create note: " . $e->getMessage()]);
}
