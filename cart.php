<?php


include 'live_visitors.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Database connection
$conn = new mysqli("localhost", "root", "", "jewelry_database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update') {
        // Update quantity
        $cart_id = $_POST['cart_id'];
        $quantity = max(1, intval($_POST['quantity']));
        
        if (isset($_SESSION['cart'][$cart_id])) {
            $_SESSION['cart'][$cart_id]['quantity'] = $quantity;
        }
        
    } elseif ($action === 'remove') {
        // Remove item
        $cart_id = $_POST['cart_id'];
        unset($_SESSION['cart'][$cart_id]);
    }
    
    // Redirect to prevent form resubmission
    header("Location: cart.php");
    exit;
}

// Get cart items with product details
$cartItems = [];
$total = 0;

foreach ($_SESSION['cart'] as $cart_id => $item) {
    $product_id = $item['id'];
    $size = $item['size'] ?? null;
    
    // Fetch product details
    $stmt = $conn->prepare("SELECT id, name, price, category, 
                            (SELECT image_path FROM product_images WHERE product_id = ? LIMIT 1) as image
                            FROM products WHERE id = ?");
    $stmt->bind_param("ii", $product_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($product = $result->fetch_assoc()) {
        // Check available stock
        $available_stock = 0;
        if ($size) {
            // Ring with size - check product_inventory
            $stmt_inv = $conn->prepare("SELECT stock FROM product_inventory WHERE product_id = ? AND size = ?");
            $stmt_inv->bind_param("is", $product_id, $size);
            $stmt_inv->execute();
            $inv_result = $stmt_inv->get_result();
            if ($inv_row = $inv_result->fetch_assoc()) {
                $available_stock = $inv_row['stock'];
            }
            $stmt_inv->close();
        } else {
            // Regular product - check products table
            $stmt_stock = $conn->prepare("SELECT stock FROM products WHERE id = ?");
            $stmt_stock->bind_param("i", $product_id);
            $stmt_stock->execute();
            $stock_result = $stmt_stock->get_result();
            if ($stock_row = $stock_result->fetch_assoc()) {
                $available_stock = $stock_row['stock'];
            }
            $stmt_stock->close();
        }
        
        $product['size'] = $size;
        $product['quantity'] = $item['quantity'];
        $product['available_stock'] = $available_stock;
        $product['cart_id'] = $cart_id;
        $product['subtotal'] = $product['price'] * $item['quantity'];
        
        $cartItems[] = $product;
        $total += $product['subtotal'];
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Âme</title>
    <script src="script.js" defer></script>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background: #f7f7f7;
            font-family: "Poppins", sans-serif;
        }

        .cart-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .cart-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .cart-header h1 {
            font-family: "Abril Fatface", serif;
            font-style: italic;
            font-size: 36px;
            margin-bottom: 0.5rem;
        }

        .cart-content {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 2rem;
        }

        .cart-items {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }

        .empty-cart {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-cart i {
            font-size: 64px;
            color: #ccc;
            margin-bottom: 1rem;
        }

        .empty-cart h2 {
            color: #666;
            margin-bottom: 1rem;
        }

        .continue-shopping {
            display: inline-block;
            padding: 0.8rem 2rem;
            background: white;
            color: black;
            border: 1px solid black;
            text-decoration: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .continue-shopping:hover {
            background: black;
            color: white;
            transform: translateY(-2px);
        }

        .cart-item {
            display: grid;
            grid-template-columns: 120px 1fr auto;
            gap: 1.5rem;
            padding: 1.5rem 0;
            border-bottom: 1px solid #eee;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .item-image {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            background: #f0f0f0;
        }

        .item-details {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .item-name {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .item-size {
            font-size: 14px;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .item-price {
            font-size: 16px;
            color: #111;
            font-weight: 600;
        }

        .item-actions {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: flex-end;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: #f5f5f5;
            border-radius: 8px;
            padding: 0.3rem;
        }

        .quantity-control button {
            width: 30px;
            height: 30px;
            border: none;
            background: white;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.2s;
        }

        .quantity-control button:hover {
            background: #111;
            color: white;
        }

        .quantity-control input {
            width: 50px;
            text-align: center;
            border: none;
            padding: 5px;
            background: transparent;
            font-weight: 600;
        }

        .remove-btn {
            background: none;
            border: none;
            color: #ff4444;
            cursor: pointer;
            font-size: 14px;
            padding: 0.5rem;
            transition: color 0.2s;
        }

        .remove-btn:hover {
            color: #cc0000;
        }

        .item-subtotal {
            font-size: 18px;
            font-weight: 700;
            color: #111;
        }

        .cart-summary {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            height: fit-content;
            position: sticky;
            top: 2rem;
        }

        .summary-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #eee;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            font-size: 16px;
        }

        .summary-row.total {
            font-size: 20px;
            font-weight: 700;
            padding-top: 1rem;
            border-top: 2px solid #eee;
            margin-top: 1.5rem;
        }

        .checkout-btn {
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

        .checkout-btn:hover {
            background: white;
            color: black;
            border: 2px dotted black ;
            transform: translateY(-2px);
        }

        .stock-warning {
            color: #ff4444;
            font-size: 12px;
            margin-top: 0.3rem;
        }

        @media (max-width: 968px) {
            .cart-content {
                grid-template-columns: 1fr;
            }

            .cart-summary {
                position: static;
            }

            .cart-item {
                grid-template-columns: 80px 1fr;
                gap: 1rem;
            }

            .item-actions {
                grid-column: 1 / -1;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
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

    <!-- Main Navbar -->
    <nav class="main-navbar">
        <div class="logo"><a class="logo" href="index.php">Âme</a></div>
        <div class="hamburger" id="hamburger">
            <div></div><div></div><div></div>
        </div>

        <div class="nav-icons">
    <i class="fas fa-search" id="search-icon"></i>

    <?php
    $cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
    ?>

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

    <?php include 'search-overlay.html'; ?>

    <div class="cart-container">
        <div class="cart-header">
            <h1>Shopping Cart</h1>
            <p><?= count($cartItems) ?> item<?= count($cartItems) !== 1 ? 's' : '' ?> in your cart</p>
        </div>

        <?php if (empty($cartItems)): ?>
            <div class="cart-items">
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <h2>Your cart is empty</h2>
                    <p style="color: #999; margin-bottom: 2rem;">Add some jewelry to get started!</p>
                    <a href="allproducts.php" class="continue-shopping">Continue Shopping</a>
                </div>
            </div>
        <?php else: ?>
            <div class="cart-content">
                <div class="cart-items">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="cart-item">
                            <img src="<?= htmlspecialchars($item['image'] ?? 'placeholder.jpg') ?>" 
                                 alt="<?= htmlspecialchars($item['name']) ?>" 
                                 class="item-image">
                            
                            <div class="item-details">
                                <div>
                                    <div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
                                    <?php if ($item['size']): ?>
                                        <div class="item-size">Size: <?= htmlspecialchars($item['size']) ?></div>
                                    <?php endif; ?>
                                    <?php if ($item['quantity'] > $item['available_stock']): ?>
                                        <div class="stock-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Only <?= $item['available_stock'] ?> in stock
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="item-price">£<?= number_format($item['price'], 2) ?></div>
                            </div>
                            
                            <div class="item-actions">
                                <div class="item-subtotal">£<?= number_format($item['subtotal'], 2) ?></div>
                                
                                <form method="POST" style="display: flex; gap: 1rem; align-items: center;">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="cart_id" value="<?= htmlspecialchars($item['cart_id']) ?>">

                                    <div class="quantity-control">
                                        <button type="submit" name="quantity" value="<?= max(1, $item['quantity'] - 1) ?>">−</button>

                                        <input type="number" 
                                            value="<?= $item['quantity'] ?>" 
                                            min="1" 
                                            max="<?= $item['available_stock'] ?>"
                                            readonly>

                                        <button type="submit" name="quantity" value="<?= $item['quantity'] + 1 ?>">+</button>
                                    </div>
                                </form>
                                
                                <form method="POST">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="cart_id" value="<?= htmlspecialchars($item['cart_id']) ?>">
                                    <button type="submit" class="remove-btn">
                                        <i class="fas fa-trash"></i> Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="cart-summary">
                    <div class="summary-title">Order Summary</div>
                    
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>£<?= number_format($total, 2) ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span>Calculated at checkout</span>
                    </div>
                    
                    <div class="summary-row total">
                        <span>Total</span>
                        <span>£<?= number_format($total, 2) ?></span>
                    </div>
                    
                    <a href="checkout.php" class="checkout-btn" style="text-decoration: none; display: block; text-align: center;">
                        Proceed to Checkout
                    </a>
                    
                    <a href="allproducts.php" class="continue-shopping" style="display: block; text-align: center; margin-top: 1rem;">
                        Continue Shopping
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>