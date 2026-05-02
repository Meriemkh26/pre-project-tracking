<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header('Location: /pre-project-tracking/frontend/login.php');
    exit;
}

require_once '../backend/config/database.php';

$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];

// Get project
$stmt = $pdo->prepare("SELECT * FROM projects WHERE student_id = ? ORDER BY submitted_at DESC LIMIT 1");
$stmt->execute([$userId]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

// Get files count
$filesCount = 0;
if ($project) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM files WHERE project_id = ?");
    $stmt->execute([$project['id']]);
    $filesCount = $stmt->fetchColumn();
}

// Get feedback count
$feedbackCount = 0;
if ($project) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM feedback WHERE project_id = ?");
    $stmt->execute([$project['id']]);
    $feedbackCount = $stmt->fetchColumn();
}

// Get latest feedback
$latestFeedback = null;
if ($project) {
    $stmt = $pdo->prepare("SELECT f.*, u.name as teacher_name FROM feedback f JOIN users u ON f.teacher_id = u.id WHERE f.project_id = ? ORDER BY f.created_at DESC LIMIT 1");
    $stmt->execute([$project['id']]);
    $latestFeedback = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Dashboard - PFE Tracker</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

  <!-- TOP ROLE SWITCH -->
  <div class="top-roles">
    <button class="top-role active-student">Student</button>
    <button class="top-role">Teacher</button>
    <button class="top-role">Admin</button>
  </div>

  <!-- NAVBAR -->
  <div class="navbar student-nav">
    <div class="nav-left">
      <div class="logo-text">
        <img src="logo.png" alt="Logo">
        <span>PFE Tracker</span>
      </div>
    </div>

    <div class="menu">
      <a href="student_dashboard.php" class="active">Dashboard</a>
      <a href="student_myproject.php">My Project</a>
      <a href="student_files.php">Files</a>
      <a href="student_feedback.php">Feedback</a>
    </div>

    <div class="nav-right">
      <div style="position:relative;">
        <button class="notif-btn" data-tooltip="Notifications">
          <i class="fa-regular fa-bell"></i>
          <span class="notif-badge">3</span>
        </button>

        <div class="notif-panel">
          <h4>Notifications</h4>
          <div class="notif-item">
            <div class="notif-dot"></div>
            <div>
              <p>Dr. Ahmed left new feedback</p>
              <small>2 hours ago</small>
            </div>
          </div>
          <div class="notif-item">
            <div class="notif-dot"></div>
            <div>
              <p>Your project was approved</p>
              <small>Yesterday</small>
            </div>
          </div>
          <div class="notif-item">
            <div class="notif-dot"></div>
            <div>
              <p>Reminder: upload your report</p>
              <small>2 days ago</small>
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

  <!-- CONTENT -->
  <div class="container">

    <div class="cards">
      <div class="card">
        <p>Project Status</p>
        <h2 class="purple-text">
          <?= $project ? ucfirst(str_replace('_', ' ', $project['status'])) : 'No Project Yet' ?>
        </h2>
      </div>

      <div class="card">
        <p>Files Uploaded</p>
        <h2 class="purple-text"><?= $filesCount ?></h2>
      </div>

      <div class="card">
        <p>Feedback Received</p>
        <h2 class="pink-text"><?= $feedbackCount ?></h2>
      </div>
    </div>

    <h3 class="section-title">My Project</h3>

    <div class="big-card">
      <?php if ($project): ?>
        <div>
          <h3><?= htmlspecialchars($project['title']) ?></h3>
          <p class="light-text">Submitted: <?= date('M d, Y', strtotime($project['submitted_at'])) ?></p>
        </div>
        <span class="badge progress"><?= ucfirst(str_replace('_', ' ', $project['status'])) ?></span>
      <?php else: ?>
        <p>You have no project yet.</p>
      <?php endif; ?>
    </div>

    <h3 class="section-title">Latest Feedback</h3>

    <div class="big-card">
      <?php if ($latestFeedback): ?>
        <p style="font-style:italic; color:#555;">"<?= htmlspecialchars($latestFeedback['comment']) ?>"</p>
        <p class="light-text"><?= htmlspecialchars($latestFeedback['teacher_name']) ?> — <?= date('M d, Y', strtotime($latestFeedback['created_at'])) ?></p>
      <?php else: ?>
        <p>No feedback received yet.</p>
      <?php endif; ?>
    </div>

  </div>

  <script src="script.js"></script>

</body>
</html>