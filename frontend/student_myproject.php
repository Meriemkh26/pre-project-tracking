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
$stmt = $pdo->prepare("SELECT p.*, u.name as teacher_name FROM projects p LEFT JOIN users u ON p.teacher_id = u.id WHERE p.student_id = ? ORDER BY p.submitted_at DESC LIMIT 1");
$stmt->execute([$userId]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Project - PFE Tracker</title>
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
      <a href="student_myproject.php" class="active">My Project</a>
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
        </div>
      </div>

      <div class="user-menu">
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

    <h3 class="section-title">My Project Details</h3>

    <?php if ($project): ?>
    <div class="big-card">
      <h3 style="font-size:20px; margin-bottom:8px;"><?= htmlspecialchars($project['title']) ?></h3>
      <p class="light-text" style="margin-bottom:6px;"><?= htmlspecialchars($project['description']) ?></p>
      <p class="light-text" style="margin-bottom:4px;">
        <strong>Supervisor:</strong> 
        <?= $project['teacher_name'] ? htmlspecialchars($project['teacher_name']) : 'Not assigned yet' ?>
      </p>
      <p class="light-text">
        <strong>Submission Date:</strong> 
        <?= date('M d, Y', strtotime($project['submitted_at'])) ?>
      </p>
      <div style="margin-top:12px;">
        <span class="badge progress"><?= ucfirst(str_replace('_', ' ', $project['status'])) ?></span>
      </div>
    </div>
    <?php else: ?>
    <div class="big-card">
      <p>You have no project yet.</p>
    </div>
    <?php endif; ?>

  </div>

  <script src="script.js"></script>

</body>
</html>