<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header('Location: /pre-project-tracking/frontend/login.php');
    exit;
}

require_once '../backend/config/database.php';

$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];

// Get student's project
$stmt = $pdo->prepare("SELECT * FROM projects WHERE student_id = ? LIMIT 1");
$stmt->execute([$userId]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

// Get all feedback
$feedbacks = [];
if ($project) {
    $stmt = $pdo->prepare("SELECT f.*, u.name as teacher_name FROM feedback f JOIN users u ON f.teacher_id = u.id WHERE f.project_id = ? ORDER BY f.created_at DESC");
    $stmt->execute([$project['id']]);
    $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Feedback - PFE Tracker</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

  <div class="top-roles">
    <button class="top-role active-student">Student</button>
    <button class="top-role">Teacher</button>
    <button class="top-role">Admin</button>
  </div>

  <div class="navbar student-nav">
    <div class="nav-left">
      <button class="back-btn" onclick="history.back()" data-tooltip="Go back">
        <i class="fa-solid fa-arrow-left"></i>
      </button>
      <div class="logo-text">
        <img src="logo.png" alt="Logo">
        <span>PFE Tracker</span>
      </div>
    </div>

    <div class="menu">
      <a href="student_dashboard.php">Dashboard</a>
      <a href="student_myproject.php">My Project</a>
      <a href="student_files.php">Files</a>
      <a href="student_feedback.php" class="active">Feedback</a>
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

      <div class="user-menu" data-tooltip="Account">
        <div class="user-trigger">
          <div class="avatar"></div>
          <span class="user-name-text"><?= htmlspecialchars($userName) ?></span>
          <span class="dropdown-arrow"><i class="fa-solid fa-chevron-down"></i></span>
        </div>
        <div class="dropdown">
          <div class="dropdown-item" onclick="window.location.href='student_profile.php'">
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

    <h3 class="section-title">All Feedback</h3>

    <?php if (empty($feedbacks)): ?>
      <div class="big-card">
        <p class="light-text">No feedback received yet.</p>
      </div>
    <?php else: ?>
      <?php foreach ($feedbacks as $fb): ?>
        <div class="big-card" style="margin-bottom: 16px;">
          <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
            <h4 style="font-size:16px; font-weight:600;">
              <i class="fa-regular fa-user" style="margin-right:6px; color:#7c6fcd;"></i>
              <?= htmlspecialchars($fb['teacher_name']) ?>
            </h4>
            <span class="light-text" style="font-size:12px;">
              <?= date('M d, Y', strtotime($fb['created_at'])) ?>
            </span>
          </div>
          <p style="color:#555; line-height:1.7;"><?= htmlspecialchars($fb['comment']) ?></p>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

  </div>

  <script src="script.js"></script>

</body>
</html>