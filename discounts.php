<?php
include 'admin_auth.php'; 
// Database connection
$conn = new mysqli("localhost", "root", "", "jewelry_database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle discount creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_discount'])) {
    $code = strtoupper(trim($_POST['code']));
    $description = trim($_POST['description']);
    $discount_type = $_POST['discount_type'];
    $discount_value = floatval($_POST['discount_value']);
    $applies_to = $_POST['applies_to'];
    $minimum_order = floatval($_POST['minimum_order_amount']);
    $usage_limit = !empty($_POST['usage_limit']) ? intval($_POST['usage_limit']) : NULL;
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    $stmt = $conn->prepare("INSERT INTO discounts (code, description, discount_type, discount_value, applies_to, minimum_order_amount, usage_limit, start_date, end_date, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdsdissi", $code, $description, $discount_type, $discount_value, $applies_to, $minimum_order, $usage_limit, $start_date, $end_date, $is_active);
    
    if ($stmt->execute()) {
        $discount_id = $stmt->insert_id;
        
        // Add specific products if applicable
        if ($applies_to === 'specific_products' && !empty($_POST['product_ids'])) {
            $stmt_product = $conn->prepare("INSERT INTO discount_products (discount_id, product_id) VALUES (?, ?)");
            foreach ($_POST['product_ids'] as $product_id) {
                $stmt_product->bind_param("ii", $discount_id, $product_id);
                $stmt_product->execute();
            }
            $stmt_product->close();
        }
        
        header("Location: discounts.php?created=1");
        exit;
    }
    $stmt->close();
}

// Handle discount update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_discount'])) {
    $discount_id = intval($_POST['discount_id']);
    $code = strtoupper(trim($_POST['code']));
    $description = trim($_POST['description']);
    $discount_type = $_POST['discount_type'];
    $discount_value = floatval($_POST['discount_value']);
    $applies_to = $_POST['applies_to'];
    $minimum_order = floatval($_POST['minimum_order_amount']);
    $usage_limit = !empty($_POST['usage_limit']) ? intval($_POST['usage_limit']) : NULL;
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    $stmt = $conn->prepare("UPDATE discounts SET code=?, description=?, discount_type=?, discount_value=?, applies_to=?, minimum_order_amount=?, usage_limit=?, start_date=?, end_date=?, is_active=? WHERE id=?");
    $stmt->bind_param("sssdsdissii", $code, $description, $discount_type, $discount_value, $applies_to, $minimum_order, $usage_limit, $start_date, $end_date, $is_active, $discount_id);
    $stmt->execute();
    $stmt->close();
    
    // Update specific products
    $conn->query("DELETE FROM discount_products WHERE discount_id = $discount_id");
    if ($applies_to === 'specific_products' && !empty($_POST['product_ids'])) {
        $stmt_product = $conn->prepare("INSERT INTO discount_products (discount_id, product_id) VALUES (?, ?)");
        foreach ($_POST['product_ids'] as $product_id) {
            $stmt_product->bind_param("ii", $discount_id, $product_id);
            $stmt_product->execute();
        }
        $stmt_product->close();
    }
    
    header("Location: discounts.php?updated=1");
    exit;
}

// Handle discount deletion
if (isset($_GET['delete'])) {
    $discount_id = intval($_GET['delete']);
    $conn->query("DELETE FROM discounts WHERE id = $discount_id");
    header("Location: discounts.php?deleted=1");
    exit;
}

// Fetch all discounts
$discounts = [];
$result = $conn->query("SELECT * FROM discounts ORDER BY created_at DESC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Get associated products if specific_products
        if ($row['applies_to'] === 'specific_products') {
            $discount_id = $row['id'];
            $products_result = $conn->query("
                SELECT p.id, p.name 
                FROM discount_products dp 
                JOIN products p ON dp.product_id = p.id 
                WHERE dp.discount_id = $discount_id
            ");
            $row['products'] = [];
            while ($prod = $products_result->fetch_assoc()) {
                $row['products'][] = $prod;
            }
        }
        $discounts[] = $row;
    }
}

