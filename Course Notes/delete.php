<?php
header("Content-Type: application/json");
require 'db.php';

// Accept DELETE method only
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
  http_response_code(405);
  echo json_encode(["error" => "Only DELETE method is allowed"]);
  exit;
}

// Get the note ID from the query string
if (!isset($_GET['id'])) {
  http_response_code(400);
  echo json_encode(["error" => "Missing note ID"]);
  exit;
}

$id = (int)$_GET['id'];

try {
  $stmt = $pdo->prepare("DELETE FROM notes WHERE id = ?");
  $stmt->execute([$id]);

  if ($stmt->rowCount()) {
    echo json_encode(["message" => "Note deleted successfully"]);
  } else {
    http_response_code(404);
    echo json_encode(["error" => "Note not found"]);
  }
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
