<?php


include 'live_visitors.php'; 

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Validate POST input
if (!isset($_POST['product_id']) || !isset($_POST['quantity'])) {
    die("Invalid request");
}

$product_id = intval($_POST['product_id']);
$quantity = intval($_POST['quantity']);
$size = isset($_POST['size']) ? trim($_POST['size']) : null;

// Database connection
$conn = new mysqli("localhost", "root", "", "jewelry_database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch product info
$stmt = $conn->prepare("SELECT id, name, price, image, category, stock FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product_result = $stmt->get_result();

if ($product_result->num_rows === 0) {
    die("Product not found");
}

$product = $product_result->fetch_assoc();
$stmt->close();

// Check if this is a ring product (check for both 'ring' and 'rings')
$category_lower = strtolower($product['category']);
$isRing = ($category_lower === 'ring' || $category_lower === 'rings');

$available_stock = 0;

// --- Stock check logic ---
if ($isRing && $size !== null) {
    // For rings, check inventory by size
    $stmt = $conn->prepare("SELECT stock FROM product_inventory WHERE product_id = ? AND size = ?");
    $stmt->bind_param("is", $product_id, $size);
    $stmt->execute();
    $inventory_result = $stmt->get_result();
    
    if ($inventory_result->num_rows > 0) {
        $inventory = $inventory_result->fetch_assoc();
        $available_stock = $inventory['stock'];
        
        if ($quantity > $available_stock) {
            die("Sorry, only {$available_stock} available for size $size.");
        }
    } else {
        die("This size is not available.");
    }
    $stmt->close();
} else {
    // For non-ring products, use general stock
    $available_stock = $product['stock'];
    
    if ($quantity > $available_stock) {
        die("Sorry, only {$available_stock} items available.");
    }
}

// Check if stock is available at all
if ($available_stock <= 0) {
    die("Sorry, this item is out of stock.");
}

// --- Add to cart ---
// Generate a unique key for the cart item (to support multiple sizes of same product)
$cart_key = $product_id . ($size ? "_$size" : "");

if (isset($_SESSION['cart'][$cart_key])) {
    // Update quantity if already exists
    $new_quantity = $_SESSION['cart'][$cart_key]['quantity'] + $quantity;
    
    // Limit to available stock
    if ($new_quantity > $available_stock) {
        $new_quantity = $available_stock;
    }
    
    $_SESSION['cart'][$cart_key]['quantity'] = $new_quantity;
} else {
    // Add new item
    $_SESSION['cart'][$cart_key] = [
        'id' => $product_id,
        'name' => $product['name'],
        'price' => $product['price'],
        'image' => $product['image'],
        'size' => $size,
        'quantity' => $quantity,
        'category' => $product['category']
    ];
}

$conn->close();

// Redirect to cart page
header("Location: cart.php");
exit;
?>