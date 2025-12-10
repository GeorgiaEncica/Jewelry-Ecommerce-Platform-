<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Âme</title>
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

        .gdpr-rights {
            background:rgb(237, 243, 227);
            border-left: 4px solid #4caf50;
            padding: 1.5rem;
            margin: 1.5rem 0;
            border-radius: 4px;
        }

        .gdpr-rights h3 {
            margin-top: 0;
            color: #2e7d32;
        }

        .highlight-box {
            background: #f0f7ff;
            border-left: 4px solid #2196f3;
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

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5rem 0;
        }

        .data-table th,
        .data-table td {
            padding: 1rem;
            text-align: left;
            border: 1px solid #ddd;
        }

        .data-table th {
            background: #f5f5f5;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .policy-card {
                padding: 2rem 1.5rem;
            }

            .policy-header h1 {
                font-size: 28px;
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
                <h1><i class="fas fa-shield-alt"></i> Privacy Policy</h1>
                <p class="last-updated">Last Updated: December 9, 2025</p>
            </div>

            <div class="policy-content">
                <h2>1. Introduction</h2>
                <p>
                    Welcome to Âme. We respect your privacy and are committed to protecting your personal data. 
                    This privacy policy explains how we collect, use, disclose, and safeguard your information when 
                    you visit our website and use our services.
                </p>
                <p>
                    This policy complies with the General Data Protection Regulation (GDPR) and UK Data Protection 
                    Act 2018.
                </p>

                <div class="highlight-box">
                    <strong><i class="fas fa-info-circle"></i> Important:</strong> 
                    Please read this privacy policy carefully. By using our website, you acknowledge that you have 
                    read and understood this policy.
                </div>

                <h2>2. Data Controller</h2>
                <p>
                    Âme is the data controller responsible for your personal data. Our contact details are:
                </p>
                <ul>
                    <li><strong>Company Name:</strong> Âme Jewelry Ltd</li>
                    <li><strong>Address:</strong> 123 Jewelry Lane, London, UK, SW1A 1AA</li>
                    <li><strong>Email:</strong> privacy@ame-jewelry.com</li>
                    <li><strong>Phone:</strong> +44 20 1234 5678</li>
                </ul>

                <h2>3. Information We Collect</h2>

                <h3>3.1 Personal Data You Provide</h3>
                <p>We collect the following personal data when you interact with our website:</p>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Data Collected</th>
                            <th>Purpose</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Order Information</td>
                            <td>Billing/shipping address, phone number, payment details</td>
                            <td>Order processing and delivery</td>
                        </tr>
                        <tr>
                            <td>Communication Data</td>
                            <td>Email content, support tickets, feedback</td>
                            <td>Customer service and support</td>
                        </tr>
                        <tr>
                            <td>Marketing Data</td>
                            <td>Email preferences, marketing consent</td>
                            <td>Marketing communications</td>
                        </tr>
                    </tbody>
                </table>

                <h3>3.2 Data We Collect Automatically</h3>
                <ul>
                    <li><strong>Technical Data:</strong> IP address, browser type, device information, operating system</li>
                    <li><strong>Usage Data:</strong> Pages visited, time spent on pages, links clicked, referral source</li>
                    <li><strong>Cookie Data:</strong> Cookie identifiers and preferences (see our <a href="cookie-policy.php">Cookie Policy</a>)</li>
                </ul>

                <h2>4. How We Use Your Information</h2>
                <p>We use your personal data for the following purposes:</p>

                <h3>4.1 Legal Bases for Processing (GDPR)</h3>
                <ul>
                    <li><strong>Contract Performance:</strong> To process your orders and deliver products</li>
                    <li><strong>Legitimate Interest:</strong> To improve our website, prevent fraud, and enhance security</li>
                    <li><strong>Legal Obligation:</strong> To comply with tax and accounting requirements</li>
                    <li><strong>Consent:</strong> To send marketing communications (you can withdraw consent anytime)</li>
                </ul>

                <h3>4.2 Specific Uses</h3>
                <ul>
                    <li>Processing and fulfilling your orders</li>
                    <li>Communicating with you about your orders and inquiries</li>
                    <li>Sending order confirmations and shipping notifications</li>
                    <li>Processing payments and preventing fraud</li>
                    <li>Improving our website and customer experience</li>
                    <li>Sending marketing communications (with your consent)</li>
                    <li>Complying with legal obligations</li>
                </ul>

                <h2>5. Sharing Your Information</h2>
                <p>We may share your personal data with the following parties:</p>

                <h3>5.1 Service Providers</h3>
                <ul>
                    <li><strong>Payment Processors:</strong> To process transactions securely</li>
                    <li><strong>Shipping Companies:</strong> To deliver your orders</li>
                    <li><strong>Email Services:</strong> To send transactional and marketing emails</li>
                    <li><strong>Cloud Hosting:</strong> To store data securely</li>
                    <li><strong>Analytics Providers:</strong> To analyze website usage</li>
                </ul>

                <h3>5.2 Legal Requirements</h3>
                <p>
                    We may disclose your information if required by law, court order, or to protect our rights, 
                    property, or safety, or that of others.
                </p>

                <h3>5.3 Business Transfers</h3>
                <p>
                    If we merge with or are acquired by another company, your data may be transferred as part of 
                    that transaction.
                </p>

                <div class="highlight-box">
                    <strong><i class="fas fa-lock"></i> Security:</strong> 
                    We ensure all third parties process your data securely and in compliance with GDPR requirements 
                    through data processing agreements.
                </div>

                <h2>6. International Data Transfers</h2>
                <p>
                    Your data may be transferred to and processed in countries outside the UK/EEA. When this occurs, 
                    we ensure adequate safeguards are in place, including:
                </p>
                <ul>
                    <li>EU Standard Contractual Clauses</li>
                    <li>Adequacy decisions by the European Commission</li>
                    <li>Privacy Shield certification (where applicable)</li>
                </ul>

                <h2>7. Data Retention</h2>
                <p>We retain your personal data only as long as necessary for the purposes set out in this policy:</p>
                <ul>
                    <li><strong>Order Data:</strong> 7 years (for tax and accounting purposes)</li>
                    <li><strong>Account Data:</strong> Until you request deletion or close your account</li>
                    <li><strong>Marketing Data:</strong> Until you unsubscribe or withdraw consent</li>
                    <li><strong>Website Analytics:</strong> Up to 26 months</li>
                </ul>

                <div class="gdpr-rights">
                    <h3><i class="fas fa-balance-scale"></i> Your GDPR Rights</h3>
                    <p>Under the GDPR, you have the following rights:</p>
                    <ol>
                        <li><strong>Right to Access:</strong> Request a copy of your personal data</li>
                        <li><strong>Right to Rectification:</strong> Correct inaccurate or incomplete data</li>
                        <li><strong>Right to Erasure:</strong> Request deletion of your data ("right to be forgotten")</li>
                        <li><strong>Right to Restriction:</strong> Limit how we use your data</li>
                        <li><strong>Right to Data Portability:</strong> Receive your data in a machine-readable format</li>
                        <li><strong>Right to Object:</strong> Object to processing based on legitimate interests</li>
                        <li><strong>Right to Withdraw Consent:</strong> Withdraw consent for marketing or cookies</li>
                        <li><strong>Right to Lodge a Complaint:</strong> File a complaint with the ICO (UK supervisory authority)</li>
                    </ol>
                    <p>
                        To exercise any of these rights, please contact us at privacy@ame-jewelry.com. 
                        We will respond within 30 days.
                    </p>
                </div>

                <h2>8. Data Security</h2>
                <p>
                    We implement appropriate technical and organizational measures to protect your personal data, 
                    including:
                </p>
                <ul>
                    <li>SSL/TLS encryption for data transmission</li>
                    <li>Secure password hashing</li>
                    <li>Regular security audits and vulnerability assessments</li>
                    <li>Access controls and employee training</li>
                    <li>Secure backup systems</li>
                </ul>

                <h2>9. Children's Privacy</h2>
                <p>
                    Our website is not intended for children under 16 years of age. We do not knowingly collect 
                    personal data from children. If you believe we have collected data from a child, please contact 
                    us immediately.
                </p>

                <h2>10. Marketing Communications</h2>
                <p>
                    We may send you marketing emails about our products, special offers, and news. You can:
                </p>
                <ul>
                    <li>Opt-out at any time by clicking "unsubscribe" in our emails</li>
                    <li>Update your preferences in your account settings</li>
                    <li>Contact us directly to opt-out</li>
                </ul>
                <p>
                    <strong>Note:</strong> Even if you opt-out of marketing, we will still send essential 
                    transactional emails (order confirmations, shipping updates, etc.).
                </p>

                <h2>11. Cookies and Tracking</h2>
                <p>
                    We use cookies and similar technologies to enhance your experience. For detailed information, 
                    please see our <a href="cookie-policy.php">Cookie Policy</a>.
                </p>

                <h2>12. Third-Party Links</h2>
                <p>
                    Our website may contain links to third-party websites. We are not responsible for the privacy 
                    practices of these websites. Please review their privacy policies before providing any personal 
                    data.
                </p>

                <h2>13. Changes to This Policy</h2>
                <p>
                    We may update this privacy policy from time to time. We will notify you of significant changes 
                    by:
                </p>
                <ul>
                    <li>Posting a notice on our website</li>
                    <li>Sending you an email notification</li>
                    <li>Updating the "Last Updated" date at the top</li>
                </ul>

                <h2>14. Supervisory Authority</h2>
                <p>
                    If you have concerns about how we handle your data, you have the right to lodge a complaint 
                    with the Information Commissioner's Office (ICO):
                </p>
                <ul>
                    <li><strong>Website:</strong> <a href="https://ico.org.uk" target="_blank">ico.org.uk</a></li>
                    <li><strong>Phone:</strong> 0303 123 1113</li>
                    <li><strong>Address:</strong> Information Commissioner's Office, Wycliffe House, Water Lane, Wilmslow, Cheshire, SK9 5AF</li>
                </ul>

                <div class="contact-section">
                    <h3><i class="fas fa-envelope"></i> Contact Us</h3>
                    <p>
                        If you have any questions about this privacy policy or how we handle your data, please contact us:
                    </p>
                    <p>
                        <strong>Email:</strong> privacy@ame-jewelry.com<br>
                        <strong>Address:</strong> Jewelry Street 13, London, UK<br>
                        <strong>Phone:</strong> +44 1234 567890<br>
                        <strong>Data Protection Officer:</strong> dpo@ame-jewelry.com
                    </p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>