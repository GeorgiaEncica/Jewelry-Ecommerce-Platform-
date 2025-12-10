<?php
header('Content-Type: application/json');

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($order_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
    exit;
}

// Database connection
$conn = new mysqli("localhost", "root", "", "jewelry_database");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Get order details
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    $stmt->close();
    $conn->close();
    exit;
}

$order = $result->fetch_assoc();
$stmt->close();

// Get order items with product images
$stmt = $conn->prepare("
    SELECT oi.*, 
           (SELECT image_path FROM product_images WHERE product_id = oi.product_id LIMIT 1) as product_image,
           p.image
    FROM order_items oi
    LEFT JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
$subtotal = 0;
while ($row = $result->fetch_assoc()) {
    $row['display_image'] = $row['product_image'] ?? $row['image'] ?? null;
    $items[] = $row;
    $subtotal += $row['subtotal'];
}
$stmt->close();

// Get discount information - prioritize from orders table, fallback to discount_usage
$discount_info = null;

// First check if discount info is stored in orders table
if (!empty($order['discount_code'])) {
    // Get discount details from discounts table
    $stmt = $conn->prepare("SELECT * FROM discounts WHERE code = ?");
    $stmt->bind_param("s", $order['discount_code']);
    $stmt->execute();
    $discount_result = $stmt->get_result();
    
    if ($discount_result->num_rows > 0) {
        $discount_data = $discount_result->fetch_assoc();
        
        // Calculate actual discount amount for display
        $discount_amount = floatval($order['discount_amount']);
        
        // For free shipping, if discount_amount is 0, calculate the shipping savings
        if ($discount_data['applies_to'] === 'shipping' && $discount_data['discount_type'] === 'free_shipping') {
            // Shipping savings = default shipping (5.00) - actual shipping paid
            $default_shipping = 5.00;
            $actual_shipping = isset($order['shipping_amount']) ? floatval($order['shipping_amount']) : 5.00;
            $discount_amount = $default_shipping - $actual_shipping;
        }
        
        $discount_info = [
            'code' => $order['discount_code'],
            'description' => $discount_data['description'],
            'discount_type' => $discount_data['discount_type'],
            'discount_value' => $discount_data['discount_value'],
            'applies_to' => $discount_data['applies_to'],
            'discount_amount' => $discount_amount
        ];
    }
    $stmt->close();
}

// Fallback to discount_usage table if not found in orders
if (!$discount_info) {
    $stmt = $conn->prepare("
        SELECT du.*, d.code, d.description, d.discount_type, d.discount_value, d.applies_to
        FROM discount_usage du
        JOIN discounts d ON du.discount_id = d.id
        WHERE du.order_id = ?
    ");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $discount_result = $stmt->get_result();
    
    if ($discount_result->num_rows > 0) {
        $discount_info = $discount_result->fetch_assoc();
    }
    $stmt->close();
}

$conn->close();

// Get shipping amount from order (default to 5.00 if not set for old orders)
$shipping_amount = isset($order['shipping_amount']) ? floatval($order['shipping_amount']) : 5.00;

echo json_encode([
    'success' => true,
    'order' => $order,
    'items' => $items,
    'subtotal' => $subtotal,
    'shipping_amount' => $shipping_amount,
    'discount' => $discount_info
]);
?>