<?php


include 'live_visitors.php';

// Connect to database
$conn = new mysqli("localhost", "root", "", "jewelry_database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get product ID from URL
if (!isset($_GET['id'])) {
    die("No product selected.");
}
$product_id = intval($_GET['id']);

// Fetch product info
$stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Product not found.");
}
$product = $result->fetch_assoc();
$stmt->close();

// Fetch all images
$stmt_img = $conn->prepare("SELECT * FROM product_images WHERE product_id=?");
$stmt_img->bind_param("i", $product_id);
$stmt_img->execute();
$images_result = $stmt_img->get_result();
$images = [];
while ($row = $images_result->fetch_assoc()) {
    $images[] = $row['image_path'];
}
$stmt_img->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($product['name']) ?> - Âme Jewelry</title>
<script src="script.js" defer></script>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
   
.product-page {
    display: flex;
    flex-wrap: wrap;
    max-width: 1000px;
    margin: 3rem auto;
    gap: 2rem;
}
.product-images {
    flex: 1;
    min-width: 320px;
}
.main-image img {
    width: 100%;
    max-width: 500px;
    max-height: 500px;
    object-fit: cover;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.thumbnail-container {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
    flex-wrap: wrap;
}
.thumbnail-container img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    cursor: pointer;
    border: 2px solid transparent;
    border-radius: 6px;
    transition: 0.2s ease;
}
.thumbnail-container img:hover {
    transform: scale(1.1);
}
.product-details {
    flex: 1;
    min-width: 300px;
}
.product-details h1 {
    font-family: "Abril Fatface", serif;
    font-style: italic;
    font-size: 2.5rem;
    margin-bottom: 1rem;
}
.product-details .price {
    font-weight: bold;
    margin-bottom: 1rem;
}


 /* ADD TO CART */
 .add-to-cart-btn {
    padding: 0.8rem 1.5rem;
    background: #111;
    color: white;
    border: none;
    border-radius: 25px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
    margin-top: 10px;
}

