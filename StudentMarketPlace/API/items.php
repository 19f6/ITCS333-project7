<?php
header('Content-Type: application/json');
require '../config.php';

// Get filter parameters from request
$search = isset($_GET['search']) ? $_GET['search'] : null;
$category = isset($_GET['category']) ? $_GET['category'] : null;
$priceRange = isset($_GET['price_range']) ? explode('-', $_GET['price_range']) : null;

try {
    // Base query
    $sql = "
        SELECT i.*, c.name as category_name 
        FROM items i
        LEFT JOIN categories c ON i.category_id = c.id
        WHERE i.status = 'available'
    ";
    
    $params = [];
    
    // Add search filter
    if ($search) {
        $sql .= " AND (i.title LIKE ? OR i.description LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    // Add category filter
    if ($category && $category !== 'Category') {
        $sql .= " AND c.name = ?";
        $params[] = $category;
    }
    
    // Add price range filter
    if ($priceRange && count($priceRange) === 2) {
        $minPrice = (float)$priceRange[0];
        $maxPrice = (float)$priceRange[1];
        $sql .= " AND i.price BETWEEN ? AND ?";
        $params[] = $minPrice;
        $params[] = $maxPrice;
    }
    
    $sql .= " ORDER BY i.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}