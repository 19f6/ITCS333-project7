<?php
header('Content-Type: application/json');

try {
    // Validate input
    if (empty($_POST['title'])) throw new Exception('Title is required');
    if (empty($_POST['description'])) throw new Exception('Description is required');
    if (!isset($_POST['price']) || !is_numeric($_POST['price'])) throw new Exception('Invalid price');
    if (empty($_POST['category'])) throw new Exception('Category is required');
    if (empty($_FILES['image'])) throw new Exception('Image is required');

    // Handle file upload
    $targetDir = "../uploads/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
    $targetFile = $targetDir . basename($_FILES["image"]["name"]);
    if (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        throw new Exception('Failed to move uploaded file');
    }

    $pdo = require_once '../config.php';
    $stmt = $pdo->prepare("
        INSERT INTO items
        (title, description, price, image, category_id, seller_id, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $_POST['title'],
        $_POST['description'],
        $_POST['price'],
        'uploads/' . basename($_FILES["image"]["name"]),
        $_POST['category'],
        1 // Make sure user with id=1 exists
    ]);

    echo json_encode(['id' => $pdo->lastInsertId()]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>