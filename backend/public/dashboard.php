<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /pre-project-tracking/backend/public/?route=login&error=' . urlencode('You must be logged in to access the dashboard.'));
    exit;
}

// You can also include your database connection if needed here
// require_once '../config/database.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PFE Tracker</title>
    <!-- Link to your frontend CSS -->
    <link rel="stylesheet" href="/pre-project-tracking/frontend/css/style.css">
    <!-- Add any specific JS if needed -->
</head>
<body>
    <h1>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h1>
    <p>Your role: <?= htmlspecialchars($_SESSION['user_role']) ?></p>

    <!-- Your dashboard content goes here -->
    <p>This is your dashboard.</p>

    <a href="/pre-project-tracking/backend/public/logout.php">Logout</a> 
    <!-- Note: You'll need to create logout.php -->

</body>
</html>