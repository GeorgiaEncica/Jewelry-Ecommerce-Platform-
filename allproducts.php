<?php

include 'live_visitors.php'; 
$conn = new mysqli("localhost", "root", "", "jewelry_database");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if this is an AJAX request for filtering
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    // Get filter values
    $category_filter = isset($_GET['category']) ? $_GET['category'] : '';
    $price_sort = isset($_GET['price_sort']) ? $_GET['price_sort'] : '';
    $price_range = isset($_GET['price_range']) ? $_GET['price_range'] : '';

    // Build SQL query with filters
    $sql = "
        SELECT p.id, p.name, p.description, p.price, p.category, 
               (SELECT image_path FROM product_images WHERE product_id = p.id LIMIT 1) AS image
        FROM products p
        WHERE 1=1
    ";

    // Apply category filter
    if (!empty($category_filter) && $category_filter !== 'all') {
        $sql .= " AND p.category = '" . $conn->real_escape_string($category_filter) . "'";
    }

    // Apply price range filter
    if (!empty($price_range) && $price_range !== 'all') {
        switch ($price_range) {
            case '300':
                $sql .= " AND p.price < 300";
                break;
            case '500':
                $sql .= " AND p.price < 500";
                break;
            case '800':
                $sql .= " AND p.price < 800";
                break;
        }
    }

    // Apply price sorting
    if (!empty($price_sort) && $price_sort !== 'default') {
        if ($price_sort == 'low') {
            $sql .= " ORDER BY p.price ASC";
        } elseif ($price_sort == 'high') {
            $sql .= " ORDER BY p.price DESC";
        }
    } else {
        $sql .= " ORDER BY p.id DESC";
    }

    $result = $conn->query($sql);
    $products = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'products' => $products, 'count' => count($products)]);
    $conn->close();
    exit;
}

