<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
    header('Location: /pre-project-tracking/frontend/login.php');
    exit;
}

require_once '../backend/config/database.php';

$teacherId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];

// Handle accept/reject + feedback submission
$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $projectId = intval($_POST['project_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    $comment = trim($_POST['comment'] ?? '');

    if ($projectId && in_array($action, ['accepted', 'rejected'])) {
        // Update project status
        $stmt = $pdo->prepare("UPDATE projects SET status = ? WHERE id = ? AND teacher_id = ?");
        $stmt->execute([$action, $projectId, $teacherId]);

        // Save feedback if provided
        if (!empty($comment)) {
            $stmt = $pdo->prepare("INSERT INTO feedback (project_id, teacher_id, comment, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$projectId, $teacherId, $comment]);
        }

        $successMsg = 'Project ' . $action . ' successfully!';
    }
}

// Get all projects assigned to this teacher
$stmt = $pdo->prepare("SELECT p.*, u.name as student_name FROM projects p JOIN users u ON p.student_id = u.id WHERE p.teacher_id = ? ORDER BY p.submitted_at DESC");
$stmt->execute([$teacherId]);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Projects - PFE Tracker</title>
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
      <a href="teacher_projects.php" class="active">Projects</a>
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

    <h3 class="section-title">Evaluate Projects</h3>

    <?php if ($successMsg): ?>
      <div style="background:#e6f4ea; color:#2d6a4f; padding:10px 16px; border-radius:8px; margin-bottom:16px;">
        ✅ <?= $successMsg ?>
      </div>
    <?php endif; ?>

    <?php if (empty($projects)): ?>
      <div class="big-card">
        <p class="light-text">No projects assigned to you yet.</p>
      </div>
    <?php else: ?>
      <?php foreach ($projects as $project): ?>
        <div class="big-card" style="margin-bottom:20px;">
          <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
            <div>
              <h3 style="font-size:18px;"><?= htmlspecialchars($project['title']) ?></h3>
              <p class="light-text">
                Student: <?= htmlspecialchars($project['student_name']) ?> —
                <?= date('M d, Y', strtotime($project['submitted_at'])) ?>
              </p>
            </div>
            <span class="badge <?= $project['status'] ?>">
              <?= ucfirst(str_replace('_', ' ', $project['status'])) ?>
            </span>
          </div>

          <p style="color:#555; margin-bottom:16px;"><?= htmlspecialchars($project['description']) ?></p>

          <form method="POST">
            <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
            <div style="margin-bottom:12px;">
              <label style="font-weight:500; display:block; margin-bottom:6px;">
                <i class="fa-regular fa-comment" style="margin-right:6px; color:#7c6fcd;"></i>
                Leave Feedback (optional)
              </label>
              <textarea name="comment" rows="3"
                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; font-family:Poppins, sans-serif; font-size:14px; resize:vertical;"
                placeholder="Write your feedback for the student..."></textarea>
            </div>
            <div style="display:flex; gap:12px;">
              <button type="submit" name="action" value="accepted"
                style="background:linear-gradient(135deg, #27ae60, #2ecc71); color:white; border:none; padding:10px 24px; border-radius:8px; cursor:pointer; font-family:Poppins, sans-serif; font-weight:500;">
                <i class="fa-solid fa-check" style="margin-right:6px;"></i> Accept
              </button>
              <button type="submit" name="action" value="rejected"
                style="background:linear-gradient(135deg, #e74c3c, #c0392b); color:white; border:none; padding:10px 24px; border-radius:8px; cursor:pointer; font-family:Poppins, sans-serif; font-weight:500;">
                <i class="fa-solid fa-xmark" style="margin-right:6px;"></i> Reject
              </button>
            </div>
          </form>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

  </div>

  <script src="script.js"></script>
</body>
</html>