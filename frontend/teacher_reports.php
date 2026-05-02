<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
    header('Location: /pre-project-tracking/frontend/login.php');
    exit;
}

require_once '../backend/config/database.php';

$teacherId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];

// Get summary stats
$stmt = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE teacher_id = ?");
$stmt->execute([$teacherId]);
$totalProjects = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE teacher_id = ? AND status = 'accepted'");
$stmt->execute([$teacherId]);
$acceptedProjects = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE teacher_id = ? AND status = 'rejected'");
$stmt->execute([$teacherId]);
$rejectedProjects = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE teacher_id = ? AND status = 'in_progress'");
$stmt->execute([$teacherId]);
$inProgressProjects = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM feedback WHERE teacher_id = ?");
$stmt->execute([$teacherId]);
$totalFeedback = $stmt->fetchColumn();

// Get all projects with details
$stmt = $pdo->prepare("SELECT p.*, u.name as student_name FROM projects p JOIN users u ON p.student_id = u.id WHERE p.teacher_id = ? ORDER BY p.submitted_at DESC");
$stmt->execute([$teacherId]);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reports - PFE Tracker</title>
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
      <a href="teacher_dashboard.php">Dashboard</a>
      <a href="teacher_students.php">Students</a>
      <a href="teacher_projects.php">Projects</a>
      <a href="teacher_reports.php" class="active">Reports</a>
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

    <h3 class="section-title">Reports Overview</h3>

    <!-- STAT CARDS -->
    <div class="cards">
      <div class="card">
        <p>Total Projects</p>
        <h2 class="pink-text"><?= $totalProjects ?></h2>
      </div>
      <div class="card">
        <p>Accepted</p>
        <h2 class="green-text"><?= $acceptedProjects ?></h2>
      </div>
      <div class="card">
        <p>Rejected</p>
        <h2 class="orange-text"><?= $rejectedProjects ?></h2>
      </div>
    </div>

    <!-- PROJECTS TABLE -->
    <h3 class="section-title" style="margin-top:24px;">Project Details</h3>

    <table class="table">
      <tr>
        <th>Student</th>
        <th>Project Title</th>
        <th>Submitted</th>
        <th>Status</th>
        <th>Feedback Given</th>
      </tr>

      <?php if (empty($projects)): ?>
        <tr>
          <td colspan="5" style="text-align:center; color:#999;">No projects yet.</td>
        </tr>
      <?php else: ?>
        <?php foreach ($projects as $p): ?>
          <?php
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM feedback WHERE project_id = ? AND teacher_id = ?");
            $stmt->execute([$p['id'], $teacherId]);
            $feedbackCount = $stmt->fetchColumn();
          ?>
          <tr>
            <td><?= htmlspecialchars($p['student_name']) ?></td>
            <td><?= htmlspecialchars($p['title']) ?></td>
            <td><?= date('M d, Y', strtotime($p['submitted_at'])) ?></td>
            <td>
              <span class="badge <?= $p['status'] ?>">
                <?= ucfirst(str_replace('_', ' ', $p['status'])) ?>
              </span>
            </td>
            <td><?= $feedbackCount ?> comment(s)</td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </table>

  </div>

  <script src="script.js"></script>
</body>
</html>