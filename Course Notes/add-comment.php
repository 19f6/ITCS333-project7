<?php
header("Content-Type: application/json");
require 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['note_id']) || !isset($data['comment'])) {
  http_response_code(400);
  echo json_encode(["error" => "Missing required fields"]);
  exit;
}

$noteId = (int)$data['note_id'];
$comment = trim($data['comment']);
$author = isset($data['author']) ? trim($data['author']) : 'Anonymous';

try {
  $stmt = $pdo->prepare("INSERT INTO comments (note_id, author, comment) VALUES (?, ?, ?)");
  $stmt->execute([$noteId, $author, $comment]);
  echo json_encode(["message" => "Comment added successfully"]);
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(["error" => $e->getMessage()]);
}
