<?php

 include 'admin_auth.php'; 


$conn = new mysqli("localhost", "root", "", "jewelry_database");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$total_revenue_query = $conn->query("
    SELECT SUM(total_amount) AS revenue
    FROM orders
    WHERE order_status != 'cancelled'
");

$total_revenue = $total_revenue_query->fetch_assoc()['revenue'] ?? 0;


$count = $conn->query("SELECT COUNT(*) AS total FROM live_visitors")->fetch_assoc()['total'];


$result = $conn->query("SELECT COUNT(*) AS total_orders FROM orders");
$row = $result->fetch_assoc();
$total_orders = $row['total_orders'];
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Luxury Jewelry Store</title>
    <script src="script.js" defer></script>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
 .dashboard_heading{
  padding: 1rem;
  margin: 2rem;
  font-family: "Abril Fatface", serif;
  font-style: italic;
  font-weight: 600;
  font-size: 32px;
}

.dashboard_container{
    display: flex;
    flex-wrap: wrap;
    flex-direction: row;
    gap: 1rem;
    padding: 2rem;
    margin: 3rem;
    border-radius: 7px;
    justify-content: space-evenly;
}

.total_sales, .live_visitors, .total_orders{
    height: 200px;
    width: 200px;
    background-color: white;
    display: flex;           
    flex-direction: column;  
    justify-content: center; 
    align-items: center;     
    text-align: center; 
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);     
}

.total_sales p, .live_visitors p , .total_orders p{
    text-align: center;
    letter-spacing: 1.5px;
    margin: 0; 
    font-size: 14px;
    text-transform: uppercase;
}
.total_sales h2{
    color: green;
}

@media (max-width: 900px){

    .logo{
      position: absolute;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
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

    <h1 class="dashboard_heading">Dashboard</h1>

    <div class="dashboard_container">
        <div class="total_sales">
            <h2><?php echo "£" . number_format($total_revenue, 2); ?></h2>
            <p>Total Revenue</p>
        </div>
        <div class="total_orders">
            <h2><?= $total_orders ?></h2>
            <p>Total Orders</p>
        </div>

        <div class="live_visitors">
            <h2><?= $count ?></h2>
            <p>Live Visitors</p>
        </div>
    </div>