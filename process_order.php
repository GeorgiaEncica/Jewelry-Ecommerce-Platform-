<?php
session_start();

// Redirect if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

// Validate form data
$required_fields = ['name', 'email', 'address', 'city', 'postal_code', 'country'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        die("Error: Missing required field - $field");
    }
}

// Database connection
$conn = new mysqli("localhost", "root", "", "jewelry_database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start transaction
$conn->begin_transaction();

try {
    // Generate unique order number
    $order_number = 'ORD' . date('Ymd') . strtoupper(substr(uniqid(), -6));
    
    // Get form data
    $customer_name = $conn->real_escape_string($_POST['name']);
    $customer_email = $conn->real_escape_string($_POST['email']);
    $customer_phone = $conn->real_escape_string($_POST['phone'] ?? '');
    $shipping_address = $conn->real_escape_string($_POST['address']);
    $city = $conn->real_escape_string($_POST['city']);
    $postal_code = $conn->real_escape_string($_POST['postal_code']);
    $country = $conn->real_escape_string($_POST['country']);
    
    // Get the ACTUAL amounts from checkout form (already calculated with discounts)
    $grand_total = floatval($_POST['grand_total']);
    $discount_amount = floatval($_POST['discount_amount']);
    $discount_code = $_POST['discount_code'] ?? '';
    
    // Calculate subtotal from cart
    $subtotal = 0;
    foreach ($_SESSION['cart'] as $cart_key => $item) {
        $product_id = $item['id'];
        
        $stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($product = $result->fetch_assoc()) {
            $item_subtotal = $product['price'] * $item['quantity'];
            $subtotal += $item_subtotal;
        }
        $stmt->close();
    }
    
    // Calculate shipping (reverse engineer from grand_total)
    // grand_total = (subtotal - discount_amount) + shipping
    // So: shipping = grand_total - (subtotal - discount_amount)
    $shipping = $grand_total - ($subtotal - $discount_amount);
    
    // Ensure shipping is not negative
    if ($shipping < 0) {
        $shipping = 0;
    }
    
    // Insert order with discount and shipping amounts
    $stmt = $conn->prepare("INSERT INTO orders (order_number, customer_name, customer_email, customer_phone, 
                            shipping_address, city, postal_code, country, total_amount, discount_amount, 
                            discount_code, shipping_amount, payment_method, order_status) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Credit Card', 'pending')");
    $stmt->bind_param("ssssssssddsd", $order_number, $customer_name, $customer_email, $customer_phone, 
                      $shipping_address, $city, $postal_code, $country, $grand_total, $discount_amount, 
                      $discount_code, $shipping);
    $stmt->execute();
    $order_id = $conn->insert_id;
    $stmt->close();
    
    // Insert order items
    foreach ($_SESSION['cart'] as $cart_key => $item) {
        $product_id = $item['id'];
        $quantity = $item['quantity'];
        $size = $item['size'] ?? null;
        
        // Get product details
        $stmt = $conn->prepare("SELECT name, price FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($product = $result->fetch_assoc()) {
            $product_name = $product['name'];
            $product_price = $product['price'];
            $item_subtotal = $product_price * $quantity;
            
            // Insert order item
            $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, product_price, size, quantity, subtotal) 
                                         VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt_item->bind_param("iisdsid", $order_id, $product_id, $product_name, $product_price, $size, $quantity, $item_subtotal);
            $stmt_item->execute();
            $stmt_item->close();
            
            // Deduct stock
            $category_lower = strtolower($item['category'] ?? '');
            $isRing = ($category_lower === 'ring' || $category_lower === 'rings');
            
            if ($isRing && $size) {
                // Deduct from product_inventory
                $stmt_inv = $conn->prepare("UPDATE product_inventory SET stock = stock - ? WHERE product_id = ? AND size = ?");
                $stmt_inv->bind_param("iis", $quantity, $product_id, $size);
                $stmt_inv->execute();
                $stmt_inv->close();
            } else {
                // Deduct from products table
                $stmt_stock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                $stmt_stock->bind_param("ii", $quantity, $product_id);
                $stmt_stock->execute();
                $stmt_stock->close();
            }
        }
        $stmt->close();
    }
    
    // Save discount usage if discount was applied
    if (isset($_SESSION['applied_discount']) && $_SESSION['applied_discount'] && $discount_amount > 0) {
        $discount = $_SESSION['applied_discount'];
        
        // Insert into discount_usage table
        $stmt_discount = $conn->prepare("INSERT INTO discount_usage (discount_id, order_id, customer_email, discount_amount) VALUES (?, ?, ?, ?)");
        $stmt_discount->bind_param("iisd", $discount['id'], $order_id, $customer_email, $discount_amount);
        $stmt_discount->execute();
        $stmt_discount->close();
        
        // Increment usage count for the discount
        $stmt_update = $conn->prepare("UPDATE discounts SET usage_count = usage_count + 1 WHERE id = ?");
        $stmt_update->bind_param("i", $discount['id']);
        $stmt_update->execute();
        $stmt_update->close();
    }
    
    // Commit transaction
    $conn->commit();
    
    // Clear cart and discount
    $_SESSION['cart'] = [];
    unset($_SESSION['applied_discount']);
    unset($_SESSION['discount_code']);
    
    // Store order info for thank you page
    $_SESSION['last_order'] = [
        'order_number' => $order_number,
        'customer_name' => $customer_name,
        'customer_email' => $customer_email,
        'total' => $grand_total
    ];
    
    $conn->close();
    
    // Redirect to thank you page
    header("Location: order_complete.php?order=" . $order_number);
    exit;
    
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    $conn->close();
    
    die("Error processing order: " . $e->getMessage());
}
?>