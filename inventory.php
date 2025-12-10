<?php
include 'admin_auth.php'; 
// Database connection
$conn = new mysqli("localhost", "root", "", "jewelry_database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle stock updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    $product_id = intval($_POST['product_id']);
    $new_stock = intval($_POST['stock']);
    
    $stmt = $conn->prepare("UPDATE products SET stock = ? WHERE id = ?");
    $stmt->bind_param("ii", $new_stock, $product_id);
    $stmt->execute();
    $stmt->close();
    
    header("Location: inventory.php?updated=1");
    exit;
}

// Handle ring size stock updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_size_stock'])) {
    $inventory_id = intval($_POST['inventory_id']);
    $new_stock = intval($_POST['stock']);
    
    $stmt = $conn->prepare("UPDATE product_inventory SET stock = ? WHERE id = ?");
    $stmt->bind_param("ii", $new_stock, $inventory_id);
    $stmt->execute();
    $stmt->close();
    
    header("Location: inventory.php?updated=1");
    exit;
}

// Handle product deletion
if (isset($_GET['delete'])) {
    $product_id = intval($_GET['delete']);
    $conn->query("DELETE FROM products WHERE id = $product_id");
    header("Location: inventory.php?deleted=1");
    exit;
}

// Handle product update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $product_id = intval($_POST['product_id']);
    $name = $conn->real_escape_string(trim($_POST['name']));
    $description = $conn->real_escape_string(trim($_POST['description']));
    $price = floatval($_POST['price']);
    $category = $conn->real_escape_string(trim($_POST['category']));
    
    $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, category=? WHERE id=?");
    $stmt->bind_param("ssdsi", $name, $description, $price, $category, $product_id);
    $stmt->execute();
    $stmt->close();
    
    header("Location: inventory.php?updated=1");
    exit;
}

// Get filter
$category_filter = isset($_GET['category']) ? $_GET['category'] : 'all';
$stock_filter = isset($_GET['stock']) ? $_GET['stock'] : 'all';

// Fetch all products with stock info
$sql = "SELECT p.*, 
        (SELECT COUNT(*) FROM product_inventory pi WHERE pi.product_id = p.id) as has_sizes
        FROM products p WHERE 1=1";

if ($category_filter !== 'all') {
    $sql .= " AND p.category = '" . $conn->real_escape_string($category_filter) . "'";
}

if ($stock_filter === 'low') {
    $sql .= " AND p.stock <= 10";
} elseif ($stock_filter === 'out') {
    $sql .= " AND p.stock = 0";
}

$sql .= " ORDER BY p.name ASC";

$result = $conn->query($sql);
$products = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // If product has sizes, get size inventory
        if ($row['has_sizes'] > 0) {
            $product_id = $row['id'];
            $size_result = $conn->query("SELECT * FROM product_inventory WHERE product_id = $product_id ORDER BY size ASC");
            $row['sizes'] = [];
            while ($size_row = $size_result->fetch_assoc()) {
                $row['sizes'][] = $size_row;
            }
        }
        $products[] = $row;
    }
}

