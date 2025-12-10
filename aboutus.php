
<?php include 'live_visitors.php'; ?>
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
@media (max-width: 900px){
      .about-container{
        padding: 2rem;
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

    <div class="about-container">

    <h1 class="aboutus_heading">Our Story</h1>
    <p class="intro">
        Born from a passion for timeless elegance, <strong>
            Âme</strong> was founded with a singular vision - to
        celebrate the
        beauty of individuality through the art of fine jewelry. Our name, Âme, meaning “soul,” reflects what we
        stand for: jewelry crafted not just to be worn, but to be felt - a reflection of inner strength, grace,
        and
        self-expression. </p>

    <img class="ourstory_img1" src="images/pexels-olly-1050312.jpg" alt="">
    <h1 class="craftmanship_h1">A Legacy of Craftsmanship</h1>
    <p class="craftmanship_p">Each piece begins as an idea, meticulously shaped by our master artisans who blend
        traditional
        techniques with
        contemporary design.
        From the first sketch to the final polish, we honor the artistry of human touch - ensuring that every ring,
        necklace, and bracelet carries with it the precision and passion of its creator.

        We work exclusively with ethically sourced 18K gold and conflict-free diamonds, chosen for their brilliance and
        integrity. Every detail is a commitment to quality that endures.</p>
    <img class="ourstory_img2" src="images/pexels-ozgur-ozkan-288576-848205.jpg" alt="">

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

</script>