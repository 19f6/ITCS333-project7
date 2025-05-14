<?php
require_once(__DIR__ . '/backend/config/db.php');

$pdo = connectDB();

try {
    $pdo->exec("ALTER TABLE campus_news MODIFY image LONGTEXT");
    echo "✅ Column 'image' changed to LONGTEXT successfully.";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
