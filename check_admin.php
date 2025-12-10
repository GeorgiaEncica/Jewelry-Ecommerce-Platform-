<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "jewelry_database");
if ($conn->connect_error) {
    die("Connection failed.");
}

// Get form values safely
$username = trim($_POST['username']);
$password = trim($_POST['password']);

// Fetch admin from database
$stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = ? LIMIT 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();

    // Verify hashed password
    if (password_verify($password, $admin['password'])) {

        // Set session login data
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['last_activity'] = time();

        // Redirect to the page they originally wanted to access
        if (isset($_SESSION['redirect_after_login'])) {
            $target = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']);
            header("Location: $target");
        } else {
            header("Location: admin.php");
        }
        exit;
    }
}

// If login fails
header("Location: admin_login.php?error=1");
exit;
