<?php
$pdo = require 'config.php';

// Fetch all products from database
try {
    $stmt = $pdo->query("
        SELECT i.*, c.name as category_name 
        FROM items i
        LEFT JOIN categories c ON i.category_id = c.id
        WHERE i.status = 'available'
        ORDER BY i.created_at DESC
    ");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching products: " . $e->getMessage());
}

// Fetch categories for filter dropdown
try {
  $stmt = $pdo->query("SELECT * FROM categories");
  $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("Error fetching categories: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO items 
            (title, description, price, image, category_id, status, seller_id, item_condition, created_at)
            VALUES (?, ?, ?, ?, ?, 'available', ?, ?, NOW())
        ");
        
        $stmt->execute([
            $_POST['title'],
            $_POST['description'],
            $_POST['price'],
            'uploads/' . basename($_FILES['image']['name']),
            $_POST['category'],
            1, // Replace with actual seller ID
            $_POST['condition']
        ]);
        
        // Move uploaded file
        move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . basename($_FILES['image']['name']));
        
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        die("Error adding product: " . $e->getMessage());
    }
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css" />
    <title>MarketPlace | UniHub</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap"
      rel="stylesheet"
    />

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        // This will be replaced by your main.js functionality
        // Now it will work with real data from PHP
        const productsData = <?php echo json_encode($products); ?>;
        
        // Initialize your app with real data
        if (window.initApp) {
            window.initApp(productsData);
        }
    });
    </script>

  </head>

  <body>
    <div class="container">
      <!-- Navigation Bar -->
      <nav id="navbar">
        <a class="home" href="../index.html">UniHub</a>
        <ul class="links">
            <li><a href="../event calander/event.html">Calendar</a></li>
            <li><a href="../study-group/study-groups.html">Study Groups</a></li>
            <li><a href="../course reviews/CourseReviews.html">Course Reviews</a></li>
            <li><a href="../Campus-News/index.html">News</a></li>
            <li><a href="../Course Notes/index.html">Notes</a></li>
            <li><a href="../StudentMarketPlace/index.html">Marketplace</a></li>
        </ul>
      </nav>

      <div class="navbar-title">
        <h1>Student Marketplace</h1>
      </div>

      <div class="nav-content">
        <!-- Search Box -->
        <div class="search-box">
          <input type="text" placeholder="Search products..." />
        </div>

        <!-- Filters -->
        <div class="filters">
        <select id="category-filter">
            <option value="Category">Category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo htmlspecialchars($category['name']); ?>">
                    <?php echo htmlspecialchars($category['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
          
          <select id="price-filter">
            <option value="Price Range">Price Range</option>
            <option value="$0 - $50">$0 - $50</option>
            <option value="$50 - $100">$50 - $100</option>
            <option value="$100 - $200">$100 - $200</option>
            <option value="$200+">$200+</option>
          </select>
          
          
        </div>
      </div>

      <!-- Main Content -->
      <div class="main-content">
        <div class="tabs">
          <div class="tab active" data-tab="available">Products</div>
        </div>

        <div class="content-wrapper">
          <!-- Product Listing -->
          <div class="product-list">
        <?php foreach ($products as $product): ?>
            <div class="product-item">
                <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                     alt="<?php echo htmlspecialchars($product['title']); ?>" 
                     class="product-image" />
                <div class="product-details">
                    <h4><?php echo htmlspecialchars($product['title']); ?></h4>
                    <p><?php echo htmlspecialchars($product['description']); ?></p>
                    <p class="price">Price: <?php echo htmlspecialchars($product['price']); ?>BD</p>
                    <div class="rating">
                        <?php 
                        $stars = str_repeat('★', floor($product['rating'])) . 
                                 str_repeat('☆', 5 - floor($product['rating']));
                        echo $stars . ' <span>(' . $product['reviews'] . ')</span>';
                        ?>
                    </div>
                </div>
                <a href="details.php?id=<?php echo $product['id']; ?>" class="group-links">
    <button class="card-button">View Details</button>
</a>
            </div>
        <?php endforeach; ?>
    </div>
            
          </div>

          <!-- Floating Form Button (hidden by default, will be shown with JS) -->
          <button id="form-toggle" class="floating-form-button">
            <span>+</span>
          </button>

          <!-- Product Form (hidden by default, will be shown with JS) -->
          <div class="form-container hidden" action="api/items.php" method="POST" enctype="multipart/form-data">
            <button id="close-form" class="close-button">&times;</button>
            <h3>Add New Product</h3>
            <form>
              <label for="product">Product Name</label>
              <input type="text" id="product" name="title" placeholder="Enter product name" required />
              
              <label for="description">Description</label>
              <textarea id="description" name="description" placeholder="Describe your product" required></textarea>
              
              <label for="price">Price (BD)</label>
              <input type="number" id="price" name="price" placeholder="Enter price" required />

              <label for="image">Product Image</label>
              <input type="file" id="image" name="image" accept="image/*" required />
              
              <label for="category">Category</label>
              <select id="category" name="category" required>
                <option value="">Select category</option>
                <?php foreach ($categories as $category): ?>
                  <option value="<?php echo htmlspecialchars($category['id']); ?>">
                    <?php echo htmlspecialchars($category['name']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
              
              <div class="button-group">
                <button class="add" type="submit">Add Product</button>
                <button class="cancel" type="reset">Cancel</button>
              </div>
            </form>
          </div>
        </div>

        <!-- Pagination -->
        <div class="pagination">
          <a href="#">&laquo;</a> 
          <a class="active" href="#">1</a>
          <a href="#">2</a>
          <a href="#">3</a>
          <a href="#">4</a>
          <a href="#">5</a>
          <a href="#">&raquo;</a>
        </div>
      </div>
    </div>

    <script src="main.js"></script>
    <script src="details.js"></script>
  </body>
</html>