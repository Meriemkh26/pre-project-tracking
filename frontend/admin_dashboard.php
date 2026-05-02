<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /pre-project-tracking/frontend/login.php');
    exit;
}

require_once '../backend/config/database.php';

$userName = $_SESSION['user_name'];

// Stats
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$totalUsers = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM projects");
$totalProjects = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM projects WHERE teacher_id IS NULL");
$unassigned = $stmt->fetchColumn();

// Recent users
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
$recentUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Project overview
$stmt = $pdo->query("SELECT COUNT(*) FROM projects WHERE status = 'pending'");
$pendingCount = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM projects WHERE status = 'accepted'");
$acceptedCount = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM projects WHERE status = 'in_progress'");
$inProgressCount = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM projects WHERE status = 'rejected'");
$rejectedCount = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - PFE Tracker</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

  <div class="top-roles">
    <button class="top-role">Student</button>
    <button class="top-role">Teacher</button>
    <button class="top-role active-admin">Admin</button>
  </div>

  <div class="navbar admin-nav">
    <div class="nav-left">
      <div class="logo-text">
        <img src="logo.png" alt="Logo">
        <span>PFE Tracker</span>
      </div>
    </div>

    <div class="menu">
      <a href="admin_dashboard.php" class="active">Dashboard</a>
      <a href="admin_users.php">Users</a>
      <a href="admin_projects.php">Projects</a>
      <a href="admin_assign.php">Assign</a>
    </div>

    <div class="nav-right">
      <div style="position:relative;">
        <button class="notif-btn" data-tooltip="Notifications">
          <i class="fa-regular fa-bell"></i>
          <span class="notif-badge"></span>
        </button>
        <div class="notif-panel">
          <h4>Notifications</h4>
          <div class="notif-item">
            <div class="notif-dot"></div>
            <div>
              <p>Notifications will appear here</p>
              <small>Stay tuned</small>
            </div>
          </div>
        </div>
      </div>

      <div class="user-menu">
        <div class="user-trigger">
          <div class="avatar"></div>
          <span class="user-name-text"><?= htmlspecialchars($userName) ?></span>
          <span class="dropdown-arrow"><i class="fa-solid fa-chevron-down"></i></span>
        </div>
        <div class="dropdown">
          <div class="dropdown-item" onclick="window.location.href='admin_profile.php'">
            <i class="fa-regular fa-user"></i> Profile
          </div>
          <div class="dropdown-item" onclick="logout()">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="container">

    <div class="cards">
      <div class="card">
        <p>Total Users</p>
        <h2 class="orange-text"><?= $totalUsers ?></h2>
      </div>
      <div class="card">
        <p>Total Projects</p>
        <h2 class="purple-text"><?= $totalProjects ?></h2>
      </div>
      <div class="card">
        <p>Unassigned</p>
        <h2 class="pink-text"><?= $unassigned ?></h2>
      </div>
    </div>

    <div class="two-columns">

      <div class="big-card column">
        <h3 class="section-title">Recent Users</h3>
        <?php foreach ($recentUsers as $user): ?>
          <div class="user-item">
            <div class="circle purple-bg">
              <?= strtoupper(substr($user['name'], 0, 2)) ?>
            </div>
            <div>
              <p><?= htmlspecialchars($user['name']) ?></p>
              <p class="light-text"><?= ucfirst($user['role']) ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="big-card column">
        <h3 class="section-title">Project Overview</h3>

        <div class="overview-item">
          <span>Pending</span>
          <span class="badge pending"><?= $pendingCount ?></span>
        </div>
        <div class="overview-item">
          <span>Accepted</span>
          <span class="badge accepted"><?= $acceptedCount ?></span>
        </div>
        <div class="overview-item">
          <span>In Progress</span>
          <span class="badge progress"><?= $inProgressCount ?></span>
        </div>
        <div class="overview-item">
          <span>Rejected</span>
          <span class="badge rejected"><?= $rejectedCount ?></span>
        </div>
      </div>

    </div>

  </div>

  <script src="script.js"></script>
</body>
</html>