// Get categories for filter
$categories = [];
$cat_result = $conn->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != '' ORDER BY category ASC");
if ($cat_result && $cat_result->num_rows > 0) {
    while ($row = $cat_result->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management - Admin</title>
    <script src="script.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Poppins", sans-serif;
            background: #f7f7f7;
        }

        .inventory-container{
            padding: 2.5rem;
        }

         /*MAIN NAVBAR*/
        .main-navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 3rem;
            background-color: #fff;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
        }
        
        .logo {
            font-family: "Abril Fatface", serif;
            font-size: 2rem;
            font-style: italic;
            font-weight: 500;
            text-decoration: none;
            color: #111;
        }
        
        .nav-icons {
            display: flex;
            align-items: center;
            gap: 25px;
            font-size: 19px;
        }
        
        .nav-icons i {
            cursor: pointer !important;
            transition: opacity 0.3s ease;
        }

        
        .nav-icons i:hover {
            color: rgb(225, 203, 75);
            opacity: 0.6;
        }
        
        /* LOWER NAV LINKS */
        .nav-links {
            display: flex;
            justify-content: center;
            gap: 2.5rem;
            padding: 0.8rem 0;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
            background-color: #fff;
        }
        
        .nav-links a {
            text-decoration: none;
            color: #111;
            font-size: 0.9rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            position: relative;
        }
        
        .nav-links a:hover{
            text-decoration: underline;
        }
        /* ===== HAMBURGER ===== */
        .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
        }
        
        .hamburger div {
            width: 25px;
            height: 3px;
            background-color: #111;
            margin: 4px 0;
            transition: all 0.3s ease;
        }
        
        .hamburger.open div:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }
        
        .hamburger.open div:nth-child(2) {
            opacity: 0;
        }
        
        .hamburger.open div:nth-child(3) {
            transform: rotate(-45deg) translate(5px, -5px);
        }
        
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .dashboard-header h1 {
            font-family: "Abril Fatface", serif;
            font-style: italic;
            font-size: 32px;
        }

        .message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
        }

        .message.success {
            background: #e8f5e9;
            color: #388e3c;
        }

        .filters {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 0.6rem 1.2rem;
            border: 2px solid #ddd;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            color: #666;
            font-weight: 600;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: #111;
            color: white;
            border-color: #111;
        }

        .inventory-table {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f5f5f5;
        }

        th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            color: #333;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tbody tr:hover {
            background: #f9f9f9;
        }

        .product-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            background: #e0e0e0;
        }

        .product-details h3 {
            font-size: 14px;
            margin-bottom: 0.3rem;
        }

        .product-details p {
            font-size: 12px;
            color: #666;
        }

        .stock-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .stock-good {
            background: #e8f5e9;
            color: #388e3c;
        }

        .stock-low {
            background: #fff3e0;
            color: #f57c00;
        }

        .stock-out {
            background: #ffebee;
            color: #c62828;
        }

        .stock-input {
            width: 80px;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            text-align: center;
        }

        .size-stock-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .size-label {
            min-width: 60px;
            font-weight: 600;
        }

        .action-btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-edit {
            background: #e3f2fd;
            color: #1976d2;
            margin-right: 0.5rem;
        }

        .btn-edit:hover {
            background: #1976d2;
            color: white;
        }

        .btn-delete {
            background: #ffebee;
            color: #c62828;
        }

        .btn-delete:hover {
            background: #c62828;
            color: white;
        }

        .btn-update {
            background: #4caf50;
            color: white;
        }

        .btn-update:hover {
            background: #45a049;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #eee;
            font-family: "Abril Fatface", serif;
            font-size: 20px;
            font-style: italic;
            font-weight: 500;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #999;
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
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
            background: #111;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 550;
            cursor: pointer;
            margin-top: 1rem;
            font-size: 15px;
        }

        .submit-btn:hover {
            background: #333;
        }

        @media (max-width: 968px) {
            .inventory-table {
                overflow-x: auto;
            }

            table {
                min-width: 900px;
            }
        }
        @media (max-width: 900px) {
    .main-navbar {
      justify-content: space-between;
      padding: 1rem;
    }
  
    .logo {
      position: absolute;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
    }
  
    .hamburger {
      display: flex;
    }
  
    .nav-icons {
      display: flex;
    }
  
    .nav-links {
      display: none;
      flex-direction: column;
      position: absolute;
      top: 60px; 
      left: 10px;
      width: 100%;
      background: #fff;
      padding: 10px;
      gap: 1rem;
      z-index: 999;
      text-decoration: none;
      margin-left: -10px;
     
    }
  
    .nav-links.active {
      display: flex;
      position: fixed;
    }

    .nav-links a:hover::after {
        text-decoration: none !important;
    }
    
  
    .hamburger.open div:nth-child(1) {
      transform: rotate(45deg) translate(6px, 8px);
    }
    .hamburger.open div:nth-child(2) {
      opacity: 0;
    }
    .hamburger.open div:nth-child(3) {
      transform: rotate(-45deg) translate(6px, -10px);
    } 
    }
    </style>
</head>
<body>

<!-- Main Navbar -->
<nav class="main-navbar">

<div class="logo"><a class="logo" href="index.php">Âme</a></div>

<!-- Hamburger Menu -->
<div class="hamburger" id="hamburger">
    <div></div>
    <div></div>
    <div></div>
</div>

</nav>

<!-- Lower Navigation Links -->
<div class="nav-links" id="navLinks">
    <a href="admin.php">Dashboard</a>
    <a href="addproducts.php">Add Products</a>
    <a href="discounts.php">Discounts</a>
    <a href="admin_orders.php">Orders</a>
    <a href="analytics.php">Analytics</a>
    <a href="inventory.php">Inventory</a>
</div>

