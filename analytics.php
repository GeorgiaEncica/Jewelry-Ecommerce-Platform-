<?php
include 'admin_auth.php'; 
// Database connection
$conn = new mysqli("localhost", "root", "", "jewelry_database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get filter period (default: month)
$period = isset($_GET['period']) ? $_GET['period'] : 'month';

// Calculate date range based on period
$date_condition = "";
switch ($period) {
    case 'day':
        $date_condition = "DATE(o.created_at) = CURDATE()";
        break;
    case 'week':
        $date_condition = "YEARWEEK(o.created_at, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case 'month':
        $date_condition = "MONTH(o.created_at) = MONTH(CURDATE()) AND YEAR(o.created_at) = YEAR(CURDATE())";
        break;
    case 'year':
        $date_condition = "YEAR(o.created_at) = YEAR(CURDATE())";
        break;
    case 'all':
        $date_condition = "1=1";
        break;
    default:
        $date_condition = "MONTH(o.created_at) = MONTH(CURDATE()) AND YEAR(o.created_at) = YEAR(CURDATE())";
}

// 1. Key Metrics
$total_orders_query = "SELECT COUNT(*) as count FROM orders o WHERE $date_condition AND order_status != 'cancelled'";
$total_orders = $conn->query($total_orders_query)->fetch_assoc()['count'] ?? 0;

$total_revenue_query = "SELECT SUM(total_amount) as revenue FROM orders o WHERE $date_condition AND order_status != 'cancelled'";
$total_revenue = $conn->query($total_revenue_query)->fetch_assoc()['revenue'] ?? 0;

$aov = $total_orders > 0 ? $total_revenue / $total_orders : 0;

$total_customers_query = "SELECT COUNT(DISTINCT customer_email) as count FROM orders o WHERE $date_condition";
$total_customers = $conn->query($total_customers_query)->fetch_assoc()['count'] ?? 0;

// 2. Top 5 Selling Products
$top_products_query = "
    SELECT 
        oi.product_name,
        SUM(oi.quantity) as total_sold,
        SUM(oi.subtotal) as total_revenue,
        p.image
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    LEFT JOIN products p ON oi.product_id = p.id
    WHERE $date_condition AND o.order_status != 'cancelled'
    GROUP BY oi.product_id, oi.product_name
    ORDER BY total_sold DESC
    LIMIT 5
";
$top_products_result = $conn->query($top_products_query);
$top_products = [];
while ($row = $top_products_result->fetch_assoc()) {
    $top_products[] = $row;
}

// 3. Sales by Category
$category_sales_query = "
    SELECT 
        p.category,
        COUNT(DISTINCT o.id) as order_count,
        SUM(oi.quantity) as items_sold,
        SUM(oi.subtotal) as revenue
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    JOIN products p ON oi.product_id = p.id
    WHERE $date_condition AND o.order_status != 'cancelled'
    GROUP BY p.category
    ORDER BY revenue DESC
";
$category_sales_result = $conn->query($category_sales_query);
$category_sales = [];
while ($row = $category_sales_result->fetch_assoc()) {
    $category_sales[] = $row;
}

// 4. Orders Over Time (for chart)
$time_series_query = "";
switch ($period) {
    case 'day':
        // Hourly for today
        $time_series_query = "
            SELECT 
                HOUR(created_at) as time_unit,
                COUNT(*) as order_count,
                SUM(total_amount) as revenue
            FROM orders
            WHERE DATE(created_at) = CURDATE() AND order_status != 'cancelled'
            GROUP BY HOUR(created_at)
            ORDER BY time_unit
        ";
        break;
    case 'week':
        // Daily for this week
        $time_series_query = "
            SELECT 
                DAYNAME(created_at) as time_unit,
                DATE(created_at) as date_value,
                COUNT(*) as order_count,
                SUM(total_amount) as revenue
            FROM orders
            WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1) AND order_status != 'cancelled'
            GROUP BY DATE(created_at)
            ORDER BY date_value
        ";
        break;
    case 'month':
        // Daily for this month
        $time_series_query = "
            SELECT 
                DAY(created_at) as time_unit,
                COUNT(*) as order_count,
                SUM(total_amount) as revenue
            FROM orders
            WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE()) AND order_status != 'cancelled'
            GROUP BY DAY(created_at)
            ORDER BY time_unit
        ";
        break;
    case 'year':
        // Monthly for this year
        $time_series_query = "
            SELECT 
                MONTHNAME(created_at) as time_unit,
                MONTH(created_at) as month_num,
                COUNT(*) as order_count,
                SUM(total_amount) as revenue
            FROM orders
            WHERE YEAR(created_at) = YEAR(CURDATE()) AND order_status != 'cancelled'
            GROUP BY MONTH(created_at)
            ORDER BY month_num
        ";
        break;
    case 'all':
        // Monthly for all time
        $time_series_query = "
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as time_unit,
                COUNT(*) as order_count,
                SUM(total_amount) as revenue
            FROM orders
            WHERE order_status != 'cancelled'
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY time_unit DESC
            LIMIT 12
        ";
        break;
}