// Fetch all categories for dropdown
$categories = [];
$cat_result = $conn->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != '' ORDER BY category ASC");
if ($cat_result && $cat_result->num_rows > 0) {
    while ($row = $cat_result->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Luxury Jewelry Store</title>
    <script src="script.js" defer></script>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        
        .filter-section {
            background: white;
            padding: 1.5rem 2rem;
            margin: 2rem auto;
            max-width: 1400px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .filter-form {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: end;
            justify-content: center;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
            max-width: 280px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-group select {
            width: 100%;
            padding: 0.7rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            background: white;
            transition: border-color 0.3s ease;
        }

        .filter-group select:focus {
            outline: none;
            border-color: #111;
        }

        .results-info {
            text-align: center;
            margin: 1rem 0;
            color: #666;
            font-size: 14px;
            min-height: 20px;
        }

        .loading-overlay {
            text-align: center;
            padding: 3rem;
            color: #999;
        }

        .loading-overlay i {
            font-size: 32px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .product-card {
            animation: fadeIn 0.4s ease;
        }
      
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .filter-form {
                flex-direction: column;
            }
            
            .filter-group {
                width: 100%;
                max-width: 100%;
            }
        }
        .cart-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #ff4444;
    color: white;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    font-size: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}
    </style>
</head>
<body>
<?php include 'search-overlay.html'; ?>
<nav class="main-navbar">

    <div class="logo">
        <a class="logo" href="index.php">Âme</a>
    </div>

    <div class="hamburger" id="hamburger">
        <div></div>
        <div></div>
        <div></div>
    </div>

    <?php 
        $cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; 
    ?>

    <div class="nav-icons">
        <i class="fas fa-search" id="search-icon"></i>

        <a href="cart.php" style="color: inherit; text-decoration: none; position: relative;">
            <i class="fas fa-shopping-bag"></i>

            <?php if ($cart_count > 0): ?>
                <span class="cart-badge"><?= $cart_count ?></span>
            <?php endif; ?>
        </a>
    </div>

</nav>

    <!-- Lower Navigation Links -->
    <div class="nav-links" id="navLinks">
        <a href="bracelets.php">Bracelets</a>
        <a href="necklaces.php">Necklaces</a>
        <a href="rings.php">Rings</a>
        <a href="earrings.php">Earrings</a>
        <a href="order_tracking.php">Track your order</a>
        <a href="aboutus.php">About Us</a>
        <a href="contactus.php">Contact Us</a>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="filter-form">
            <div class="filter-group">
                <label for="category">
                    <i class="fas fa-tag"></i> Category
                </label>
                <select id="category">
                    <option value="all">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="price_range">
                    <i class="fas fa-pound-sign"></i> Price Range
                </label>
                <select id="price_range">
                    <option value="all">All Prices</option>
                    <option value="300">< £300</option>
                    <option value="500">< £500</option>
                    <option value="800">< £800</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="price_sort">
                    <i class="fas fa-sort-amount-down"></i> Sort by Price
                </label>
                <select id="price_sort">
                    <option value="default">Default</option>
                    <option value="low">Price: Low to High</option>
                    <option value="high">Price: High to Low</option>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Results Info -->
    <div id="results-info" class="results-info"></div>

    <!-- Products Container -->
    <div class="products-container" id="products-container">
        <div class="loading-overlay">
            <i class="fas fa-spinner"></i>
            <p>Loading products...</p>
        </div>
    </div>

    <footer class="site-footer">
    <div class="footer-container">
        
        <!-- Company Info -->
        <div class="footer-section">
            <h3>About Us</h3>
            <p>jewelry crafted not just to be worn, but to be felt - a reflection of inner strength, grace, and self-expression. Find more <a href="aboutus.php">here</a></p>
            <p><strong>Address:</strong> Jewelry Street 13, London, UK</p>
            <p><strong>Email:</strong> support@ame-jewelry.com</p>
            <p><strong>Phone:</strong> +44 1234 567890</p>
        </div>

        <!-- Customer Service -->
        <div class="footer-section">
            <h3>Customer Service</h3>
            <ul>
                <li><a href="contactus.php">Contact Us</a></li>
                <li><a href="returns-policy.php">Returns & Refunds</a></li>
                <li><a href="shipping-policy.php">Shipping Information</a></li>
                <li><a href="order_tracking.php">Track Your Order</a></li>
            </ul>
        </div>

        <!-- Legal Pages -->
        <div class="footer-section">
            <h3>Legal & Privacy</h3>
            <ul>
                <li><a href="privacy-policy.php">Privacy Policy</a></li>
                <li><a href="cookie-policy.php">Cookie Policy</a></li>
                <li><a href="#" onclick="openCookieSettings()">Cookie Preferences</a></li>
            </ul>
        </div>

        <!-- Social Media -->
        <div class="footer-section">
            <h3>Follow Us</h3>
            <div class="footer-social">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-tiktok"></i></a>
            </div>
        </div>

    </div>

    <div class="footer-bottom">
        <p>&copy; <?php echo date("Y"); ?> Âme Jewelry Ltd. All Rights Reserved.</p>
    </div>
</footer>

    <script>
        // Filter elements
        const categorySelect = document.getElementById('category');
        const priceRangeSelect = document.getElementById('price_range');
        const priceSortSelect = document.getElementById('price_sort');
        const productsContainer = document.getElementById('products-container');
        const resultsInfo = document.getElementById('results-info');

        // Load products on page load
        loadProducts();

        // Add event listeners for instant filtering
        categorySelect.addEventListener('change', loadProducts);
        priceRangeSelect.addEventListener('change', loadProducts);
        priceSortSelect.addEventListener('change', loadProducts);

        function loadProducts() {
            // Show loading state
            productsContainer.innerHTML = '<div class="loading-overlay"><i class="fas fa-spinner"></i><p>Loading products...</p></div>';
            
            // Get filter values
            const category = categorySelect.value;
            const priceRange = priceRangeSelect.value;
            const priceSort = priceSortSelect.value;
            
            // Build URL with filters
            const url = `?ajax=1&category=${category}&price_range=${priceRange}&price_sort=${priceSort}`;
            
            // Fetch filtered products
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayProducts(data.products);
                        updateResultsInfo(data.count, category, priceRange);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    productsContainer.innerHTML = '<p style="text-align:center; padding: 3rem; color: #999;">Error loading products</p>';
                });
        }

        function displayProducts(products) {
            if (products.length === 0) {
                productsContainer.innerHTML = '<p style="text-align:center; padding: 3rem; color: #999;">No products available matching your criteria.</p>';
                return;
            }
            
            let html = '';
            products.forEach(product => {
                const imageSrc = product.image ? escapeHtml(product.image) : 'placeholder.jpg';
                const imageAlt = product.image ? escapeHtml(product.name) : 'No product photo';
                
                html += `
                    <div class="product-card">
                        <div class="product-image">
                            <img src="${imageSrc}" alt="${imageAlt}">
                        </div>
                        <div class="product-info" style="margin-bottom: 10px;">
                            <h3>${escapeHtml(product.name)}</h3>
                            <p class="price" style="padding: 6px;">£${parseFloat(product.price).toFixed(2)}</p>
                            <a href="product.php?id=${product.id}" class="shop-btn" style="text-decoration: none; margin-bottom: 5px;">view details</a>
                        </div>
                    </div>
                `;
            });
            
            productsContainer.innerHTML = html;
        }

        function updateResultsInfo(count, category, priceRange) {
            let info = `Showing ${count} product${count !== 1 ? 's' : ''}`;
            
            if (category !== 'all') {
                info += ` in <strong>${escapeHtml(category)}</strong>`;
            }
            
            if (priceRange !== 'all') {
                info += ` • Below <strong>£${priceRange}</strong>`;
            }
            
            resultsInfo.innerHTML = info;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        

    </script>

</body>
</html>