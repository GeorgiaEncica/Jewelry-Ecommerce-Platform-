<?php

include 'live_visitors.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Luxury Jewelry Store</title>
    <script src="script.js" defer></script>
    <link rel="stylesheet" href="style.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    
</head>

<body>


    <?php include 'search-overlay.html'; ?>
    <!-- Main Navbar -->
    <nav class="main-navbar">

        <div class="logo"><a class="logo" href="index.php">Âme</a></div>

        <!-- Hamburger Menu -->
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



</style>

        <!-- Search Bar -->
        <div class="search-overlay" id="search-overlay">
            <div class="search-container">
                <div class="search-header">
                    <input type="text" id="search-input" placeholder="Search for products, categories, or pages..."
                        autocomplete="off">
                    <i class="fas fa-times" id="close-search"></i>
                </div>
                <div class="search-results" id="search-results"></div>
            </div>
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

    <div class="slideshow">

        <div class="slide">
            <img src="images/sabrianna-2z7MxnXQs3k-unsplash.jpg" alt="Slide 1">
            <div class="overlay"></div>
            <div class="text-content">
                <h1>An Exclusive 25% Privilege Awaits</h1>
                <a class="cta-btn" href="allproducts.php">Learn More</a>
            </div>
        </div>

        <div class="slide">
            <img src="images/kotryna-juskaite-dlXBaYIQ5nY-unsplash.jpg" alt="Slide 2">
            <div class="overlay"></div>
            <div class="text-content">
                <h1>Where Confidence Meets the Brilliance of 18K Gold.</h1>
                <a href="allproducts.php" class="cta-btn">Shop Now</a>
            </div>
        </div>

        <div class="dots">
            <span class="dot"></span>
            <span class="dot"></span>
        </div>
    </div>

    <div class="featured-product">

        <div class="productimg">
            <img src="images/sabrianna-9xJ5s00mCzY-unsplash.jpg" alt="">
        </div>

        <div class="productinfo">
            <h1>Featured Product</h1>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Delectus veritatis eligendi rem corporis
                sapiente adipisci tempora quae voluptates, deserunt reprehenderit debitis, eos accusamus nesciunt
                impedit ab molestiae nostrum dolore ipsa?</p>
            <button>Shop Now</button>
        </div>

    </div>

    <h1 class="jewelry_categories_heading">Jewelry Categories</h1>

    <div class="jewelry_categories">

        <div class="category">
            <a href="necklaces.php">
                <img src="images/sabrianna-AhIQL2CKq7g-unsplash.jpg" alt="">
            </a>
            <div class="overlay-text">Necklaces</div>
        </div>

        <div class="category">
            <a href="bracelets.php">
                <img src="images/sabrianna-2z7MxnXQs3k-unsplash.jpg" alt="">
            </a>

            <div class="overlay-text">Bracelets</div>
        </div>

        <div class="category">
            <a href="earrings.php">
                <img src="images/sabrianna-Lqfqsij4EvQ-unsplash.jpg" alt="">
            </a>

            <div class="overlay-text">Earrings</div>
        </div>

        <div class="category">
            <a href="rings.php">
                <img src="images/sabrianna-CCpQ12CZ2Pc-unsplash.jpg" alt="">
            </a>

            <div class="overlay-text">Rings</div>
        </div>

    </div>

    <div class="slide2">
        <img src="images/natalie-sysko-HJHWDLUIV5w-unsplash.jpg" alt="">
        <img src="images/sabrianna-Eulnh2kzUR0-unsplash.jpg" alt="">
    </div>

    <h1 class="card_3product_h1">One Ring, One Love</h1>
    <div class="card_3product">
        <img src="images/sabrianna-9xJ5s00mCzY-unsplash-removebg-preview.png" alt="">
        <img src="images/sabrianna-9xJ5s00mCzY-unsplash-removebg-preview.png" alt="">
        <img src="images/sabrianna-9xJ5s00mCzY-unsplash-removebg-preview.png" alt="">
    </div>


    <h1 class="collapsible_h1">Why Choose Us?</h1>
    <div class="collapsible_content">
        <button type="button" class="collapsible">Express Delivery</button>
        <div class="content">
            <p>Luxury, delivered to your door with fully insured shipping across the globe.</p>
        </div>
        <button type="button" class="collapsible">5-year warranty </button>
        <div class="content">
            <p>Your jewelry is made to last a lifetime. Our 5-year warranty reflects our unwavering confidence in the
                enduring quality of every design.</p>
        </div>
        <button type="button" class="collapsible">Premium Materials</button>
        <div class="content">
            <p>We work exclusively with ethically sourced 18K gold, silver and conflict-free diamonds, ensuring every
                piece embodies purity, brilliance, and integrity.</p>
        </div>
        <button type="button" class="collapsible">Lifetime Cleaning & Maintenance</button>
        <div class="content">
            <p>Return your jewelry anytime for complimentary professional cleaning and inspection.</p>
        </div>
    </div>


