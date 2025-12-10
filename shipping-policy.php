<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Policy - Âme</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background: #f7f7f7;
            font-family: "Poppins", sans-serif;
        }

        .policy-container {
            max-width: 900px;
            margin: 3rem auto;
            padding: 0 2rem;
        }

        .policy-card {
            background: white;
            border-radius: 12px;
            padding: 3rem;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }

        .policy-header {
            text-align: center;
            margin-bottom: 3rem;
            padding-bottom: 2rem;
            border-bottom: 2px solid #eee;
        }

        .policy-header h1 {
            font-family: "Abril Fatface", serif;
            font-style: italic;
            font-size: 36px;
            margin-bottom: 0.5rem;
        }

        .policy-header .last-updated {
            color: #666;
            font-size: 14px;
        }

        .policy-content h2 {
            font-size: 24px;
            margin-top: 2rem;
            margin-bottom: 1rem;
            color: #111;
        }

        .policy-content h3 {
            font-size: 18px;
            margin-top: 1.5rem;
            margin-bottom: 0.8rem;
            color: #333;
        }

        .policy-content p {
            line-height: 1.8;
            color: #555;
            margin-bottom: 1rem;
        }

        .policy-content ul, .policy-content ol {
            margin: 1rem 0;
            padding-left: 2rem;
        }

        .policy-content li {
            margin-bottom: 0.5rem;
            line-height: 1.6;
            color: #555;
        }

        .shipping-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5rem 0;
        }

        .shipping-table th,
        .shipping-table td {
            padding: 1rem;
            text-align: left;
            border: 1px solid #ddd;
        }

        .shipping-table th {
            background: #f5f5f5;
            font-weight: 600;
        }

        .highlight-box {
            background: #fff3e0;
            border-left: 4px solid #ff9800;
            padding: 1.5rem;
            margin: 1.5rem 0;
            border-radius: 4px;
        }

        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 1.5rem;
            margin: 1.5rem 0;
            border-radius: 4px;
        }

        .success-box {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
            padding: 1.5rem;
            margin: 1.5rem 0;
            border-radius: 4px;
        }

        .contact-section {
            background: #f9f9f9;
            padding: 2rem;
            border-radius: 8px;
            margin-top: 3rem;
        }

        .icon-list {
            list-style: none;
            padding: 0;
        }

        .icon-list li {
            padding-left: 2rem;
            position: relative;
            margin-bottom: 1rem;
        }

        .icon-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #4caf50;
            font-weight: bold;
            font-size: 18px;
        }

        @media (max-width: 768px) {
            .policy-card {
                padding: 2rem 1.5rem;
            }

            .policy-header h1 {
                font-size: 28px;
            }

            .shipping-table {
                font-size: 13px;
            }

            .shipping-table th,
            .shipping-table td {
                padding: 0.7rem;
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
        <a href="order_tracking.php">Track your order</a>
        <a href="aboutus.php">About Us</a>
        <a href="contactus.php">Contact Us</a>
    </div>

    <div class="policy-container">
        <div class="policy-card">
            <div class="policy-header">
                <h1><i class="fas fa-shipping-fast"></i> Shipping Policy</h1>
                <p class="last-updated">Last Updated: December 9, 2025</p>
            </div>

            <div class="policy-content">
                <h2>1. Overview</h2>
                <p>
                    At Âme, we are committed to delivering your jewelry purchases safely and promptly. This shipping 
                    policy outlines our delivery methods, timeframes, costs, and procedures.
                </p>



                <h2>2. Shipping Locations</h2>
                <p>We currently ship to the following locations:</p>

                <!-- <h3>2Shipping </h3> -->
                <ul class="icon-list">
                    <li>England</li>
                    <li>Scotland</li>
                    <li>Wales</li>
                    <li>Northern Ireland</li>
                    <li>United States</li>
                    <li>Canada</li>
                    <li>Spain</li>
                    <li>France</li>
                    <li>Germany</li>
                    <li>Italy</li>
                </ul>


                <h2>3. Shipping Methods & Delivery Times</h2>

                <table class="shipping-table">
                    <thead>
                        <tr>
                            <th>Shipping Method</th>
                            <th>Delivery Time</th>
                            <th>Cost (UK)</th>
                            <th>Tracking</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Standard Shipping</strong></td>
                            <td>3-5 business days</td>
                            <td>£5.00</td>
                            <td>✓ Available</td>
                        </tr>
                    </tbody>
                </table>

                

                <div class="highlight-box">
                    <strong><i class="fas fa-exclamation-triangle"></i> Note:</strong> 
                    Delivery times are estimates and may vary due to customs clearance, weather conditions, 
                    or carrier delays. We cannot guarantee specific delivery dates.
                </div>

                <h2>4. Order Processing Time</h2>
                <p>
                    All orders are processed within 1-2 business days (Monday-Friday, excluding public holidays). 
                    Orders placed on weekends or holidays will be processed the next business day.
                </p>

                <h3>4.1 Processing Timeline</h3>
                <ol>
                    <li><strong>Order Confirmation:</strong> Immediate email upon order placement</li>
                    <li><strong>Processing:</strong> 1-2 business days</li>
                    <li><strong>Dispatch:</strong> Email with tracking number once shipped</li>
                    <li><strong>Delivery:</strong> According to selected shipping method</li>
                </ol>

                <h2>5. Order Tracking</h2>
                <p>
                    Once your order ships, you will receive an email with a tracking number. You can track your 
                    order by:
                </p>
                <ul>
                    <li>Visiting our <a href="order_tracking.php">Order Tracking Page</a></li>
                    <li>Clicking the tracking link in your shipping confirmation email</li>
                    <li>Using the tracking number on the carrier's website</li>
                </ul>

                <div class="info-box">
                    <strong><i class="fas fa-info-circle"></i> Tracking Updates:</strong> 
                    Please allow 24-48 hours after receiving your tracking number for the carrier to update 
                    their system.
                </div>

                <h2>6. Shipping Restrictions</h2>

                <h3>6.1 We Do Not Ship To:</h3>
                <ul>
                    <li>PO Boxes or APO/FPO addresses</li>
                    <li>Freight forwarding addresses</li>
                    <li>Countries under international sanctions</li>
                </ul>

                <h3>6.2 High-Value Items</h3>
                <p>
                    Orders over £500 may require signature upon delivery for security purposes. The carrier will 
                    leave a notice if no one is available to sign.
                </p>

                <h2>7. Customs, Duties & Taxes (International Orders)</h2>
                <p>
                    For international shipments, you may be subject to import duties, taxes, and customs fees 
                    imposed by your country. These charges are:
                </p>
                <ul>
                    <li><strong>Not included</strong> in our shipping costs or product prices</li>
                    <li><strong>Your responsibility</strong> as the recipient</li>
                    <li><strong>Collected</strong> by the carrier or customs at delivery</li>
                </ul>

                <div class="highlight-box">
                    <strong><i class="fas fa-exclamation-circle"></i> Important:</strong> 
                    Customs delays can occur. We recommend contacting your local customs office if your order 
                    is held for an extended period.
                </div>

                <h2>8. Lost or Damaged Packages</h2>

                <h3>8.1 Lost Packages</h3>
                <p>
                    If your package shows as delivered but you haven't received it:
                </p>
                <ol>
                    <li>Check with neighbors or building management</li>
                    <li>Verify the shipping address on your order confirmation</li>
                    <li>Wait 24-48 hours (sometimes carriers mark as delivered early)</li>
                    <li>Contact us at support@ame-jewelry.com with your order number</li>
                </ol>

                <h3>8.2 Damaged Packages</h3>
                <p>
                    If your package arrives damaged:
                </p>
                <ul>
                    <li>Take photos of the damaged packaging and items</li>
                    <li>Contact us within 48 hours of delivery</li>
                    <li>Do not discard the packaging</li>
                    <li>We will arrange a replacement or refund</li>
                </ul>

                <div class="success-box">
                    <strong><i class="fas fa-shield-alt"></i> Insurance:</strong> 
                    All shipments are fully insured against loss or damage during transit at no extra cost to you.
                </div>

                <h2>9. Incorrect Address</h2>
                <p>
                    Please ensure your shipping address is correct before completing your order. If you realize 
                    your address is incorrect:
                </p>
                <ul>
                    <li><strong>Before shipping:</strong> Contact us immediately to update (no charge)</li>
                    <li><strong>After shipping:</strong> Address changes may incur additional fees or result in return to sender</li>
                </ul>

                <h2>10. Refused or Undeliverable Packages</h2>
                <p>
                    If you refuse delivery or the package is returned as undeliverable:
                </p>
                <ul>
                    <li>Original shipping costs are non-refundable</li>
                    <li>Return shipping costs will be deducted from your refund</li>
                    <li>You may be charged a restocking fee</li>
                </ul>

                <h2>11. Delivery Delays</h2>
                <p>
                    While rare, delivery delays can occur due to:
                </p>
                <ul>
                    <li>Weather conditions</li>
                    <li>Customs clearance (international orders)</li>
                    <li>Carrier capacity issues</li>
                    <li>Public holidays</li>
                    <li>Natural disasters or force majeure events</li>
                </ul>
                <p>
                    We are not responsible for delays caused by carriers or factors beyond our control.
                </p>

                <h2>12. Holiday Shipping</h2>
                <p>
                    During peak seasons (Christmas, Valentine's Day, Mother's Day):
                </p>
                <ul>
                    <li>Order early to ensure timely delivery</li>
                    <li>Processing times may be extended to 2-3 business days</li>
                    <li>Carrier delivery times may be longer</li>
                    <li>Cutoff dates for guaranteed holiday delivery will be posted on our website</li>
                </ul>

                <h2>13. Multiple Item Orders</h2>
                <p>
                    If you order multiple items:
                </p>
                <ul>
                    <li>We strive to ship all items together</li>
                    <li>Items may ship separately if from different warehouses</li>
                    <li>You will receive separate tracking numbers for each shipment</li>
                    <li>No additional shipping charges for split shipments</li>
                </ul>

                <h2>14. Return Shipping</h2>
                <p>
                    For information about returning items, please see our 
                    <a href="returns-policy.php">Returns & Refunds Policy</a>.
                </p>

                <h2>15. Contact Us About Shipping</h2>
                <p>
                    If you have questions about shipping or need assistance with your order:
                </p>

                <div class="contact-section">
                    <h3><i class="fas fa-envelope"></i> Shipping Inquiries</h3>
                    <p>
                        <strong>Email:</strong> shipping@ame-jewelry.com<br>
                        <strong>Customer Service:</strong> support@ame-jewelry.com<br>
                        <strong>Phone:</strong> +44 1234 567890<br>
                        <strong>Hours:</strong> Monday-Friday, 9:00 AM - 6:00 PM GMT
                    </p>
                    <p>
                        Please include your order number when contacting us about shipping issues.
                    </p>
                </div>

                <div class="info-box" style="margin-top: 2rem;">
                    <strong><i class="fas fa-truck"></i> Track Your Order:</strong> 
                    Visit our <a href="order_tracking.php">Order Tracking Page</a> to check the status of your 
                    delivery anytime.
                </div>
            </div>
        </div>
    </div>

</body>
</html>