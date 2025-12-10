<?php
session_start();

$tracking_result = null;
$error_message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tracking_number'])) {
    $tracking_number = trim($_POST['tracking_number']);
    
    if (!empty($tracking_number)) {
        // Database connection
        $conn = new mysqli("localhost", "root", "", "jewelry_database");
        if ($conn->connect_error) {
            $error_message = "Connection failed. Please try again later.";
        } else {
            // Search for order by tracking number
            $stmt = $conn->prepare("SELECT * FROM orders WHERE tracking_number = ?");
            $stmt->bind_param("s", $tracking_number);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $tracking_result = $result->fetch_assoc();
                
                // Get order items
                $order_id = $tracking_result['id'];
                $stmt_items = $conn->prepare("
                    SELECT oi.*, 
                        (SELECT image_path FROM product_images WHERE product_id = oi.product_id LIMIT 1) as product_image,
                        p.image
                    FROM order_items oi
                    LEFT JOIN products p ON oi.product_id = p.id
                    WHERE oi.order_id = ?
                ");
                $stmt_items->bind_param("i", $order_id);
                $stmt_items->execute();
                $items_result = $stmt_items->get_result();
                
                $order_items = [];
                while ($item = $items_result->fetch_assoc()) {
                    $item['display_image'] = $item['product_image'] ?? $item['image'] ?? 'placeholder.jpg';
                    $order_items[] = $item;
                }
                $tracking_result['items'] = $order_items;
                $stmt_items->close();
            } else {
                $error_message = "Tracking number not found. Please check and try again.";
            }
            
            $stmt->close();
            $conn->close();
        }
    } else {
        $error_message = "Please enter a tracking number.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Order - Âme</title>
    <script src="script.js" defer></script>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background: #f7f7f7;
            font-family: "Poppins", sans-serif;
        }

        .tracking-container {
            max-width: 800px;
            margin: 4rem auto;
            padding: 0 2rem;
            margin-top: 20px;
        }

        .tracking-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .tracking-header h1 {
            font-family: "Abril Fatface", serif;
            font-style: italic;
            font-size: 36px;
            margin-bottom: 1rem;
            font-weight: 600;
            margin-top: 4rem;
        }

        .tracking-header p {
            color: #666;
            font-size: 16px;
        }

        .search-card {
            background: white;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .search-form {
            display: flex;
            gap: 1rem;
        }

        .search-input {
            flex: 1;
            padding: 1rem;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .search-input:focus {
            outline: none;
            border-color: #111;
        }

        .search-btn {
            padding: 1rem 2rem;
            background: black;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .search-btn:hover {
            background: white;
            color: black;
            border: 1px solid black;
        }

        .error-message {
            background: #ffebee;
            color: #c62828;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            text-align: center;
        }

        .tracking-result {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .result-header {
            background: linear-gradient(135deg,rgb(237, 214, 79) 0%,rgb(237, 158, 40) 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .result-header h2 {
            font-size: 24px;
            margin-bottom: 0.5rem;
        }

        .result-header .order-number {
            font-size: 18px;
            opacity: 0.9;
        }

        .tracking-timeline {
            padding: 3rem 2rem;
        }

        .timeline {
            position: relative;
            padding-left: 3rem;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 22px;
            top: 0;
            bottom: 0;
            width: 3px;
            background: #e0e0e0;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 2rem;
        }

        .timeline-item:last-child {
            padding-bottom: 0;
        }

        .timeline-dot {
            position: absolute;
            left: -2.4rem;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
        }

        .timeline-dot.active {
            background: #4caf50;
            box-shadow: 0 0 0 4px rgba(76, 175, 80, 0.2);
        }

        .timeline-dot.current {
            background: #2196f3;
            box-shadow: 0 0 0 4px rgba(33, 150, 243, 0.2);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 0 0 4px rgba(33, 150, 243, 0.2);
            }
            50% {
                box-shadow: 0 0 0 8px rgba(33, 150, 243, 0.1);
            }
        }

        .timeline-content h3 {
            font-size: 16px;
            margin-bottom: 0.3rem;
            color: #111;
        }

        .timeline-content p {
            font-size: 14px;
            color: #666;
        }

        .timeline-content .timestamp {
            font-size: 12px;
            color: #999;
            margin-top: 0.3rem;
        }

        .order-details {
            background: #f9f9f9;
            padding: 2rem;
            border-top: 1px solid #e0e0e0;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
        }

        .item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            background: #f0f0f0;
            flex-shrink: 0;
        }
        .detail-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            font-size: 14px;
            font-weight: 600;
            color: #111;
        }

        .order-items {
            padding: 2rem;
            border-top: 1px solid #e0e0e0;
        }

        .order-items h3 {
            margin-bottom: 1rem;
            color: #111;
        }

        .item {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid #eee;
        }
        .left-order-details{
            display: flex;
            justify-content: start;
            gap: 0.7rem;
        }

        .item:last-child {
            border-bottom: none;
        }

        .item-details strong {
            display: block;
            margin-bottom: 0.3rem;
        }

        .item-details small {
            color: #666;
        }

        .item-price {
            font-weight: 700;
            color: #111;
        }

        @media (max-width: 768px) {
            .tracking-container {
                margin: 2rem auto;
            }

            .search-form {
                flex-direction: column;
            }

            .details-grid {
                grid-template-columns: 1fr;
            }

            .timeline {
                padding-left: 2.5rem;
            }
            .timeline::before {
                left: 15px;
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
        <a href="track_order.php">Track your order</a>
        <a href="aboutus.php">About Us</a>
        <a href="contactus.php">Contact Us</a>
    </div>

    <?php include 'search-overlay.html'; ?>

    <div class="tracking-container">
        <div class="tracking-header">
            <h1><i class="fas fa-shipping-fast"></i> Track Your Order</h1>
            <p>Enter your tracking number to see the latest status of your order</p>
        </div>

        <div class="search-card">
            <form method="POST" class="search-form">
                <input type="text" 
                       name="tracking_number" 
                       class="search-input" 
                       placeholder="Enter your tracking number (e.g., AME123456)"
                       value="<?= isset($_POST['tracking_number']) ? htmlspecialchars($_POST['tracking_number']) : '' ?>"
                       required>
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i> Track Order
                </button>
            </form>
        </div>

        <?php if ($error_message): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <?php if ($tracking_result): ?>
            <div class="tracking-result">
                <div class="result-header">
                    <h2>Order Found!</h2>
                    <div class="order-number">Order #<?= htmlspecialchars($tracking_result['order_number']) ?></div>
                </div>

                <div class="tracking-timeline">
                    <?php
                    $tracking_status = $tracking_result['tracking_status'];
                    $statuses = [
                        'pending' => ['label' => 'Order Confirmed', 'icon' => 'fa-check-circle'],
                        'in_transit' => ['label' => 'In Transit', 'icon' => 'fa-truck'],
                        'out_for_delivery' => ['label' => 'Out for Delivery', 'icon' => 'fa-shipping-fast'],
                        'delivered' => ['label' => 'Delivered', 'icon' => 'fa-box-open']
                    ];

                    $status_order = ['pending', 'in_transit', 'out_for_delivery', 'delivered'];
                    $current_index = array_search($tracking_status, $status_order);
                    ?>

                    <div class="timeline">
                        <?php foreach ($status_order as $index => $status): ?>
                            <?php
                            $is_active = $index <= $current_index;
                            $is_current = $status === $tracking_status;
                            $dot_class = $is_current ? 'current' : ($is_active ? 'active' : '');
                            ?>
                            <div class="timeline-item">
                                <div class="timeline-dot <?= $dot_class ?>">
                                    <i class="fas <?= $statuses[$status]['icon'] ?>"></i>
                                </div>
                                <div class="timeline-content">
                                    <h3><?= $statuses[$status]['label'] ?></h3>
                                    <?php if ($is_current): ?>
                                        <p style="color: #2196f3; font-weight: 600;">Current Status</p>
                                    <?php elseif ($is_active): ?>
                                        <p style="color: #4caf50;">Completed</p>
                                    <?php else: ?>
                                        <p>Pending</p>
                                    <?php endif; ?>
                                    <?php if ($is_current && !empty($tracking_result['tracking_updated_at'])): ?>
                                        <div class="timestamp">
                                            Updated: <?= date('M d, Y H:i', strtotime($tracking_result['tracking_updated_at'])) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="order-details">
                    <h3 style="margin-bottom: 1.5rem;">Order Information</h3>
                    <div class="details-grid">
                        <div class="detail-item">
                            <div class="detail-label">Tracking Number</div>
                            <div class="detail-value"><?= htmlspecialchars($tracking_result['tracking_number']) ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Order Date</div>
                            <div class="detail-value"><?= date('M d, Y', strtotime($tracking_result['created_at'])) ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Customer Name</div>
                            <div class="detail-value"><?= htmlspecialchars($tracking_result['customer_name']) ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Total Amount</div>
                            <div class="detail-value">£<?= number_format($tracking_result['total_amount'], 2) ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Shipping Address</div>
                            <div class="detail-value">
                                <?= htmlspecialchars($tracking_result['shipping_address']) ?>, 
                                <?= htmlspecialchars($tracking_result['city']) ?>, 
                                <?= htmlspecialchars($tracking_result['postal_code']) ?>
                            </div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Email</div>
                            <div class="detail-value"><?= htmlspecialchars($tracking_result['customer_email']) ?></div>
                        </div>
                    </div>
                </div>

                <?php if (!empty($tracking_result['items'])): ?>
                    <div class="order-items">
                        <h3>Order Items</h3>
                        <?php foreach ($tracking_result['items'] as $item): ?>
                            <div class="item">
                                <div class="left-order-details">
                                    <img src="<?= htmlspecialchars($item['display_image']) ?>" 
                                    alt="<?= htmlspecialchars($item['product_name']) ?>" 
                                    class="item-image">
                                        <div class="item-details">
                                            <strong><?= htmlspecialchars($item['product_name']) ?></strong>
                                            <small>
                                                <?php if (!empty($item['size'])): ?>
                                                    Size: <?= htmlspecialchars($item['size']) ?> • 
                                                <?php endif; ?>
                                                Quantity: <?= $item['quantity'] ?>
                                            </small>
                                        </div>
                              </div>
                                <div class="item-price">£<?= number_format($item['subtotal'], 2) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>