<!-- Cookie Banner -->
<div id="cookieBanner">
    <div class="cookie-content">
        <div class="cookie-text">
            <h3><i class="fas fa-cookie-bite"></i> We Value Your Privacy</h3>
            <p>
                We use cookies to enhance your browsing experience, analyze site traffic, and personalize content.
                By clicking "Accept All", you consent to our use of cookies.
                <a href="cookie-policy.php">Learn more</a>
            </p>
        </div>

        <div class="cookie-actions">
            <button class="cookie-btn btn-settings" onclick="openCookieSettings()">Customize</button>
            <button class="cookie-btn btn-accept-all" onclick="acceptAll()">Accept All</button>
        </div>
    </div>
</div>

<!-- Cookie Settings Modal -->
<div id="cookieModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Cookie Preferences</h2>
            <p>Choose which cookies you want to accept</p>
        </div>

        <div class="modal-body">
            <!-- Cookie Options -->
            <div class="cookie-category">
                <div class="category-header">
                    <h3>Necessary Cookies</h3>
                    <label class="toggle-switch">
                        <input type="checkbox" checked disabled>
                        <span class="slider"></span>
                    </label>
                </div>
                <p class="category-description">
                    These cookies are essential for the website to function properly.
                </p>
            </div>

            <div class="cookie-category">
                <div class="category-header">
                    <h3>Analytics Cookies</h3>
                    <label class="toggle-switch">
                        <input type="checkbox" id="analyticsCookies" checked>
                        <span class="slider"></span>
                    </label>
                </div>
            </div>

            <div class="cookie-category">
                <div class="category-header">
                    <h3>Marketing Cookies</h3>
                    <label class="toggle-switch">
                        <input type="checkbox" id="marketingCookies" checked>
                        <span class="slider"></span>
                    </label>
                </div>
            </div>

            <div class="cookie-category">
                <div class="category-header">
                    <h3>Preference Cookies</h3>
                    <label class="toggle-switch">
                        <input type="checkbox" id="preferenceCookies" checked>
                        <span class="slider"></span>
                    </label>
                </div>
            </div>

        </div>

        <div class="modal-footer">
            <button class="cookie-btn btn-necessary" onclick="savePreferences()">Save Preferences</button>
            <button class="cookie-btn btn-accept-all" onclick="acceptAll()">Accept All</button>
        </div>
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

</body>
<script>

document.addEventListener('DOMContentLoaded', function() {
    const banner = document.getElementById('cookieBanner');
    const modal = document.getElementById('cookieModal');

    // Show banner if no consent exists
    if (!getCookie('cookie_consent')) {
        setTimeout(() => {
            if (banner) banner.classList.add('show');
        }, 800);
    }

    // Close modal when clicking outside
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) modal.classList.remove('show');
        });
    }

    // Button functions
    window.acceptAll = function() {
        setCookie('cookie_consent', 'all', 365);
        setCookie('analytics_consent', 'true', 365);
        setCookie('marketing_consent', 'true', 365);
        setCookie('preference_consent', 'true', 365);
        closeBanner();
    };

    window.acceptNecessary = function() {
        setCookie('cookie_consent', 'necessary', 365);
        setCookie('analytics_consent', 'false', 365);
        setCookie('marketing_consent', 'false', 365);
        setCookie('preference_consent', 'false', 365);
        closeBanner();
    };

    window.openCookieSettings = function() {
        if (modal) modal.classList.add('show');
    };

    window.savePreferences = function() {
        setCookie('cookie_consent', 'custom', 365);
        setCookie('analytics_consent', document.getElementById('analyticsCookies')?.checked || false, 365);
        setCookie('marketing_consent', document.getElementById('marketingCookies')?.checked || false, 365);
        setCookie('preference_consent', document.getElementById('preferenceCookies')?.checked || false, 365);
        closeBanner();
        if (modal) modal.classList.remove('show');
    };

    function closeBanner() {
        if (banner) banner.classList.remove('show');
        if (modal) modal.classList.remove('show');
    }

    // Cookie helpers
    function setCookie(name, value, days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = `${name}=${value};expires=${date.toUTCString()};path=/;SameSite=Lax`;
    }

    function getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for (let c of ca) {
            c = c.trim();
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length);
        }
        return null;
    }
});

// Set initial values from cookies/localStorage
document.addEventListener('DOMContentLoaded', function() {
    const lang = localStorage.getItem('selectedLanguage');
    const currency = localStorage.getItem('selectedCurrency');

    if(lang) document.getElementById('languageSelect').value = lang;
    if(currency) document.getElementById('currencySelect').value = currency;
});



</script>

</html>