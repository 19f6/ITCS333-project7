<?php
header("Content-Type: application/json");
require 'db.php';

if (!isset($_GET['note_id'])) {
  http_response_code(400);
  echo json_encode(["error" => "Missing note ID"]);
  exit;
}

$noteId = (int)$_GET['note_id'];

try {
  $stmt = $pdo->prepare("SELECT * FROM comments WHERE note_id = ? ORDER BY created_at DESC");
  $stmt->execute([$noteId]);
  $comments = $stmt->fetchAll();
  echo json_encode($comments);
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(["error" => $e->getMessage()]);
}
