<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Returns & Refunds - Âme</title>
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
            color: #111; }
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
        .policy-content ul { 
            margin: 1rem 0; 
            padding-left: 2rem; 
        }
        .policy-content li { 
            margin-bottom: 0.5rem; 
            line-height: 1.6; 
            color: #555;
         }
        .highlight-box { 
            background:rgb(253, 207, 196); 
            border-left: 4px solid rgb(242, 92, 28); 
            padding: 1.5rem; 
            margin: 1.5rem 0; 
            border-radius: 4px; }
            
        @media (max-width: 768px) {
             .policy-card { padding: 2rem 1.5rem; } 
             .policy-header h1 { font-size: 28px; }
             }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="main-navbar">
        <div class="logo"><a class="logo" href="index.php">Âme</a></div>
        <div class="hamburger" id="hamburger"><div></div><div></div><div></div></div>
        <div class="nav-icons">
            <i class="fas fa-search" id="search-icon"></i>
            <a href="cart.php" style="color: inherit; text-decoration: none;"><i class="fas fa-shopping-bag"></i></a>
        </div>
    </nav>
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
                <h1><i class="fas fa-undo-alt"></i> Returns & Refunds</h1>
                <p class="last-updated">Last Updated: December 9, 2025</p>
            </div>
            <div class="policy-content">
                <h2>1. Overview</h2>
                <p>At Âme, we want you to be completely satisfied with your purchase. This page explains our returns, refunds, and exchanges policy.</p>

                <h2>2. Eligibility for Returns</h2>
                <ul>
                    <li>Items must be returned within 30 days of delivery.</li>
                    <li>Products must be unused, in their original packaging, and in the same condition as received.</li>
                    <li>Sale or discounted items may have different return rules (check product description).</li>
                </ul>

                <h2>3. Non-Returnable Items</h2>
                <p>The following items cannot be returned:</p>
                <ul>
                    <li>Gift cards</li>
                    <li>Personalized or custom-made jewelry</li>
                    <li>Items showing signs of wear, damage, or misuse</li>
                </ul>

                <h2>4. How to Return</h2>
                <ul>
                    <li>Contact our support at <strong>support@ame-jewelry.com</strong> to initiate a return.</li>
                    <li>Include your order number and reason for return.</li>
                    <li>We will provide instructions and a return shipping label.</li>
                </ul>

                <h2>5. Refunds</h2>
                <ul>
                    <li>Once the returned item is received and inspected, we will notify you via email of your refund status.</li>
                    <li>Approved refunds will be processed to the original payment method within 7–10 business days.</li>
                    <li>Shipping costs are non-refundable unless the return is due to our error.</li>
                </ul>

                <h2>6. Exchanges</h2>
                <p>If you would like to exchange a product for a different size or style, please contact our support team. Exchanges are subject to product availability.</p>

                <h2>7. Contact Information</h2>
                <p>For questions regarding returns or refunds, contact:</p>
                <p>
                    <strong>Email:</strong> support@ame-jewelry.com<br>
                    <strong>Phone:</strong> +44 1234 567890
                </p>

                <div class="highlight-box">
                    <strong>Note:</strong> Âme reserves the right to refuse returns that do not comply with this policy.
                </div>
            </div>
        </div>
    </div>

</body>
</html>
