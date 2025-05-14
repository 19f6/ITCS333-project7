<?php
header("Content-Type: application/json");
require 'db.php';

// Pagination setup (optional, default 1 page of 10)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

try {
    $stmt = $pdo->prepare("SELECT * FROM notes ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->execute();

    $notes = $stmt->fetchAll();
    echo json_encode($notes);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to fetch notes: " . $e->getMessage()]);
}
