<?php
session_start();

// Check if order exists
if (!isset($_SESSION['last_order'])) {
    header("Location: index.html");
    exit;
}

$order = $_SESSION['last_order'];
$order_number = $_GET['order'] ?? $order['order_number'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Complete - Âme</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background: #f7f7f7;
            font-family: "Poppins", sans-serif;
        }

        .success-container {
            max-width: 700px;
            margin: 4rem auto;
            padding: 0 2rem;
        }

        .success-card {
            background: white;
            border-radius: 12px;
            padding: 3rem;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            text-align: center;
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: #4CAF50;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            animation: scaleIn 0.5s ease;
        }

        .success-icon i {
            font-size: 50px;
            color: white;
        }

        @keyframes scaleIn {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .success-card h1 {
            font-family: "Abril Fatface", serif;
            font-style: italic;
            font-size: 32px;
            margin-bottom: 1rem;
            color: #111;
        }

        .success-card p {
            font-size: 16px;
            color: #666;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .order-details {
            background: #f9f9f9;
            border-radius: 8px;
            padding: 1.5rem;
            margin: 2rem 0;
            text-align: left;
        }

        .order-details h3 {
            font-size: 18px;
            margin-bottom: 1rem;
            color: #111;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #666;
            font-size: 14px;
        }

        .detail-value {
            font-weight: 600;
            color: #111;
            font-size: 14px;
        }

        .order-number {
            font-size: 24px;
            font-weight: 700;
            color: #4CAF50;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }

        .btn {
            padding: 0.8rem 2rem;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn-primary {
            background: #111;
            color: white;
        }

        .btn-primary:hover {
            background: #333;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: white;
            color: #111;
            border: 2px solid #111;
        }

        .btn-secondary:hover {
            background: #111;
            color: white;
        }

        .email-notice {
            background: #e3f2fd;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 2rem;
            font-size: 14px;
            color: #1565c0;
        }

        .email-notice i {
            margin-right: 0.5rem;
        }

        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>

    <!-- Main Navbar -->
    <nav class="main-navbar">
        <div class="logo"><a class="logo" href="index.html">Âme</a></div>
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
        <a href="#">Jewelry Sets</a>
        <a href="#">Track your order</a>
        <a href="aboutus.php">About Us</a>
        <a href="contactus.php">Contact Us</a>
    </div>

    <?php include 'search-overlay.html'; ?>

    <div class="success-container">
        <div class="success-card">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>

            <h1>Thank You for Your Order!</h1>
            <p>Your order has been successfully placed and is being processed.</p>

            <div class="order-details">
                <h3>Order Details</h3>
                
                <div class="detail-row">
                    <span class="detail-label">Order Number</span>
                    <span class="order-number"><?= htmlspecialchars($order_number) ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Customer Name</span>
                    <span class="detail-value"><?= htmlspecialchars($order['customer_name']) ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Email</span>
                    <span class="detail-value"><?= htmlspecialchars($order['customer_email']) ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Total Amount</span>
                    <span class="detail-value">£<?= number_format($order['total'], 2) ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Order Status</span>
                    <span class="detail-value" style="color: #ff9800;">
                        <i class="fas fa-clock"></i> Pending
                    </span>
                </div>
            </div>

            <div class="email-notice">
                <i class="fas fa-envelope"></i>
                A confirmation email has been sent to <strong><?= htmlspecialchars($order['customer_email']) ?></strong>
            </div>

            <div class="action-buttons">
                <a href="allproducts.php" class="btn btn-primary">
                    <i class="fas fa-shopping-bag"></i> Continue Shopping
                </a>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-home"></i> Back to Home
                </a>
            </div>
        </div>
    </div>

    <script>
        // Optional: Clear the last_order session after showing it once
        // This prevents refreshing the page from showing old order info
        window.addEventListener('beforeunload', function() {
            fetch('clear_order_session.php');
        });
    </script>

</body>
</html>