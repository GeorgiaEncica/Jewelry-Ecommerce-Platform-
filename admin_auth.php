<?php
session_start();

$timeout_duration = 120; 

// If admin not logged in → redirect to login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    
    // Save page that admin was trying to access
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    
    header("Location: admin_login.php");
    exit;
}

// If inactive for too long → logout automatically
if (isset($_SESSION['last_activity']) &&
    (time() - $_SESSION['last_activity']) > $timeout_duration) {
    
    session_unset();
    session_destroy();
    header("Location: admin_login.php?timeout=1");
    exit;
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Prevent browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");
?>