.add-to-cart-btn:hover {
    background: white;
    color:black;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.size-selector,
.quantity-selector {
    margin-bottom: 1rem;
}

.size-selector label,
.quantity-selector label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.size-selector select,
.quantity-selector input {
    width: 100%;
    padding: 0.7rem;
    border: 1px solid #ccc;
    border-radius: 8px;
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
@media (max-width: 780px){
    .product-page{
        padding: 0 2rem;
    }
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

<div class="product-page">
    <div class="product-images">
        <div class="main-image">
            <img id="mainImage" src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
        </div>
        <div class="thumbnail-container">
            <?php foreach ($images as $img): ?>
                <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            <?php endforeach; ?>
        </div>
    </div>

    <div class="product-details">
    <h1><?= htmlspecialchars($product['name']) ?></h1>
    <p class="price">£<?= number_format($product['price'], 2) ?></p>
    <p style="margin-bottom: 15px;"><?= nl2br(htmlspecialchars($product['description'])) ?></p>

    <?php if (stripos($product['category'], 'ring') !== false): ?>
        <!-- Ring Product: show size options -->
        <form method="POST" action="add_to_cart.php">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            
            <!-- Size Selector -->
            <div class="size-selector">
                <label>Select Size:</label>
                <select name="size" required>
                    <option value="">Choose size</option>
                    <?php
                    $stmt = $conn->prepare("SELECT size, stock FROM product_inventory WHERE product_id = ? AND stock > 0");
                    $stmt->bind_param("i", $product['id']);
                    $stmt->execute();
                    $sizes_result = $stmt->get_result();
                    while ($size_row = $sizes_result->fetch_assoc()):
                    ?>
                    
                        <option value="<?= htmlspecialchars($size_row['size']) ?>">
                            Size <?= htmlspecialchars($size_row['size']) ?> (<?= $size_row['stock'] ?> available)
                        </option>

                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Quantity Selector -->
            <div class="quantity-selector">
                <label>Quantity:</label>
                <input type="number" name="quantity" value="1" min="1" required>
            </div>

            <button type="submit" class="add-to-cart-btn">
                <i class="fas fa-shopping-cart"></i> Add to Cart
            </button>
        </form>

    <?php else: ?>
        <!-- Non-ring products (no size needed) -->
        <form method="POST" action="add_to_cart.php">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            <input type="number" name="quantity" value="1" min="1" required>
            <button type="submit" class="add-to-cart-btn">
                <i class="fas fa-shopping-cart"></i> Add to Cart
            </button>
        </form>
    <?php endif; ?>
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
// Thumbnail click switches main image
const mainImage = document.getElementById("mainImage");
document.querySelectorAll(".thumbnail-container img").forEach(thumbnail => {
    thumbnail.addEventListener("click", () => {
        mainImage.src = thumbnail.src;
    });
});


    // Global search functionality - works on all pages
    (function () {

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSearch);
} else {
    initSearch();
}

function initSearch() {
    // Get elements
    const searchIcon = document.getElementById('search-icon');
    const searchOverlay = document.getElementById('search-overlay');
    const closeSearch = document.getElementById('close-search');
    const searchInput = document.getElementById('search-input');
    const searchResults = document.getElementById('search-results');

    if (!searchIcon || !searchOverlay) return;

    // Open search overlay
    searchIcon.addEventListener('click', function () {
        searchOverlay.classList.add('active');
        searchInput.focus();
    });

    // Close search overlay
    closeSearch.addEventListener('click', function () {
        searchOverlay.classList.remove('active');
        searchInput.value = '';
        searchResults.innerHTML = '';
    });

    // Close search on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && searchOverlay.classList.contains('active')) {
            searchOverlay.classList.remove('active');
            searchInput.value = '';
            searchResults.innerHTML = '';
        }
    });

    // Close search when clicking outside
    searchOverlay.addEventListener('click', function (e) {
        if (e.target === searchOverlay) {
            searchOverlay.classList.remove('active');
            searchInput.value = '';
            searchResults.innerHTML = '';
        }
    });

    // Search input with debounce
    let searchTimeout;
    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length === 0) {
            searchResults.innerHTML = '';
            return;
        }

        searchTimeout = setTimeout(function () {
            performSearch(query);
        }, 300);
    });

    function performSearch(query) {
        searchResults.innerHTML = '<div class="search-loading"><i class="fas fa-spinner"></i><p>Searching...</p></div>';

        fetch('search.php?q=' + encodeURIComponent(query))
            .then(response => response.json())
            .then(data => {
                displaySearchResults(data, query);
            })
            .catch(error => {
                console.error('Error:', error);
                searchResults.innerHTML = '<div class="no-results"><i class="fas fa-exclamation-triangle"></i><p>Error performing search</p></div>';
            });
    }

    function displaySearchResults(data, query) {
        let html = '';
        let hasResults = false;

        // Display Products
        if (data.products && data.products.length > 0) {
            hasResults = true;
            html += '<div class="search-category">';
            html += '<div class="search-category-title">Products</div>';
            data.products.forEach(function (product) {
                const imageSrc = product.image ? escapeHtml(product.image) : 'placeholder.jpg';
                html += '<a href="product.php?id=' + product.id + '" class="search-item">';
                html += '<img src="' + imageSrc + '" alt="' + escapeHtml(product.name) + '" class="search-item-image">';
                html += '<div class="search-item-info">';
                html += '<div class="search-item-name">' + escapeHtml(product.name) + '</div>';
                html += '<div class="search-item-details">' + escapeHtml(product.category) + ' • £' + parseFloat(product.price).toFixed(2) + '</div>';
                html += '</div>';
                html += '</a>';
            });
            html += '</div>';
        }

        // Display Categories
        if (data.categories && data.categories.length > 0) {
            hasResults = true;
            html += '<div class="search-category">';
            html += '<div class="search-category-title">Categories</div>';
            data.categories.forEach(function (category) {
                const categoryUrl = getCategoryUrl(category.category);
                html += '<a href="' + categoryUrl + '" class="search-item">';
                html += '<div class="search-item-icon"><i class="fas fa-tag"></i></div>';
                html += '<div class="search-item-info">';
                html += '<div class="search-item-name">' + escapeHtml(category.category) + '</div>';
                html += '<div class="search-item-details">' + category.count + ' product' + (category.count !== 1 ? 's' : '') + '</div>';
                html += '</div>';
                html += '</a>';
            });
            html += '</div>';
        }

        // Display Pages
        if (data.pages && data.pages.length > 0) {
            hasResults = true;
            html += '<div class="search-category">';
            html += '<div class="search-category-title">Pages</div>';
            data.pages.forEach(function (page) {
                html += '<a href="' + escapeHtml(page.url) + '" class="search-item">';
                html += '<div class="search-item-icon"><i class="' + escapeHtml(page.icon) + '"></i></div>';
                html += '<div class="search-item-info">';
                html += '<div class="search-item-name">' + escapeHtml(page.title) + '</div>';
                html += '<div class="search-item-details">' + escapeHtml(page.description) + '</div>';
                html += '</div>';
                html += '</a>';
            });
            html += '</div>';
        }

        if (!hasResults) {
            html = '<div class="no-results">';
            html += '<i class="fas fa-search"></i>';
            html += '<p>No results found for "' + escapeHtml(query) + '"</p>';
            html += '</div>';
        }

        searchResults.innerHTML = html;
    }

    function getCategoryUrl(category) {
        const categoryMap = {
            'Bracelets': 'bracelets.php',
            'Necklaces': 'necklaces.php',
            'Rings': 'rings.php',
            'Earrings': 'earrings.php'
        };
        return categoryMap[category] || 'allproducts.php?category=' + encodeURIComponent(category);
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}
})();
</script>

</body>
</html>
<?php

$conn->close();
?>
