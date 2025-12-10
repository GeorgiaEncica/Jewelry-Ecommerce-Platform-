<?php
include 'live_visitors.php';

// Redirect if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

// Database connection
$conn = new mysqli("localhost", "root", "", "jewelry_database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get cart items with product details
$cartItems = [];
$total = 0;
$discount_amount = 0;
$discount_code = '';
$discount_error = '';

// Handle discount code application
if (isset($_POST['apply_discount'])) {
    $discount_code = strtoupper(trim($_POST['discount_code']));
    
    if (!empty($discount_code)) {
        $stmt = $conn->prepare("
            SELECT * FROM discounts 
            WHERE code = ? 
            AND is_active = 1 
            AND NOW() BETWEEN start_date AND end_date
            AND (usage_limit IS NULL OR usage_count < usage_limit)
        ");
        $stmt->bind_param("s", $discount_code);
        $stmt->execute();
        $discount_result = $stmt->get_result();
        
        if ($discount_result->num_rows > 0) {
            $discount = $discount_result->fetch_assoc();
            $_SESSION['applied_discount'] = $discount;
            $_SESSION['discount_code'] = $discount_code;
        } else {
            $discount_error = "Invalid or expired discount code";
        }
        $stmt->close();
    }
}

// Handle discount removal
if (isset($_POST['remove_discount'])) {
    unset($_SESSION['applied_discount']);
    unset($_SESSION['discount_code']);
}

// Get applied discount from session
$applied_discount = $_SESSION['applied_discount'] ?? null;
if ($applied_discount) {
    $discount_code = $_SESSION['discount_code'] ?? '';
}

foreach ($_SESSION['cart'] as $cart_key => $item) {
    $product_id = $item['id'];
    
    $stmt = $conn->prepare("SELECT id, name, price, category FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($product = $result->fetch_assoc()) {
        $product['size'] = $item['size'] ?? null;
        $product['quantity'] = $item['quantity'];
        $product['subtotal'] = $product['price'] * $item['quantity'];
        
        $cartItems[] = $product;
        $total += $product['subtotal'];
    }
    $stmt->close();
}

$conn->close();

// Calculate discount
if ($applied_discount) {
    $discount_applies = false;
    
    // Check minimum order amount
    if ($total >= $applied_discount['minimum_order_amount']) {
        if ($applied_discount['applies_to'] === 'all_products' || $applied_discount['applies_to'] === 'order_total') {
            $discount_applies = true;
            
            if ($applied_discount['discount_type'] === 'percentage') {
                $discount_amount = ($total * $applied_discount['discount_value']) / 100;
            } elseif ($applied_discount['discount_type'] === 'fixed_amount') {
                $discount_amount = $applied_discount['discount_value'];
                if ($discount_amount > $total) {
                    $discount_amount = $total; // Can't discount more than total
                }
            }
        } elseif ($applied_discount['applies_to'] === 'specific_products') {
            // Check if any cart items match discount products
            $conn = new mysqli("localhost", "root", "", "jewelry_database");
            $discount_id = $applied_discount['id'];
            $stmt = $conn->prepare("SELECT product_id FROM discount_products WHERE discount_id = ?");
            $stmt->bind_param("i", $discount_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $discount_product_ids = [];
            while ($row = $result->fetch_assoc()) {
                $discount_product_ids[] = $row['product_id'];
            }
            $stmt->close();
            $conn->close();
            
            // Calculate discount only on matching products
            $matching_total = 0;
            foreach ($cartItems as $item) {
                if (in_array($item['id'], $discount_product_ids)) {
                    $matching_total += $item['subtotal'];
                }
            }
            
            if ($matching_total > 0) {
                $discount_applies = true;
                if ($applied_discount['discount_type'] === 'percentage') {
                    $discount_amount = ($matching_total * $applied_discount['discount_value']) / 100;
                } elseif ($applied_discount['discount_type'] === 'fixed_amount') {
                    $discount_amount = $applied_discount['discount_value'];
                    if ($discount_amount > $matching_total) {
                        $discount_amount = $matching_total;
                    }
                }
            }
        } elseif ($applied_discount['applies_to'] === 'shipping') {
            // Handle shipping discount in shipping calculation
            $discount_applies = true;
        }
    } else {
        $discount_error = "Minimum order amount of £" . number_format($applied_discount['minimum_order_amount'], 2) . " required";
    }
}

// Add shipping 
$shipping = 5.00;

// Apply shipping discount
if ($applied_discount && $applied_discount['applies_to'] === 'shipping') {
    if ($applied_discount['discount_type'] === 'free_shipping') {
        // make shipping free
        $shipping = 0;
    } elseif ($applied_discount['discount_type'] === 'fixed_amount') {
        $discount_amount = min($applied_discount['discount_value'], $shipping);
        $shipping -= $discount_amount;
    } elseif ($applied_discount['discount_type'] === 'percentage') {
        $discount_amount = ($shipping * $applied_discount['discount_value']) / 100;
        $shipping -= $discount_amount;
    }
}

$grand_total = ($total - $discount_amount) + $shipping;
if ($grand_total < 0) {
    $grand_total = 0; // Safety: No negative total
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Âme</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background: #f7f7f7;
            font-family: "Poppins", sans-serif;
        }

        .checkout-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .checkout-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .checkout-header h1 {
            font-family: "Abril Fatface", serif;
            font-style: italic;
            font-size: 36px;
        }

        .checkout-progress {
            display: flex;
            justify-content: center;
            margin-bottom: 3rem;
            gap: 2rem;
        }

        .progress-step {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #999;
        }

        .progress-step.active {
            color: #111;
            font-weight: 600;
        }

        .progress-step i {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .progress-step.active i {
            background: #111;
            color: white;
        }

        .checkout-content {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 2rem;
        }

        .checkout-form,
        .order-summary {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }

        .form-section {
            margin-bottom: 2rem;
        }

        .form-section h2 {
            font-size: 20px;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #eee;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            font-size: 14px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #111;
        }

        .card-icons {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .card-icons i {
            font-size: 28px;
            color: #666;
        }

        .order-summary h2 {
            font-size: 20px;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #eee;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }

        .order-item:last-of-type {
            border-bottom: 2px solid #eee;
        }

        .item-details h4 {
            margin: 0 0 0.3rem 0;
            font-size: 14px;
        }

        .item-details p {
            margin: 0;
            font-size: 12px;
            color: #666;
        }

        .item-price {
            font-weight: 600;
            color: #111;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            font-size: 14px;
        }

        .summary-row.total {
            font-size: 20px;
            font-weight: 700;
            padding-top: 1rem;
            border-top: 2px solid #111;
            margin-top: 1rem;
        }

        .place-order-btn {
            width: 100%;
            padding: 1rem;
            background: #111;
            color: white;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 1.5rem;
            transition: all 0.3s ease;
        }

        .place-order-btn:hover {
            background: #333;
            transform: translateY(-2px);
        }

        .secure-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1rem;
            color: #666;
            font-size: 12px;
        }

        @media (max-width: 968px) {
            .checkout-content {
                grid-template-columns: 1fr;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .checkout-progress {
                flex-direction: column;
                align-items: center;
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
            <a href="cart.php" style="color: inherit; text-decoration: none; position: relative;">
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

    <?php include 'search-overlay.html'; ?>

    <div class="checkout-container">
        <div class="checkout-header">
            <h1>Checkout</h1>
            <div class="checkout-progress">
                <div class="progress-step">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Cart</span>
                </div>
                <span style="color: #ddd;">→</span>
                <div class="progress-step active">
                    <i class="fas fa-credit-card"></i>
                    <span>Checkout</span>
                </div>
                <span style="color: #ddd;">→</span>
                <div class="progress-step">
                    <i class="fas fa-check-circle"></i>
                    <span>Complete</span>
                </div>
            </div>
        </div>

        <div class="checkout-content">
            <div class="checkout-form">
                <form action="process_order.php" method="POST" id="checkoutForm">
                    
                    <!-- Contact Information -->
                    <div class="form-section">
                        <h2><i class="fas fa-user"></i> Contact Information</h2>
                        <div class="form-group">
                            <label for="name">Full Name *</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone">
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="form-section">
                        <h2><i class="fas fa-shipping-fast"></i> Shipping Address</h2>
                        <div class="form-group">
                            <label for="address">Street Address *</label>
                            <input type="text" id="address" name="address" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="city">City *</label>
                                <input type="text" id="city" name="city" required>
                            </div>
                            <div class="form-group">
                                <label for="postal_code">Postal Code *</label>
                                <input type="text" id="postal_code" name="postal_code" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="country">Country *</label>
                            <select id="country" name="country" required>
                                <option value="">Select Country</option>
                                <option value="United Kingdom" selected>United Kingdom</option>
                                <option value="United States">United States</option>
                                <option value="Canada">Canada</option>
                                <option value="France">France</option>
                                <option value="Germany">Germany</option>
                                <option value="Italy">Italy</option>
                                <option value="Spain">Spain</option>
                            </select>
                        </div>
                    </div>

                    <!-- Payment Information (FAKE) -->
                    <div class="form-section">
                        <h2><i class="fas fa-credit-card"></i> Payment Information</h2>
                        <p style="font-size: 12px; color: #999; margin-bottom: 1rem;">
                            <i class="fas fa-info-circle"></i> This is a demo checkout. No real payment will be processed.
                        </p>
                        
                        <div class="form-group">
                            <label for="card_number">Card Number *</label>
                            <input type="text" 
                                   id="card_number" 
                                   name="card_number" 
                                   placeholder="1234 5678 9012 3456" 
                                   maxlength="19"
                                   required>
                            <div class="card-icons">
                                <i class="fab fa-cc-visa"></i>
                                <i class="fab fa-cc-mastercard"></i>
                                <i class="fab fa-cc-amex"></i>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="expiry">Expiry Date *</label>
                                <input type="text" 
                                       id="expiry" 
                                       name="expiry" 
                                       placeholder="MM/YY" 
                                       maxlength="5"
                                       required>
                            </div>
                            <div class="form-group">
                                <label for="cvv">CVV *</label>
                                <input type="text" 
                                       id="cvv" 
                                       name="cvv" 
                                       placeholder="123" 
                                       maxlength="4"
                                       required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="cardholder">Cardholder Name *</label>
                            <input type="text" 
                                   id="cardholder" 
                                   name="cardholder" 
                                   placeholder="Name on card"
                                   required>
                        </div>
                    </div>
                   
                    <input type="hidden" name="grand_total" value="<?= $grand_total ?>">
                    <input type="hidden" name="discount_amount" value="<?= $discount_amount ?>">
                    <input type="hidden" name="discount_code" value="<?= htmlspecialchars($discount_code) ?>">

                    <button type="submit" class="place-order-btn">
                        <i class="fas fa-lock"></i> Pay Now 
                    </button>
                    
                    <div class="secure-badge">
                        <i class="fas fa-shield-alt"></i>
                        <span>Secure Checkout</span>
                    </div>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="order-summary">
                <h2>Order Summary</h2>
                
                <?php foreach ($cartItems as $item): ?>
                    <div class="order-item">
                        <div class="item-details">
                            <h4><?= htmlspecialchars($item['name']) ?></h4>
                            <p>
                                <?php if ($item['size']): ?>
                                    Size: <?= htmlspecialchars($item['size']) ?> •
                                <?php endif; ?>
                                Qty: <?= $item['quantity'] ?>
                            </p>
                        </div>
                        <div class="item-price">
                            £<?= number_format($item['subtotal'], 2) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <!-- Discount Code -->
                <form action="" method="POST" style="margin-bottom: 1rem; display: flex; gap: 0.5rem;">
                    <input type="text"
                        name="discount_code"
                        placeholder="Enter discount code"
                        value="<?= htmlspecialchars($discount_code) ?>"
                        style="flex:1; padding:0.6rem; border:1px solid #ddd; border-radius:6px; font-size:14px;">

                    <?php if ($applied_discount): ?>
                        <button type="submit" name="remove_discount"
                                style="background:#c00;color:#fff;border:none;border-radius:6px;padding:0 1rem;cursor:pointer;">
                            Remove
                        </button>
                    <?php else: ?>
                        <button type="submit" name="apply_discount"
                                style="background:#111;color:#fff;border:none;border-radius:6px;padding:0 1rem;cursor:pointer;">
                            Apply
                        </button>
                    <?php endif; ?>
                </form>

                    <?php if ($discount_error): ?>
                        <p style="color:#c00; font-size:13px; margin-bottom:1rem;">
                            <?= htmlspecialchars($discount_error) ?>
                        </p>
                    <?php endif; ?>

                    <?php if ($applied_discount): ?>
                        <p style="color:#2b8a3e; font-size:13px; margin-bottom:1rem;">
                            Discount "<strong><?= htmlspecialchars($discount_code) ?></strong>" applied!
                        </p>
                    <?php endif; ?>

                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>£<?= number_format($total, 2) ?></span>
                </div>
                
                <div class="summary-row">
                    <span>Shipping</span>
                    <span>£<?= number_format($shipping, 2) ?></span>
                </div>
                
                <?php if ($applied_discount && $discount_amount > 0): ?>
                <div class="summary-row" style="color: #2b8a3e; font-weight:600;">
                    <span>Discount (<?= htmlspecialchars($discount_code) ?>)</span>
                    <span>-£<?= number_format($discount_amount, 2) ?></span>
                </div>
                <?php endif; ?>

                <div class="summary-row total">
                    <span>Total</span>
                    <span>£<?= number_format($grand_total, 2) ?></span>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Format card number with spaces
        document.getElementById('card_number').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;
        });

        // Format expiry date MM/YY
        document.getElementById('expiry').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.slice(0, 2) + '/' + value.slice(2, 4);
            }
            e.target.value = value;
        });

        // CVV numbers only
        document.getElementById('cvv').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });
    </script>

</body>
</html>