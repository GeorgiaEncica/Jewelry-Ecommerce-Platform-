<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cookie Policy - Âme</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background: #f7f7f7;
            font-family: "Poppins", sans-serif;
        }

        .policy-container {
            max-width: 900px;
            margin: 3rem auto;
            padding: 0 2rem;
        }

        .policy-card {
            background: white;
            border-radius: 12px;
            padding: 3rem;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }

        .policy-header {
            text-align: center;
            margin-bottom: 3rem;
            padding-bottom: 2rem;
            border-bottom: 2px solid #eee;
        }

        .policy-header h1 {
            font-family: "Abril Fatface", serif;
            font-style: italic;
            font-size: 36px;
            margin-bottom: 0.5rem;
        }

        .policy-header .last-updated {
            color: #666;
            font-size: 14px;
        }

        .policy-content h2 {
            font-size: 24px;
            margin-top: 2rem;
            margin-bottom: 1rem;
            color: #111;
        }

        .policy-content h3 {
            font-size: 18px;
            margin-top: 1.5rem;
            margin-bottom: 0.8rem;
            color: #333;
        }

        .policy-content p {
            line-height: 1.8;
            color: #555;
            margin-bottom: 1rem;
        }

        .policy-content ul, .policy-content ol {
            margin: 1rem 0;
            padding-left: 2rem;
        }

        .policy-content li {
            margin-bottom: 0.5rem;
            line-height: 1.6;
            color: #555;
        }

        .cookie-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5rem 0;
        }

        .cookie-table th,
        .cookie-table td {
            padding: 1rem;
            text-align: left;
            border: 1px solid #ddd;
        }

        .cookie-table th {
            background: #f5f5f5;
            font-weight: 600;
        }

        .highlight-box {
            background: #f0f7ff;
            border-left: 4px solid #2196f3;
            padding: 1.5rem;
            margin: 1.5rem 0;
            border-radius: 4px;
        }

        .contact-section {
            background: #f9f9f9;
            padding: 2rem;
            border-radius: 8px;
            margin-top: 3rem;
        }

        @media (max-width: 768px) {
            .policy-card {
                padding: 2rem 1.5rem;
            }

            .policy-header h1 {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>

    <!-- Main Navbar -->
    <nav class="main-navbar">
        <div class="logo"><a class="logo" href="index.php">Âme</a></div>
        <div class="hamburger" id="hamburger">
            <div></div><div></div><div></div>
        </div>
        <div class="nav-icons">
            <i class="fas fa-search" id="search-icon"></i>
            <a href="cart.php" style="color: inherit; text-decoration: none;">
                <i class="fas fa-shopping-bag"></i>
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

    <div class="policy-container">
        <div class="policy-card">
            <div class="policy-header">
                <h1><i class="fas fa-cookie-bite"></i> Cookie Policy</h1>
                <p class="last-updated">Last Updated: December 9, 2025</p>
            </div>

            <div class="policy-content">
                <h2>1. Introduction</h2>
                <p>
                    This Cookie Policy explains how Âme ("we", "us", or "our") uses cookies and similar technologies 
                    to recognize you when you visit our website. It explains what these technologies are, why we use 
                    them, and your rights to control our use of them.
                </p>

                <h2>2. What Are Cookies?</h2>
                <p>
                    Cookies are small text files that are placed on your computer or mobile device when you visit a 
                    website. They are widely used to make websites work more efficiently and provide information to 
                    website owners.
                </p>
                <p>
                    Cookies set by the website owner are called "first-party cookies." Cookies set by parties other 
                    than the website owner are called "third-party cookies." Third-party cookies enable features or 
                    functionality to be provided on or through the website (e.g., analytics and advertising).
                </p>

                <h2>3. Types of Cookies We Use</h2>

                <h3>3.1 Necessary Cookies</h3>
                <p>
                    These cookies are essential for the website to function properly. They enable basic functions 
                    like page navigation, access to secure areas, and shopping cart functionality. The website cannot 
                    function properly without these cookies.
                </p>

                <table class="cookie-table">
                    <thead>
                        <tr>
                            <th>Cookie Name</th>
                            <th>Purpose</th>
                            <th>Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>PHPSESSID</td>
                            <td>Maintains your session and shopping cart</td>
                            <td>Session</td>
                        </tr>
                        <tr>
                            <td>cookie_consent</td>
                            <td>Stores your cookie preferences</td>
                            <td>1 year</td>
                        </tr>
                    </tbody>
                </table>

                <h3>3.2 Analytics Cookies</h3>
                <p>
                    These cookies help us understand how visitors interact with our website by collecting and 
                    reporting information anonymously. This helps us improve our website's performance and user 
                    experience.
                </p>

                <table class="cookie-table">
                    <thead>
                        <tr>
                            <th>Cookie Name</th>
                            <th>Purpose</th>
                            <th>Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>_ga</td>
                            <td>Google Analytics - distinguishes users</td>
                            <td>2 years</td>
                        </tr>
                        <tr>
                            <td>_gid</td>
                            <td>Google Analytics - distinguishes users</td>
                            <td>24 hours</td>
                        </tr>
                    </tbody>
                </table>

                <h3>3.3 Marketing Cookies</h3>
                <p>
                    These cookies are used to track visitors across websites. The intention is to display ads that 
                    are relevant and engaging for individual users, thereby making them more valuable for publishers 
                    and third-party advertisers.
                </p>

                <h3>3.4 Preference Cookies</h3>
                <p>
                    These cookies enable the website to remember information that changes the way the website behaves 
                    or looks, such as your preferred language or the region you are in.
                </p>

                <h2>4. How We Use Cookies</h2>
                <p>We use cookies for the following purposes:</p>
                <ul>
                    <li>To enable the proper functioning of the website</li>
                    <li>To maintain your shopping cart and checkout process</li>
                    <li>To remember your preferences and settings</li>
                    <li>To analyze how visitors use our website</li>
                    <li>To improve our website performance and user experience</li>
                    <li>To deliver relevant advertisements</li>
                    <li>To prevent fraud and enhance security</li>
                </ul>

                <h2>5. Third-Party Cookies</h2>
                <p>
                    In addition to our own cookies, we may use various third-party cookies to report usage statistics 
                    of the website, deliver advertisements, and so on. These include:
                </p>
                <ul>
                    <li><strong>Google Analytics:</strong> For website analytics and performance monitoring</li>
                    <li><strong>Payment Processors:</strong> For secure payment processing</li>
                    <li><strong>Social Media Platforms:</strong> For social sharing and login functionality</li>
                </ul>

                <div class="highlight-box">
                    <strong><i class="fas fa-info-circle"></i> Note:</strong> 
                    Third-party cookies are subject to the respective privacy policies of these external services.
                </div>

                <h2>6. Your Cookie Choices</h2>
                <p>
                    You have several options to manage or limit how cookies are used:
                </p>

                <h3>6.1 Cookie Banner</h3>
                <p>
                    When you first visit our website, you'll see a cookie banner allowing you to accept all cookies, 
                    accept only necessary cookies, or customize your preferences.
                </p>

                <h3>6.2 Browser Settings</h3>
                <p>
                    Most web browsers allow you to control cookies through their settings. You can set your browser to:
                </p>
                <ul>
                    <li>Block all cookies</li>
                    <li>Block third-party cookies only</li>
                    <li>Clear all cookies when you close the browser</li>
                    <li>Alert you each time a cookie is being sent</li>
                </ul>

                <p>Learn how to manage cookies in popular browsers:</p>
                <ul>
                    <li><a href="https://support.google.com/chrome/answer/95647" target="_blank">Google Chrome</a></li>
                    <li><a href="https://support.mozilla.org/en-US/kb/cookies-information-websites-store-on-your-computer" target="_blank">Mozilla Firefox</a></li>
                    <li><a href="https://support.apple.com/guide/safari/manage-cookies-sfri11471/mac" target="_blank">Safari</a></li>
                    <li><a href="https://support.microsoft.com/en-us/microsoft-edge/delete-cookies-in-microsoft-edge-63947406-40ac-c3b8-57b9-2a946a29ae09" target="_blank">Microsoft Edge</a></li>
                </ul>

                <div class="highlight-box">
                    <strong><i class="fas fa-exclamation-triangle"></i> Warning:</strong> 
                    Blocking all cookies may affect the functionality of our website, including your ability to 
                    make purchases or access certain features.
                </div>

                <h2>7. Changes to This Cookie Policy</h2>
                <p>
                    We may update this Cookie Policy from time to time to reflect changes in technology, legislation, 
                    our business operations, or for other operational, legal, or regulatory reasons. We encourage you 
                    to review this policy periodically.
                </p>
                <p>
                    The "Last Updated" date at the top of this policy indicates when it was last revised. Any changes 
                    will take effect immediately upon posting.
                </p>

                <h2>8. More Information</h2>
                <p>
                    For more information about how we process your personal data, please see our 
                    <a href="privacy-policy.php">Privacy Policy</a>.
                </p>

                <div class="contact-section">
                    <h3><i class="fas fa-envelope"></i> Contact Us</h3>
                    <p>
                        If you have any questions about our use of cookies or this Cookie Policy, please contact us:
                    </p>
                    <p>
                        <strong>Email:</strong> privacy@ame-jewelry.com<br>
                        <strong>Address:</strong> Jewelry Street 13, London, UK<br>
                        <strong>Phone:</strong> +44 1234 567890
                    </p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>