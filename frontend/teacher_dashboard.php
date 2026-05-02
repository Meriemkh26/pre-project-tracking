<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
    header('Location: /pre-project-tracking/frontend/login.php');
    exit;
}

require_once '../backend/config/database.php';

$teacherId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];

// Total students assigned to this teacher
$stmt = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE teacher_id = ?");
$stmt->execute([$teacherId]);
$totalStudents = $stmt->fetchColumn();

// Pending projects
$stmt = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE teacher_id = ? AND status = 'pending'");
$stmt->execute([$teacherId]);
$pendingCount = $stmt->fetchColumn();

// Accepted projects
$stmt = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE teacher_id = ? AND status = 'accepted'");
$stmt->execute([$teacherId]);
$acceptedCount = $stmt->fetchColumn();

// Projects list
$stmt = $pdo->prepare("SELECT p.*, u.name as student_name FROM projects p JOIN users u ON p.student_id = u.id WHERE p.teacher_id = ? ORDER BY p.submitted_at DESC");
$stmt->execute([$teacherId]);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Teacher Dashboard - PFE Tracker</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

  <div class="top-roles">
    <button class="top-role">Student</button>
    <button class="top-role active-teacher">Teacher</button>
    <button class="top-role">Admin</button>
  </div>

  <div class="navbar teacher-nav">
    <div class="nav-left">
      <div class="logo-text">
        <img src="logo.png" alt="Logo">
        <span>PFE Tracker</span>
      </div>
    </div>

    <div class="menu">
      <a href="teacher_dashboard.php" class="active">Dashboard</a>
      <a href="teacher_students.php">Students</a>
      <a href="teacher_projects.php">Projects</a>
      <a href="teacher_reports.php">Reports</a>
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
          <div class="dropdown-item" onclick="window.location.href='teacher_profile.php'">
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
        <p>Total Students</p>
        <h2 class="pink-text"><?= $totalStudents ?></h2>
      </div>
      <div class="card">
        <p>Pending Review</p>
        <h2 class="orange-text"><?= $pendingCount ?></h2>
      </div>
      <div class="card">
        <p>Accepted</p>
        <h2 class="green-text"><?= $acceptedCount ?></h2>
      </div>
    </div>

    <h3 class="section-title">Projects to Review</h3>

    <?php if (empty($projects)): ?>
      <div class="big-card">
        <p class="light-text">No projects assigned to you yet.</p>
      </div>
    <?php else: ?>
      <?php foreach ($projects as $project): ?>
        <div class="project-item">
          <div>
            <h3><?= htmlspecialchars($project['title']) ?></h3>
            <p class="light-text">
              Submitted by: <?= htmlspecialchars($project['student_name']) ?> — 
              <?= date('M d, Y', strtotime($project['submitted_at'])) ?>
            </p>
          </div>
          <span class="badge <?= $project['status'] ?>">
            <?= ucfirst(str_replace('_', ' ', $project['status'])) ?>
          </span>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

  </div>

  <script src="script.js"></script>
</body>
</html>