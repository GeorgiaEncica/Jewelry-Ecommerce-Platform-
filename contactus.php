<?php


include 'live_visitors.php';

// Initialize variables for feedback messages
$successMsg = '';
$errorMsg = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $name = htmlspecialchars(strip_tags(trim($_POST["name"])));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars(strip_tags(trim($_POST["message"])));

    // Recipient email
    $to = "georgiaencica03@gmail.com"; // Replace with your email
    $subject = "New Contact Form Submission from Ame";

    // Email content
    $body = "Name: $name\n";
    $body .= "Email: $email\n\n";
    $body .= "Message:\n$message\n";

    // Email headers
    $headers = "From: $name <$email>\r\n";
    $headers .= "Reply-To: $email\r\n";

    // Send email
    if (mail($to, $subject, $body, $headers)) {
        $successMsg = "Thank you, your message has been sent successfully!";
    } else {
        $errorMsg = "Oops! Something went wrong. Please try again later.";
    }
}
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
        h2{
        font-family: "Abril Fatface", serif;
        font-style: italic;
        font-size: 36px;
        margin-bottom: 1rem;
        font-weight: 600;
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

    <section class="contact-section">
        <h2>Contact Us</h2>
        <p>We’d love to hear from you. Whether you have a question about our collections, custom designs, or your order
            - our team is here to help.</p>

        <form class="contact-form" action="#" method="post">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" placeholder="Your name" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Your email" required>
            </div>

            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" placeholder="How can we assist you?" rows="5" required></textarea>
            </div>

            <button type="submit" class="formsubmit">Send Message</button>
        </form>
        <?php if($successMsg): ?>
        <p class="feedback success">
            <?php echo $successMsg; ?>
        </p>
        <?php elseif($errorMsg): ?>
        <p class="feedback error">
            <?php echo $errorMsg; ?>
        </p>
        <?php endif; ?>
    </section>

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