<div class="inventory-container">

    <div class="dashboard-header">
        <h1><i class="fas fa-boxes"></i> Inventory Management</h1>
    </div>

    <?php if (isset($_GET['updated'])): ?>
        <div class="message success">
            <i class="fas fa-check-circle"></i> Inventory updated successfully!
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="message success">
            <i class="fas fa-check-circle"></i> Product deleted successfully!
        </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="filters">
        <strong style="align-self: center;">Category:</strong>
        <a href="?category=all&stock=<?= $stock_filter ?>" class="filter-btn <?= $category_filter === 'all' ? 'active' : '' ?>">
            All
        </a>
        <?php foreach ($categories as $cat): ?>
            <a href="?category=<?= urlencode($cat) ?>&stock=<?= $stock_filter ?>" class="filter-btn <?= $category_filter === $cat ? 'active' : '' ?>">
                <?= htmlspecialchars($cat) ?>
            </a>
        <?php endforeach; ?>
        
        <div style="width: 100%; border-top: 1px solid #eee; margin: 0.5rem 0;"></div>
        
        <strong style="align-self: center;">Stock Level:</strong>
        <a href="?category=<?= $category_filter ?>&stock=all" class="filter-btn <?= $stock_filter === 'all' ? 'active' : '' ?>">
            All Stock
        </a>
        <a href="?category=<?= $category_filter ?>&stock=low" class="filter-btn <?= $stock_filter === 'low' ? 'active' : '' ?>">
            Low Stock (≤10)
        </a>
        <a href="?category=<?= $category_filter ?>&stock=out" class="filter-btn <?= $stock_filter === 'out' ? 'active' : '' ?>">
            Out of Stock
        </a>
    </div>

    <!-- Inventory Table -->
    <div class="inventory-table">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 3rem; color: #999;">
                            No products found
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td>
                                <div class="product-info">
                                    <?php if (!empty($product['image'])): ?>
                                        <img src="<?= htmlspecialchars($product['image']) ?>" class="product-image" alt="<?= htmlspecialchars($product['name']) ?>">
                                    <?php else: ?>
                                        <div class="product-image"></div>
                                    <?php endif; ?>
                                    <div class="product-details">
                                        <h3><?= htmlspecialchars($product['name']) ?></h3>
                                        <p><?= htmlspecialchars(substr($product['description'], 0, 50)) ?>...</p>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($product['category']) ?></td>
                            <td><strong>£<?= number_format($product['price'], 2) ?></strong></td>
                            <td>
                                <?php if (!empty($product['sizes'])): ?>
                                    <!-- Ring with sizes -->
                                    <?php foreach ($product['sizes'] as $size): ?>
                                        <form method="POST" style="margin-bottom: 0.5rem;">
                                            <input type="hidden" name="inventory_id" value="<?= $size['id'] ?>">
                                            <input type="hidden" name="update_size_stock" value="1">
                                            <div class="size-stock-row">
                                                <span class="size-label">Size <?= htmlspecialchars($size['size']) ?>:</span>
                                                <input type="number" name="stock" class="stock-input" value="<?= $size['stock'] ?>" min="0">
                                                <button type="submit" class="action-btn btn-update" style="padding: 0.4rem 0.8rem;">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                            </div>
                                        </form>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <!-- Regular product -->
                                    <form method="POST" style="display: flex; gap: 0.5rem; align-items: center;">
                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                        <input type="hidden" name="update_stock" value="1">
                                        <input type="number" name="stock" class="stock-input" value="<?= $product['stock'] ?>" min="0">
                                        <button type="submit" class="action-btn btn-update">
                                            <i class="fas fa-save"></i> Update
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $total_stock = 0;
                                if (!empty($product['sizes'])) {
                                    foreach ($product['sizes'] as $size) {
                                        $total_stock += $size['stock'];
                                    }
                                } else {
                                    $total_stock = $product['stock'];
                                }
                                
                                if ($total_stock == 0) {
                                    echo '<span class="stock-badge stock-out">Out of Stock</span>';
                                } elseif ($total_stock <= 10) {
                                    echo '<span class="stock-badge stock-low">Low Stock</span>';
                                } else {
                                    echo '<span class="stock-badge stock-good">In Stock</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <button class="action-btn btn-edit" onclick='editProduct(<?= json_encode($product) ?>)'>
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="action-btn btn-delete" onclick="deleteProduct(<?= $product['id'] ?>, '<?= htmlspecialchars($product['name']) ?>')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Edit Product Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Product</h2>
                <button class="modal-close" onclick="closeModal()">×</button>
            </div>
            
            <form method="POST">
                <input type="hidden" name="product_id" id="edit_product_id">
                <input type="hidden" name="update_product" value="1">
                
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="name" id="edit_name" required>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="edit_description" required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Price (£)</label>
                    <input type="number" name="price" id="edit_price" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label>Category</label>
                    <input type="text" name="category" id="edit_category" required>
                </div>
                
                <button type="submit" class="submit-btn">
                     Save Changes
                </button>
            </form>
        </div>
    </div>
    
</div>

    <script>
        const modal = document.getElementById('editModal');

        function editProduct(product) {
            document.getElementById('edit_product_id').value = product.id;
            document.getElementById('edit_name').value = product.name;
            document.getElementById('edit_description').value = product.description;
            document.getElementById('edit_price').value = product.price;
            document.getElementById('edit_category').value = product.category;
            modal.classList.add('active');
        }

        function deleteProduct(id, name) {
            if (confirm(`Are you sure you want to delete "${name}"? This will also delete all associated images and inventory records.`)) {
                window.location.href = `?delete=${id}`;
            }
        }

        function closeModal() {
            modal.classList.remove('active');
        }

        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    </script>

</body>
</html>