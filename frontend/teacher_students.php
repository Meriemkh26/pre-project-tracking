<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
    header('Location: /pre-project-tracking/frontend/login.php');
    exit;
}

require_once '../backend/config/database.php';

$teacherId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];

// Get all students assigned to this teacher
$stmt = $pdo->prepare("SELECT p.*, u.name as student_name, u.email as student_email FROM projects p JOIN users u ON p.student_id = u.id WHERE p.teacher_id = ? ORDER BY p.submitted_at DESC");
$stmt->execute([$teacherId]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Students - PFE Tracker</title>
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
      <a href="teacher_students.php" class="active">Students</a>
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

    <h3 class="section-title">Assigned Students</h3>

    <table class="table">
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Project Title</th>
        <th>Status</th>
      </tr>

      <?php if (empty($students)): ?>
        <tr>
          <td colspan="4" style="text-align:center; color:#999;">No students assigned yet.</td>
        </tr>
      <?php else: ?>
        <?php foreach ($students as $s): ?>
          <tr>
            <td><?= htmlspecialchars($s['student_name']) ?></td>
            <td><?= htmlspecialchars($s['student_email']) ?></td>
            <td><?= htmlspecialchars($s['title']) ?></td>
            <td>
              <span class="badge <?= $s['status'] ?>">
                <?= ucfirst(str_replace('_', ' ', $s['status'])) ?>
              </span>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </table>

  </div>

  <script src="script.js"></script>
</body>
</html>