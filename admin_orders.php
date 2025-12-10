<?php
 include 'admin_auth.php'; 
// Database connection
$conn = new mysqli("localhost", "root", "", "jewelry_database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle tracking number update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_tracking'])) {
    $order_id = intval($_POST['order_id']);
    $tracking_number = $conn->real_escape_string(trim($_POST['tracking_number']));
    $tracking_status = $conn->real_escape_string($_POST['tracking_status']);
    
    // Update tracking info and set order to completed if tracking number is added
    $new_order_status = !empty($tracking_number) ? 'completed' : 'processing';
    
    $stmt = $conn->prepare("UPDATE orders SET tracking_number = ?, tracking_status = ?, order_status = ?, tracking_updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("sssi", $tracking_number, $tracking_status, $new_order_status, $order_id);
    $stmt->execute();
    $stmt->close();
    
    header("Location: admin_orders.php?tracking_updated=1");
    exit;
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $conn->real_escape_string($_POST['status']);
    
    $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();
    $stmt->close();
    
    header("Location: admin_orders.php?updated=1");
    exit;
}

// Get filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Fetch orders
$sql = "SELECT * FROM orders";
if ($status_filter !== 'all') {
    $sql .= " WHERE order_status = '" . $conn->real_escape_string($status_filter) . "'";
}
$sql .= " ORDER BY created_at DESC";

$result = $conn->query($sql);
$orders = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}

// Get order statistics
$stats = [
    'total' => $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'],
    'pending' => $conn->query("SELECT COUNT(*) as count FROM orders WHERE order_status = 'pending'")->fetch_assoc()['count'],
    'processing' => $conn->query("SELECT COUNT(*) as count FROM orders WHERE order_status = 'processing'")->fetch_assoc()['count'],
    'completed' => $conn->query("SELECT COUNT(*) as count FROM orders WHERE order_status = 'completed'")->fetch_assoc()['count'],
    'cancelled' => $conn->query("SELECT COUNT(*) as count FROM orders WHERE order_status = 'cancelled'")->fetch_assoc()['count']
];

