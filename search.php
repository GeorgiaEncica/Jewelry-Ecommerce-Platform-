<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "jewelry_database");

if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($query)) {
    echo json_encode(['products' => [], 'categories' => [], 'pages' => []]);
    exit;
}

$searchTerm = '%' . $conn->real_escape_string($query) . '%';

// Search Products
$products = [];
$sql = "SELECT p.id, p.name, p.description, p.price, p.category,
        (SELECT image_path FROM product_images WHERE product_id = p.id LIMIT 1) AS image
        FROM products p
        WHERE p.name LIKE ? OR p.description LIKE ? OR p.category LIKE ?
        LIMIT 5";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
$stmt->close();

// Search Categories
$categories = [];
$sql = "SELECT category, COUNT(*) as count 
        FROM products 
        WHERE category LIKE ? AND category IS NOT NULL AND category != ''
        GROUP BY category
        LIMIT 5";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}
$stmt->close();

// Search Pages (static pages - customize these to match YOUR actual pages)
$pages = [];
$staticPages = [
    ['title' => 'About Us', 'url' => 'aboutus.php', 'description' => 'Learn more about our story', 'icon' => 'fas fa-info-circle', 'keywords' => 'about us story company information'],
    ['title' => 'Contact Us', 'url' => 'contactus.php', 'description' => 'Get in touch with us', 'icon' => 'fas fa-envelope', 'keywords' => 'contact email phone support help'],
    ['title' => 'Bracelets', 'url' => 'bracelets.php', 'description' => 'Browse our bracelet collection', 'icon' => 'fas fa-gem', 'keywords' => 'bracelets jewelry accessories'],
    ['title' => 'Necklaces', 'url' => 'necklaces.php', 'description' => 'Explore our necklace collection', 'icon' => 'fas fa-gem', 'keywords' => 'necklaces jewelry accessories'],
    ['title' => 'Rings', 'url' => 'rings.php', 'description' => 'Discover our ring collection', 'icon' => 'fas fa-gem', 'keywords' => 'rings jewelry accessories engagement wedding'],
    ['title' => 'Earrings', 'url' => 'earrings.php', 'description' => 'View our earring collection', 'icon' => 'fas fa-gem', 'keywords' => 'earrings jewelry accessories'],
    ['title' => 'All Products', 'url' => 'allproducts.php', 'description' => 'Browse all our products', 'icon' => 'fas fa-shopping-bag', 'keywords' => 'all products shop store catalog'],
    ['title' => 'Track your order', 'url' => 'order_tracking.php', 'description' => 'Check the status of your order', 'icon' => 'fas fa fa-truck', 'keywords' => 'all products shop store tracking shipping delivery']
];

$queryLower = strtolower($query);
foreach ($staticPages as $page) {
    $titleLower = strtolower($page['title']);
    $keywordsLower = strtolower($page['keywords']);
    
    if (strpos($titleLower, $queryLower) !== false || strpos($keywordsLower, $queryLower) !== false) {
        $pages[] = [
            'title' => $page['title'],
            'url' => $page['url'],
            'description' => $page['description'],
            'icon' => $page['icon']
        ];
    }
}

$conn->close();

echo json_encode([
    'products' => $products,
    'categories' => $categories,
    'pages' => $pages
]);
?>