// Fetch all products for selection
$all_products = [];
$products_result = $conn->query("SELECT id, name, category FROM products ORDER BY name ASC");
if ($products_result && $products_result->num_rows > 0) {
    while ($prod = $products_result->fetch_assoc()) {
        $all_products[] = $prod;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discount Management - Admin</title>
    <script src="script.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
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

        body {
            font-family: "Poppins", sans-serif;
            background: #f7f7f7;
        }
        .discount_management{
            padding: 2.5rem;
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
            font-weight: 600;
        }

        .add-btn {
            padding: 0.8rem 1.5rem;
            background: #4caf50;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .add-btn:hover {
            background: #45a049;
            transform: translateY(-2px);
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

        .discounts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .discount-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            position: relative;
            transition: transform 0.3s;
        }

        .discount-card:hover {
            transform: translateY(-5px);
        }

        .discount-code {
            font-size: 24px;
            font-weight: 700;
            color: #111;
            margin-bottom: 0.5rem;
        }

        .discount-description {
            color: #666;
            font-size: 14px;
            margin-bottom: 1rem;
        }

        .discount-details {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
        }

        .detail-label {
            color: #666;
        }

        .detail-value {
            font-weight: 600;
            color: #111;
        }

        .status-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-active {
            background: #e8f5e9;
            color: #388e3c;
        }

        .status-inactive {
            background: #ffebee;
            color: #c62828;
        }

        .status-expired {
            background: #f5f5f5;
            color: #999;
        }

        .discount-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .action-btn {
            flex: 1;
            padding: 0.6rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-edit {
            background: #e3f2fd;
            color: #1976d2;
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
            overflow-y: auto;
            padding: 2rem 0;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 18px;
            padding: 2rem;
            max-width: 700px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 0.7rem;
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
            color: grey;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 550;
            font-size: 15px;
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

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
        }

        .product-selector {
            display: none;
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 0.5rem;
        }

        .product-selector.show {
            display: block;
        }

        .product-item {
            padding: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .product-item:hover {
            background: #f5f5f5;
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

        .no-discounts {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 12px;
        }

        .no-discounts i {
            font-size: 64px;
            color: #ccc;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .discounts-grid {
                grid-template-columns: 1fr;
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

<div class="discount_management">

    <div class="dashboard-header">
        <h1><i class="fas fa-tags"></i> Discount Management</h1>
        <button class="add-btn" onclick="openCreateModal()">
            <i class="fas fa-plus"></i> Create New Discount
        </button>
    </div>

    <?php if (isset($_GET['created'])): ?>
        <div class="message success">
            <i class="fas fa-check-circle"></i> Discount created successfully!
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['updated'])): ?>
        <div class="message success">
            <i class="fas fa-check-circle"></i> Discount updated successfully!
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="message success">
            <i class="fas fa-check-circle"></i> Discount deleted successfully!
        </div>
    <?php endif; ?>

    <?php if (empty($discounts)): ?>
        <div class="no-discounts">
            <i class="fas fa-percentage"></i>
            <p>No discounts created yet</p>
        </div>
    <?php else: ?>
        <div class="discounts-grid">
            <?php foreach ($discounts as $discount): ?>
                <?php
                $now = time();
                $start = strtotime($discount['start_date']);
                $end = strtotime($discount['end_date']);
                $is_expired = $now > $end;
                $is_upcoming = $now < $start;
                $is_current = $now >= $start && $now <= $end;
                
                if ($is_expired) {
                    $status = 'expired';
                    $status_text = 'Expired';
                } elseif (!$discount['is_active']) {
                    $status = 'inactive';
                    $status_text = 'Inactive';
                } elseif ($is_upcoming) {
                    $status = 'inactive';
                    $status_text = 'Upcoming';
                } else {
                    $status = 'active';
                    $status_text = 'Active';
                }
                ?>
                <div class="discount-card">
                    <span class="status-badge status-<?= $status ?>"><?= $status_text ?></span>
                    
                    <div class="discount-code"><?= htmlspecialchars($discount['code']) ?></div>
                    <div class="discount-description"><?= htmlspecialchars($discount['description']) ?></div>
                    
                    <div class="discount-details">
                        <div class="detail-row">
                            <span class="detail-label">Type:</span>
                            <span class="detail-value">
                                <?php
                                if ($discount['discount_type'] === 'percentage') {
                                    echo $discount['discount_value'] . '% Off';
                                } elseif ($discount['discount_type'] === 'fixed_amount') {
                                    echo '£' . number_format($discount['discount_value'], 2) . ' Off';
                                } else {
                                    echo 'Free Shipping';
                                }
                                ?>
                            </span>
                        </div>
                        
                        <div class="detail-row">
                            <span class="detail-label">Applies to:</span>
                            <span class="detail-value"><?= ucwords(str_replace('_', ' ', $discount['applies_to'])) ?></span>
                        </div>
                        
                        <?php if (!empty($discount['products'])): ?>
                            <div class="detail-row">
                                <span class="detail-label">Products:</span>
                                <span class="detail-value"><?= count($discount['products']) ?> selected</span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="detail-row">
                            <span class="detail-label">Usage:</span>
                            <span class="detail-value">
                                <?= $discount['usage_count'] ?><?= $discount['usage_limit'] ? ' / ' . $discount['usage_limit'] : ' / ∞' ?>
                            </span>
                        </div>
                        
                        <div class="detail-row">
                            <span class="detail-label">Valid:</span>
                            <span class="detail-value" style="font-size: 12px;">
                                <?= date('M d, Y', strtotime($discount['start_date'])) ?> - 
                                <?= date('M d, Y', strtotime($discount['end_date'])) ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="discount-actions">
                        <button class="action-btn btn-edit" onclick='editDiscount(<?= json_encode($discount) ?>)'>
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="action-btn btn-delete" onclick="deleteDiscount(<?= $discount['id'] ?>, '<?= htmlspecialchars($discount['code']) ?>')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Create/Edit Discount Modal -->
    <div id="discountModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Create New Discount</h2>
                <button class="modal-close" onclick="closeModal()">×</button>
            </div>
            
            <form method="POST" id="discountForm">
                <input type="hidden" name="discount_id" id="discount_id">
                <input type="hidden" name="create_discount" id="form_action" value="1">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Discount Code *</label>
                        <input type="text" name="code" id="code" required placeholder="e.g., SAVE20">
                    </div>
                    
                    <div class="form-group">
                        <label>Discount Type *</label>
                        <select name="discount_type" id="discount_type" required>
                            <option value="percentage">Percentage Off</option>
                            <option value="fixed_amount">Fixed Amount Off</option>
                            <option value="free_shipping">Free Shipping</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="description" placeholder="e.g., Save 20% on all products"></textarea>
                </div>
                
                <div class="form-grid">
                    <div class="form-group" id="discount_value_group">
                        <label>Discount Value *</label>
                        <input type="number" name="discount_value" id="discount_value" step="0.01" required placeholder="e.g., 20">
                    </div>
                    
                    <div class="form-group">
                        <label>Applies To *</label>
                        <select name="applies_to" id="applies_to" required>
                            <option value="all_products">All Products</option>
                            <option value="specific_products">Specific Products</option>
                            <option value="order_total">Order Total</option>
                            <option value="shipping">Shipping</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group full-width" id="product_selector_group" style="display: none;">
                    <label>Select Products</label>
                    <div class="product-selector" id="product_selector">
                        <?php foreach ($all_products as $product): ?>
                            <div class="product-item">
                                <input type="checkbox" name="product_ids[]" value="<?= $product['id'] ?>" id="prod_<?= $product['id'] ?>">
                                <label for="prod_<?= $product['id'] ?>"><?= htmlspecialchars($product['name']) ?> (<?= htmlspecialchars($product['category']) ?>)</label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Minimum Order Amount (£)</label>
                        <input type="number" name="minimum_order_amount" id="minimum_order_amount" step="0.01" value="0" placeholder="0.00">
                    </div>
                    
                    <div class="form-group">
                        <label>Usage Limit (leave empty for unlimited)</label>
                        <input type="number" name="usage_limit" id="usage_limit" placeholder="Unlimited">
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Start Date & Time *</label>
                        <input type="datetime-local" name="start_date" id="start_date" required>
                    </div>
                    
                    <div class="form-group">
                        <label>End Date & Time *</label>
                        <input type="datetime-local" name="end_date" id="end_date" required>
                    </div>
                </div>
                
                <div class="form-group checkbox-group">
                    <input type="checkbox" name="is_active" id="is_active" checked>
                    <label for="is_active">Active (discount is currently available)</label>
                </div>
                
                <button type="submit" class="submit-btn">
                     Save Discount
                </button>
            </form>
        </div>
    </div>
</div>

    <script>
        const modal = document.getElementById('discountModal');
        const discountTypeSelect = document.getElementById('discount_type');
        const appliesToSelect = document.getElementById('applies_to');
        const productSelectorGroup = document.getElementById('product_selector_group');
        const productSelector = document.getElementById('product_selector');
        const discountValueGroup = document.getElementById('discount_value_group');

        // Show/hide discount value based on type
        discountTypeSelect.addEventListener('change', function() {
            if (this.value === 'free_shipping') {
                discountValueGroup.style.display = 'none';
                document.getElementById('discount_value').required = false;
                document.getElementById('discount_value').value = 0;
            } else {
                discountValueGroup.style.display = 'block';
                document.getElementById('discount_value').required = true;
            }
        });

        // Show/hide product selector
        appliesToSelect.addEventListener('change', function() {
            if (this.value === 'specific_products') {
                productSelectorGroup.style.display = 'block';
                productSelector.classList.add('show');
            } else {
                productSelectorGroup.style.display = 'none';
                productSelector.classList.remove('show');
            }
        });

        function openCreateModal() {
            document.getElementById('modalTitle').textContent = 'Create New Discount';
            document.getElementById('discountForm').reset();
            document.getElementById('discount_id').value = '';
            document.getElementById('form_action').name = 'create_discount';
            
            // Uncheck all products
            document.querySelectorAll('input[name="product_ids[]"]').forEach(cb => cb.checked = false);
            
            modal.classList.add('active');
        }

        function editDiscount(discount) {
            document.getElementById('modalTitle').textContent = 'Edit Discount';
            document.getElementById('discount_id').value = discount.id;
            document.getElementById('form_action').name = 'update_discount';
            document.getElementById('code').value = discount.code;
            document.getElementById('description').value = discount.description;
            document.getElementById('discount_type').value = discount.discount_type;
            document.getElementById('discount_value').value = discount.discount_value;
            document.getElementById('applies_to').value = discount.applies_to;
            document.getElementById('minimum_order_amount').value = discount.minimum_order_amount;
            document.getElementById('usage_limit').value = discount.usage_limit || '';
            document.getElementById('start_date').value = discount.start_date.replace(' ', 'T').slice(0, 16);
            document.getElementById('end_date').value = discount.end_date.replace(' ', 'T').slice(0, 16);
            document.getElementById('is_active').checked = discount.is_active == 1;
            
            // Trigger change events
            discountTypeSelect.dispatchEvent(new Event('change'));
            appliesToSelect.dispatchEvent(new Event('change'));
            
            // Check selected products
            document.querySelectorAll('input[name="product_ids[]"]').forEach(cb => cb.checked = false);
            if (discount.products) {
                discount.products.forEach(prod => {
                    const checkbox = document.getElementById('prod_' + prod.id);
                    if (checkbox) checkbox.checked = true;
                });
            }
            
            modal.classList.add('active');
        }

        function deleteDiscount(id, code) {
            if (confirm(`Are you sure you want to delete discount "${code}"?`)) {
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