<?php

include 'live_visitors.php';

$conn = new mysqli("localhost", "root", "", "jewelry_database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle AJAX requests (filtering)
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    $price_sort = $_GET['price_sort'] ?? '';
    $price_range = $_GET['price_range'] ?? '';

    // Base query — only necklaces
    $sql = "
        SELECT p.id, p.name, p.description, p.price, p.category,
               (SELECT image_path FROM product_images WHERE product_id = p.id LIMIT 1) AS image
        FROM products p
        WHERE p.category = 'Necklaces'
    ";

    // Apply price range
    if ($price_range === '300') $sql .= " AND p.price < 300";
    if ($price_range === '500') $sql .= " AND p.price < 500";
    if ($price_range === '800') $sql .= " AND p.price < 800";

    // Apply sorting
    if ($price_sort === 'low') $sql .= " ORDER BY p.price ASC";
    elseif ($price_sort === 'high') $sql .= " ORDER BY p.price DESC";
    else $sql .= " ORDER BY p.id DESC";

    $result = $conn->query($sql);
    $products = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'products' => $products, 'count' => count($products)]);
    $conn->close();
    exit;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Necklaces | Âme Jewelry</title>
<script src="script.js" defer></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="style.css" />
<style>


body {
    background-color: #f9f9f9;
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
}

/* Navbar */
.main-navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 2rem;
    background: white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}
.main-navbar .logo a {
    font-family: 'Abril Fatface', serif;
    font-size: 1.8rem;
    color: #000;
    text-decoration: none;
}
     

/* Category Title */
.category-title {
    text-align: center;
    font-size: 2.4rem;
    margin-top: 2rem;
    font-family: 'Abril Fatface', serif;
    font-style: italic;
}

/* Filter Section */
.filter-section {
    background: white;
    padding: 1.5rem 2rem;
    margin: 2rem auto;
    max-width: 1200px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.filter-form {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
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
    font-size: 13px;
    text-transform: uppercase;
    color: #333;
}
.filter-group select {
    width: 100%;
    padding: 0.7rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

/* Misc */
.results-info {
    text-align: center;
    margin-bottom: 1rem;
    color: #666;
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
<h1 class="category-title">Necklaces Collection</h1>

<!-- Filters -->
<div class="filter-section">
    <div class="filter-form">
        <div class="filter-group">
            <label for="price_range"><i class="fas fa-pound-sign"></i> Price Range</label>
            <select id="price_range">
                <option value="all">All Prices</option>
                <option value="300">< £300</option>
                <option value="500">< £500</option>
                <option value="800">< £800</option>
            </select>
        </div>

        <div class="filter-group">
            <label for="price_sort"><i class="fas fa-sort-amount-down"></i> Sort by Price</label>
            <select id="price_sort">
                <option value="default">Default</option>
                <option value="low">Price: Low to High</option>
                <option value="high">Price: High to Low</option>
            </select>
        </div>
    </div>
</div>

<div id="results-info" class="results-info"></div>

<div class="products-container" id="products-container">
    <div class="loading-overlay"><i class="fas fa-spinner"></i><p>Loading Necklaces...</p></div>
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

</body>
<script>
const priceRange = document.getElementById('price_range');
const priceSort = document.getElementById('price_sort');
const productsContainer = document.getElementById('products-container');
const resultsInfo = document.getElementById('results-info');

// Load products initially
loadProducts();
priceRange.addEventListener('change', loadProducts);
priceSort.addEventListener('change', loadProducts);

function loadProducts() {
    productsContainer.innerHTML = '<div class="loading-overlay"><i class="fas fa-spinner"></i><p>Loading Necklaces...</p></div>';
    const url = `?ajax=1&price_range=${priceRange.value}&price_sort=${priceSort.value}`;
    fetch(url)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                displayProducts(data.products);
                resultsInfo.innerHTML = `Showing ${data.count} necklace${data.count !== 1 ? 's' : ''}`;
            }
        })
        .catch(err => {
            console.error(err);
            productsContainer.innerHTML = '<p style="text-align:center;color:#999;">Error loading products.</p>';
        });
}

function displayProducts(products) {
    if (!products.length) {
        productsContainer.innerHTML = '<p style="text-align:center;color:#999;">No necklaces found.</p>';
        return;
    }
    productsContainer.innerHTML = products.map(p => `
        <div class="product-card">
            <div class="product-image">
                <img src="${p.image || 'placeholder.jpg'}" alt="${p.name}">
            </div>
            <div class="product-info">
                <h3>${p.name}</h3>
                <p class="price">£${parseFloat(p.price).toFixed(2)}</p>
                <a href="product.php?id=${p.id}" class="shop-btn">View Details</a>
            </div>
        </div>
    `).join('');
}

</script>

</html>