$total_revenue = $conn->query("SELECT SUM(total_amount) as revenue FROM orders WHERE order_status != 'cancelled'")->fetch_assoc()['revenue'] ?? 0;

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management - Admin</title>
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
            font-weight: 600;
        }


        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }

        .stat-card h3 {
            font-size: 14px;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .stat-card .number {
            font-size: 32px;
            font-weight: 700;
            color: #111;
        }

        .stat-card.revenue .number {
            color: #4CAF50;
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

        .orders-table {
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

        .order-number {
            font-weight: 700;
            color: #111;
        }

        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .status-pending {
            background: #fff3e0;
            color: #f57c00;
        }

        .status-processing {
            background: #e3f2fd;
            color: #1976d2;
        }

        .status-completed {
            background: #e8f5e9;
            color: #388e3c;
        }

        .status-cancelled {
            background: #ffebee;
            color: #d32f2f;
        }

        .action-btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.2s;
        }

        .btn-view {
            background: #e3f2fd;
            color: #1976d2;
            margin-right: 0.5rem;
        }

        .btn-view:hover {
            background: #1976d2;
            color: white;
        }

        .status-select {
            padding: 0.4rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 12px;
        }

        .no-orders {
            text-align: center;
            padding: 3rem;
            color: #999;
        }

        .no-orders i {
            font-size: 48px;
            margin-bottom: 1rem;
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

        .order-detail-row {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid #eee;
        }

        .detail-label {
            color: #666;
            font-weight: 600;
        }

        @media (max-width: 968px) {
            .orders-table {
                overflow-x: auto;
            }

            table {
                min-width: 800px;
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

<div class="orders_dashboard" style="padding: 2.5rem;">

    <div class="dashboard-header">
        <h1><i class="fas fa-shopping-cart"></i> Orders Management</h1>
    </div>

    <?php if (isset($_GET['tracking_updated'])): ?>
        <div style="background: #e8f5e9; color: #388e3c; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            <i class="fas fa-check-circle"></i> Tracking information updated successfully!
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['updated'])): ?>
        <div style="background: #e8f5e9; color: #388e3c; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            <i class="fas fa-check-circle"></i> Order status updated successfully!
        </div>
    <?php endif; ?>

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Orders</h3>
            <div class="number"><?= $stats['total'] ?></div>
        </div>
        <div class="stat-card">
            <h3>Pending</h3>
            <div class="number" style="color: #f57c00;"><?= $stats['pending'] ?></div>
        </div>
        <div class="stat-card">
            <h3>Processing</h3>
            <div class="number" style="color: #1976d2;"><?= $stats['processing'] ?></div>
        </div>
        <div class="stat-card">
            <h3>Completed</h3>
            <div class="number" style="color: #388e3c;"><?= $stats['completed'] ?></div>
        </div>
        <div class="stat-card revenue">
            <h3>Total Revenue</h3>
            <div class="number">£<?= number_format($total_revenue, 2) ?></div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters">
        <a href="?status=all" class="filter-btn <?= $status_filter === 'all' ? 'active' : '' ?>">
            All Orders
        </a>
        <a href="?status=pending" class="filter-btn <?= $status_filter === 'pending' ? 'active' : '' ?>">
            Pending
        </a>
        <a href="?status=processing" class="filter-btn <?= $status_filter === 'processing' ? 'active' : '' ?>">
            Processing
        </a>
        <a href="?status=completed" class="filter-btn <?= $status_filter === 'completed' ? 'active' : '' ?>">
            Completed
        </a>
        <a href="?status=cancelled" class="filter-btn <?= $status_filter === 'cancelled' ? 'active' : '' ?>">
            Cancelled
        </a>
    </div>

    <!-- Orders Table -->
    <div class="orders-table">
        <?php if (empty($orders)): ?>
            <div class="no-orders">
                <i class="fas fa-inbox"></i>
                <p>No orders found</p>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Tracking</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td class="order-number"><?= htmlspecialchars($order['order_number']) ?></td>
                            <td><?= htmlspecialchars($order['customer_name']) ?></td>
                            <td><?= htmlspecialchars($order['customer_email']) ?></td>
                            <td>£<?= number_format($order['total_amount'], 2) ?></td>
                            <td>
                                <span class="status-badge status-<?= $order['order_status'] ?>">
                                    <?= ucfirst($order['order_status']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($order['tracking_number'])): ?>
                                    <button class="action-btn" style="background: #e3f2fd; color: #1976d2;" onclick="editTracking(<?= $order['id'] ?>, '<?= htmlspecialchars($order['tracking_number']) ?>', '<?= $order['tracking_status'] ?>')">
                                        <i class="fas fa-truck"></i> <?= htmlspecialchars($order['tracking_number']) ?>
                                    </button>
                                <?php else: ?>
                                    <button class="action-btn" style="background: #fff3e0; color: #f57c00;" onclick="addTracking(<?= $order['id'] ?>)">
                                        <i class="fas fa-plus"></i> Add Tracking
                                    </button>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                            <td>
                                <button class="action-btn btn-view" onclick="viewOrder(<?= $order['id'] ?>)">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <input type="hidden" name="update_status" value="1">
                                    <select name="status" class="status-select" onchange="this.form.submit()">
                                        <option value="pending" <?= $order['order_status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="processing" <?= $order['order_status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                                        <option value="completed" <?= $order['order_status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                                        <option value="cancelled" <?= $order['order_status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Order Details Modal -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Order Details</h2>
                <button class="modal-close" onclick="closeModal()">×</button>
            </div>
            <div id="modalBody">
                <!-- Order details will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Tracking Modal -->
    <div id="trackingModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="trackingModalTitle">Add Tracking Information</h2>
                <button class="modal-close" onclick="closeTrackingModal()">×</button>
            </div>
            <form method="POST" style="padding: 1rem 0;">
                <input type="hidden" name="order_id" id="tracking_order_id">
                <input type="hidden" name="update_tracking" value="1">
                
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Tracking Number</label>
                    <input type="text" 
                           name="tracking_number" 
                           id="tracking_number_input"
                           placeholder="Enter tracking number"
                           style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 8px;" 
                           required>
                </div>
                
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Tracking Status</label>
                    <select name="tracking_status" 
                            id="tracking_status_select"
                            style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 8px;" 
                            required>
                        <option value="pending">Pending</option>
                        <option value="in_transit">In Transit</option>
                        <option value="out_for_delivery">Out for Delivery</option>
                        <option value="delivered">Delivered</option>
                    </select>
                </div>
                
                <div style="background: #e3f2fd; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; font-size: 14px;">
                    <i class="fas fa-info-circle"></i> 
                    Adding a tracking number will automatically mark this order as <strong>Completed</strong>
                </div>
                
                <button type="submit" 
                        style="width: 100%; padding: 0.8rem; background: #111; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    <i class="fas fa-save"></i> Save Tracking Information
                </button>
            </form>
        </div>
    </div>
</div>

<script>
        function addTracking(orderId) {
            document.getElementById('trackingModalTitle').textContent = 'Add Tracking Information';
            document.getElementById('tracking_order_id').value = orderId;
            document.getElementById('tracking_number_input').value = '';
            document.getElementById('tracking_status_select').value = 'pending';
            document.getElementById('trackingModal').classList.add('active');
        }

        function editTracking(orderId, trackingNumber, trackingStatus) {
            document.getElementById('trackingModalTitle').textContent = 'Edit Tracking Information';
            document.getElementById('tracking_order_id').value = orderId;
            document.getElementById('tracking_number_input').value = trackingNumber;
            document.getElementById('tracking_status_select').value = trackingStatus;
            document.getElementById('trackingModal').classList.add('active');
        }

        function closeTrackingModal() {
            document.getElementById('trackingModal').classList.remove('active');
        }

        function viewOrder(orderId) {
            const modal = document.getElementById('orderModal');
            const modalBody = document.getElementById('modalBody');
            
            modal.classList.add('active');
            modalBody.innerHTML = '<p style="text-align: center; padding: 2rem;">Loading...</p>';
            
            fetch('get_order_details.php?id=' + orderId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayOrderDetails(data.order, data.items);
                    } else {
                        modalBody.innerHTML = '<p style="color: red;">Error loading order details</p>';
                    }
                })
                .catch(error => {
                    modalBody.innerHTML = '<p style="color: red;">Error: ' + error.message + '</p>';
                });
        }

        function displayOrderDetails(order, items) {
    let html = `
        <div class="order-detail-row">
            <span class="detail-label">Order Number:</span>
            <span style="font-weight: 700;">${order.order_number}</span>
        </div>
        <div class="order-detail-row">
            <span class="detail-label">Customer Name:</span>
            <span>${order.customer_name}</span>
        </div>
        <div class="order-detail-row">
            <span class="detail-label">Email:</span>
            <span>${order.customer_email}</span>
        </div>
        <div class="order-detail-row">
            <span class="detail-label">Phone:</span>
            <span>${order.customer_phone || 'N/A'}</span>
        </div>
        <div class="order-detail-row">
            <span class="detail-label">Address:</span>
            <span>${order.shipping_address}</span>
        </div>
        <div class="order-detail-row">
            <span class="detail-label">City:</span>
            <span>${order.city}</span>
        </div>
        <div class="order-detail-row">
            <span class="detail-label">Postal Code:</span>
            <span>${order.postal_code}</span>
        </div>
        <div class="order-detail-row">
            <span class="detail-label">Country:</span>
            <span>${order.country}</span>
        </div>
        
        <h3 style="margin-top: 2rem; margin-bottom: 1rem;">Order Items</h3>
    `;
    
    items.forEach(item => {
        const imageSrc = item.display_image || 'placeholder.jpg';
        html += `
            <div class="order-detail-row" style="align-items: center; padding: 1rem 0;">
                <div style="display: flex; align-items: center; gap: 1rem; flex: 1;">
                    <img src="${imageSrc}" 
                         alt="${item.product_name}" 
                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; background: #f0f0f0;">
                    <div>
                        <strong>${item.product_name}</strong>
                        ${item.size ? `<br><small style="color: #666;">Size: ${item.size}</small>` : ''}
                        <br><small style="color: #666;">Qty: ${item.quantity} × £${parseFloat(item.product_price).toFixed(2)}</small>
                    </div>
                </div>
                <span style="font-weight: 700;">£${parseFloat(item.subtotal).toFixed(2)}</span>
            </div>
        `;
    });
    
    html += `
        <div style="margin-top: 2rem; padding-top: 1rem; border-top: 2px solid #eee;">
            <h3 style="margin-bottom: 1rem;">Order Summary</h3>
    `;
    
    // Fetch data to get subtotal and shipping from server
    fetch('get_order_details.php?id=' + order.id)
        .then(response => response.json())
        .then(data => {
            if (data.subtotal) {
                html += `
                    <div class="order-detail-row">
                        <span class="detail-label">Subtotal:</span>
                        <span>£${parseFloat(data.subtotal).toFixed(2)}</span>
                    </div>
                `;
            }
            
            // Show discount if applied
            if (data.discount) {
                const discountAmount = parseFloat(data.discount.discount_amount) || 0;
                const discountLabel = data.discount.applies_to === 'shipping' && data.discount.discount_type === 'free_shipping' 
                    ? 'Free Shipping Applied' 
                    : 'Discount Applied';
                
                html += `
                    <div class="order-detail-row" style="background: #e8f5e9; padding: 0.8rem; border-radius: 6px; margin: 0.5rem 0;">
                        <div>
                            <strong style="color: #388e3c;">
                                <i class="fas fa-tag"></i> ${discountLabel}
                            </strong>
                            <br>
                            <small style="color: #666;">Code: ${data.discount.code}</small>
                            ${data.discount.description ? `<br><small style="color: #666;">${data.discount.description}</small>` : ''}
                        </div>
                        ${discountAmount > 0 ? `<span style="font-weight: 700; color: #388e3c;">-£${discountAmount.toFixed(2)}</span>` : ''}
                    </div>
                `;
            }
            
            // Use stored shipping amount from database
            const shipping = data.shipping_amount !== undefined ? parseFloat(data.shipping_amount) : 5.00;
            html += `
                <div class="order-detail-row">
                    <span class="detail-label">Shipping:</span>
                    <span>£${shipping.toFixed(2)}</span>
                </div>
            `;
            
            html += `
                <div class="order-detail-row" style="border-top: 2px solid #111; margin-top: 1rem; padding-top: 1rem;">
                    <span style="font-size: 18px; font-weight: 700;">Total Paid:</span>
                    <span style="font-size: 18px; font-weight: 700; color: #4CAF50;">£${parseFloat(order.total_amount).toFixed(2)}</span>
                </div>
            </div>
            `;
            
            document.getElementById('modalBody').innerHTML = html;
        });
}

        function closeModal() {
            document.getElementById('orderModal').classList.remove('active');
        }

        // Close modal when clicking outside
        document.getElementById('orderModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        document.getElementById('trackingModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeTrackingModal();
            }
        });
    </script>

</body>
</html>