$time_series_result = $conn->query($time_series_query);
$time_series_data = [];
while ($row = $time_series_result->fetch_assoc()) {
    $time_series_data[] = $row;
}

// Reverse for proper chronological order in "all" view
if ($period === 'all') {
    $time_series_data = array_reverse($time_series_data);
}

$conn->close();

// Prepare data for JavaScript
$time_labels = json_encode(array_column($time_series_data, 'time_unit'));
$time_orders = json_encode(array_column($time_series_data, 'order_count'));
$time_revenue = json_encode(array_column($time_series_data, 'revenue'));

$category_labels = json_encode(array_column($category_sales, 'category'));
$category_revenue_data = json_encode(array_column($category_sales, 'revenue'));

$product_labels = json_encode(array_column($top_products, 'product_name'));
$product_sales_data = json_encode(array_column($top_products, 'total_sold'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard - Admin</title>
    <script src="script.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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

        .analyticsContainer{
            padding: 2.4rem;
        }
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .dashboard-header h1 {
            font-family: "Abril Fatface", serif;
            font-style: italic;
            font-weight: 600;
            font-size: 32px;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }



        .period-filters {
            display: flex;
            gap: 0.5rem;
            background: white;
            padding: 0.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03);
        }

        .filter-btn {
            padding: 0.6rem 1.2rem;
            border: none;
            background: transparent;
            border-radius: 6px;
            cursor: pointer;
            color: rgba(0, 0, 0, 0.74);
            transition: all 0.3s;
            text-decoration: none;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: #111;
            color: white;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .metric-card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }

        .metric-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            
        }

        .metric-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            font-size: 24px;
        }

        .metric-icon.revenue {
            background: #e8f5e9;
            color: #4caf50;
        }

        .metric-icon.orders {
            background: #e3f2fd;
            color: #2196f3;
        }

        .metric-icon.aov {
            background: #fff3e0;
            color: #ff9800;
        }

        .metric-icon.customers {
            background: #f3e5f5;
            color: #9c27b0;
        }

        .metric-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .metric-value {
            font-size: 32px;
            font-weight: 700;
            color: #111;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .chart-card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }

        .chart-card h2 {
            font-size: 18px;
            margin-bottom: 1.5rem;
            color: #111;
        }

        .chart-card.full-width {
            grid-column: 1 / -1;
        }

        .top-products-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .product-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: #f9f9f9;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .product-item:hover {
            background: #f0f0f0;
            transform: translateX(5px);
        }

        .product-rank {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg,rgb(235, 164, 42) 0%,rgb(230, 211, 74) 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
        }

        .product-rank.gold {
            background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
            color: #111;
        }

        .product-rank.silver {
            background: linear-gradient(135deg, #c0c0c0 0%, #e8e8e8 100%);
            color: #111;
        }

        .product-rank.bronze {
            background: linear-gradient(135deg, #cd7f32 0%, #daa06d 100%);
            color: white;
        }

        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            background: #e0e0e0;
        }

        .product-info {
            flex: 1;
        }

        .product-name {
            font-weight: 600;
            margin-bottom: 0.3rem;
            color: #111;
        }

        .product-stats {
            font-size: 13px;
            color: #666;
        }

        .performance-table{
            width: 100%;
            overflow-x: auto;
        }
        .category-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
            white-space: nowrap;
        }

        .category-table th {
            text-align: left;
            padding: 0.8rem;
            background: #f5f5f5;
            font-weight: 600;
            font-size: 14px;
            color: #333;
        }

        .category-table td {
            padding: 0.8rem;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .category-table tr:hover {
            background: #f9f9f9;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 0.5rem;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg,rgb(227, 227, 52) 0%,rgb(241, 184, 39) 100%);
            transition: width 0.3s ease;
        }

        @media (max-width: 1200px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            /* body {
                padding: 1rem;
            } */

            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .period-filters {
                width: 70%;
                overflow-x: scroll;
                white-space: nowrap;
                
            }

            .metrics-grid {
                grid-template-columns: 1fr;
            }
            .category-table{
                width: 100%;
                overflow-x: auto;
                overflow-y: hidden;
                white-space: nowrap;
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

    <!--Analytics Container  -->
<div class="analyticsContainer">

<div class="dashboard-header">
        <h1><i class="fa fa-line-chart" ></i> Analytics</h1>
        <div class="header-actions">
            <div class="period-filters">
                <a href="?period=day" class="filter-btn <?= $period === 'day' ? 'active' : '' ?>">TODAY</a>
                <a href="?period=week" class="filter-btn <?= $period === 'week' ? 'active' : '' ?>">THIS WEEK</a>
                <a href="?period=month" class="filter-btn <?= $period === 'month' ? 'active' : '' ?>">THIS MONTH</a>
                <a href="?period=year" class="filter-btn <?= $period === 'year' ? 'active' : '' ?>">THIS YEAR</a>
                <a href="?period=all" class="filter-btn <?= $period === 'all' ? 'active' : '' ?>">ALL TIME</a>
            </div>
        </div>
</div>

    <!-- Key Metrics -->
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-icon revenue">
                <i class="fas fa-pound-sign"></i>
            </div>
            <div class="metric-label">Total Revenue</div>
            <div class="metric-value">£<?= number_format($total_revenue, 2) ?></div>
        </div>

        <div class="metric-card">
            <div class="metric-icon orders">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="metric-label">Total Orders</div>
            <div class="metric-value"><?= number_format($total_orders) ?></div>
        </div>

        <div class="metric-card">
            <div class="metric-icon aov">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="metric-label">Average Order Value</div>
            <div class="metric-value">£<?= number_format($aov, 2) ?></div>
        </div>

        <div class="metric-card">
            <div class="metric-icon customers">
                <i class="fas fa-users"></i>
            </div>
            <div class="metric-label">Total Customers</div>
            <div class="metric-value"><?= number_format($total_customers) ?></div>
        </div>
    </div>

    <!-- Charts -->
    <div class="charts-grid">
        <!-- Orders & Revenue Over Time -->
        <div class="chart-card full-width">
            <h2><i class="fas fa-chart-area"></i> Orders & Revenue Over Time</h2>
            <canvas id="timeSeriesChart"></canvas>
        </div>

        <!-- Top 5 Products -->
        <div class="chart-card">
            <h2><i class="fas fa-trophy"></i> Top 5 Selling Products</h2>
            <?php if (empty($top_products)): ?>
                <p style="text-align: center; color: #999; padding: 2rem;">No product data available</p>
            <?php else: ?>
                <div class="top-products-list">
                    <?php foreach ($top_products as $index => $product): 
                        $rank_class = '';
                        if ($index === 0) $rank_class = 'gold';
                        elseif ($index === 1) $rank_class = 'silver';
                        elseif ($index === 2) $rank_class = 'bronze';
                    ?>
                        <div class="product-item">
                            <div class="product-rank <?= $rank_class ?>"><?= $index + 1 ?></div>
                            <img src="<?= htmlspecialchars($product['image'] ?? 'placeholder.jpg') ?>" 
                                 alt="<?= htmlspecialchars($product['product_name']) ?>" 
                                 class="product-image">
                            <div class="product-info">
                                <div class="product-name"><?= htmlspecialchars($product['product_name']) ?></div>
                                <div class="product-stats">
                                    <?= number_format($product['total_sold']) ?> sold • 
                                    £<?= number_format($product['total_revenue'], 2) ?> revenue
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sales by Category -->
        <div class="chart-card">
            <h2><i class="fas fa-chart-pie"></i> Sales by Category</h2>
            <canvas id="categoryChart"></canvas>
        </div>
    </div>

    <!-- Category Details Table -->
    <div class="chart-card">
        <h2><i class="fas fa-table"></i> Category Performance Details</h2>
        <?php if (empty($category_sales)): ?>
            <p style="text-align: center; color: #999; padding: 2rem;">No category data available</p>
        <?php else: ?>

        <div class="performance-table">

            <table class="category-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Orders</th>
                        <th>Items Sold</th>
                        <th>Revenue</th>
                        <th>Performance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $max_revenue = max(array_column($category_sales, 'revenue'));
                    foreach ($category_sales as $category): 
                        $percentage = $max_revenue > 0 ? ($category['revenue'] / $max_revenue) * 100 : 0;
                    ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($category['category']) ?></strong></td>
                            <td><?= number_format($category['order_count']) ?></td>
                            <td><?= number_format($category['items_sold']) ?></td>
                            <td>£<?= number_format($category['revenue'], 2) ?></td>
                            <td style="width: 200px;">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?= $percentage ?>%"></div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        
    </div>

</div>
   
    <script>
        // Time Series Chart (Orders & Revenue)
const timeCtx = document.getElementById('timeSeriesChart').getContext('2d');

new Chart(timeCtx, {
    type: 'line',
    data: {
        labels: <?= $time_labels ?>,
        datasets: [
            {
                label: 'Orders',
                data: <?= $time_orders ?>,
                borderColor: 'rgb(237, 212, 70)',     // gold
                backgroundColor: 'transparent',        // no fill
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: 'rgb(237, 212, 70)',
                yAxisID: 'y',
                tension: 0   // straight lines (no curve)
            },
            {
                label: 'Revenue (£)',
                data: <?= $time_revenue ?>,
                borderColor: 'rgb(218, 165, 52)',      // darker gold
                backgroundColor: 'transparent',
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: 'rgb(218, 165, 52)',
                yAxisID: 'y1',
                tension: 0   // straight lines
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        plugins: {
            legend: { position: 'top' }
        },
        scales: {
            y: {
                type: 'linear',
                position: 'left',
                title: { display: true, text: 'Orders' }
            },
            y1: {
                type: 'linear',
                position: 'right',
                title: { display: true, text: 'Revenue (£)' },
                grid: { drawOnChartArea: false }
            }
        }
    }
});


        // Category Pie Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: <?= $category_labels ?>,
                datasets: [{
                    data: <?= $category_revenue_data ?>,
                    backgroundColor: [
                        'rgb(237, 212, 70)',
                        'rgb(218, 165, 52)',
                        'rgb(237, 212, 70)',
                        'rgb(218, 165, 52)',
                        'rgb(237, 212, 70)',
                        'rgb(218, 165, 52)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': £' + context.parsed.toFixed(2);
                            }
                        }
                    }
                }
            }
        });
    </script>

</body>
</html>