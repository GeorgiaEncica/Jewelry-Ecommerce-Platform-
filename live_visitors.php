<?php
session_start();

// Creating a unique session ID 
$session_id = session_id();

// DB connection
$conn = new mysqli("localhost", "root", "", "jewelry_database");

// Record/update visitor activity (valid for last 5 minutes)
$stmt = $conn->prepare("REPLACE INTO live_visitors (session_id, last_activity) VALUES (?, NOW())");
$stmt->bind_param("s", $session_id);
$stmt->execute();
$stmt->close();

// Delete sessions older than 5 minutes (no longer active)
$conn->query("DELETE FROM live_visitors WHERE last_activity < (NOW() - INTERVAL 5 MINUTE)");

$conn->close();
?>
