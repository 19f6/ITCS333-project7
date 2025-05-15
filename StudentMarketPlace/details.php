<?php
require_once 'config.php';

// Check if product ID is provided
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$productId = $_GET['id'];

// Fetch product details
try {
    $stmt = $pdo->prepare("
    SELECT i.*, c.name as category_name, u.email as seller_email
    FROM items i
    LEFT JOIN categories c ON i.category_id = c.id
    LEFT JOIN users u ON i.seller_id = u.id
    WHERE i.id = ?
");
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: index.php");
    exit();
}
} catch (PDOException $e) {
die("Error fetching product: " . $e->getMessage());
}

// Fetch comments for this product
try {
    $stmt = $pdo->prepare("
        SELECT ic.*, u.username as author
        FROM item_comments ic
        JOIN users u ON ic.user_id = u.id
        WHERE ic.item_id = ?
        ORDER BY ic.created_at DESC
    ");
    $stmt->execute([$productId]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching comments: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Detail | UniHub</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="details.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        // Pass product data to JavaScript
        const productData = <?php echo json_encode($product); ?>;
        const commentsData = <?php echo json_encode($comments); ?>;
        
        // Initialize details page with real data
        if (window.initDetails) {
            window.initDetails(productData, commentsData);
        }
    });
    </script>

</head>
<body>
    <nav id="navbar">
        <a class="home" href="../index.html">UniHub</a>
        <ul class="links">
          <li><a href="../event calander/event.html">Calendar</a></li>
          <li><a href="../study-group/study-groups.html">Study Groups</a></li>
          <li><a href="../course reviews/CourseReviews.html">Course Reviews</a></li>
          <li><a href="../Campus-News/index.html">News</a></li>
          <li><a href="../Course Notes/index.html">Notes</a></li>
          <li><a href="#">Marketplace</a></li>
        </ul>
      </nav>
<div class="container">  
        <div class="details-container">
            <div class="header">
                <a href="index.php" class="back-link">Back to MarketPlace</a>
                <div class="header-detail">
                    <div>
                        <h1 class="title"><?php echo htmlspecialchars($product['title']); ?></h1>
                    </div>
                </div>
            </div>

            <div class="content-detail">
                <div class="image-description">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['title']); ?>" 
                         class="image">

                         <div class="description-section">
                        <h1>Description</h1>
                        <h2><?php echo htmlspecialchars($product['title']); ?></h2>
                        <p><?php echo htmlspecialchars($product['description']); ?></p>
                        <p><strong>Price:</strong> <span style="color:#7c7fd9;"><?php echo htmlspecialchars($product['price']); ?>BD</span></p>
                    </div>                
                </div>

                <div class="comments-section">
                    <div class="comments-header">
                        <h2 class="comments-count">Comments (<?php echo count($comments); ?>)</h2>
                    </div>

                    <?php foreach ($comments as $comment): ?>
                        <div class="comment">
                            <div class="comment-content">
                                <div class="comment-header">
                                    <span class="comment-author"><?php echo htmlspecialchars($comment['author']); ?></span>
                                    <span class="comment-time"><?php echo date('M j, Y', strtotime($comment['created_at'])); ?></span>
                                </div>
                                <p class="comment-text"><?php echo htmlspecialchars($comment['comment_text']); ?></p>

                                </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="comment-form">
    <h3>Add a Comment</h3>
    <form action="API/add_comment.php" method="POST">
        <input type="hidden" name="item_id" value="<?php echo $productId; ?>">
        <textarea name="comment_text" placeholder="Write your comment here..." required></textarea>
        <button type="submit">Post Comment</button>
    </form>
</div>
                </div>
            </div>
        </div>
    </div>

    <script src="main.js"></script>
    <script src="details.js"></script>
</body>
</html>
</body>
</html>