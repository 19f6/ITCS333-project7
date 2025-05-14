<?php
header("Content-Type: application/json");
require 'db.php';

// Accept only PUT requests
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
  http_response_code(405);
  echo json_encode(["error" => "Only PUT method is allowed"]);
  exit;
}

// Get the ID from query string
if (!isset($_GET['id'])) {
  http_response_code(400);
  echo json_encode(["error" => "Missing note ID"]);
  exit;
}

$id = (int) $_GET['id'];
$data = json_decode(file_get_contents("php://input"), true);

// Validate input
if (
  !isset($data['course']) ||
  !isset($data['title']) ||
  !isset($data['summary']) ||
  !isset($data['content'])
) {
  http_response_code(400);
  echo json_encode(["error" => "Missing required fields"]);
  exit;
}

try {
  $stmt = $pdo->prepare("UPDATE notes SET course_code = ?, title = ?, summary = ?, content = ? WHERE id = ?");
  $stmt->execute([
    $data['course'],
    $data['title'],
    $data['summary'],
    $data['content'],
    $id
  ]);

  if ($stmt->rowCount()) {
    echo json_encode(["message" => "Note updated successfully"]);
  } else {
    http_response_code(404);
    echo json_encode(["error" => "Note not found or unchanged"]);
  }
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
