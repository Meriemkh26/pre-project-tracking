<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /pre-project-tracking/frontend/login.php');
    exit;
}

require_once '../backend/config/database.php';

$userName = $_SESSION['user_name'];

// Handle assignment
$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $projectId = intval($_POST['project_id'] ?? 0);
    $teacherId = intval($_POST['teacher_id'] ?? 0);

    if ($projectId && $teacherId) {
        $stmt = $pdo->prepare("UPDATE projects SET teacher_id = ? WHERE id = ?");
        $stmt->execute([$teacherId, $projectId]);
        $successMsg = 'Teacher assigned successfully!';
    } else {
        $errorMsg = 'Please select both a project and a teacher.';
    }
}

// Get all projects
$stmt = $pdo->query("SELECT p.*, u.name as student_name, t.name as teacher_name FROM projects p JOIN users u ON p.student_id = u.id LEFT JOIN users t ON p.teacher_id = t.id ORDER BY p.submitted_at DESC");
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all teachers
$stmt = $pdo->query("SELECT * FROM users WHERE role = 'teacher' ORDER BY name");
$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Assign - PFE Tracker</title>
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
      <a href="admin_dashboard.php">Dashboard</a>
      <a href="admin_users.php">Users</a>
      <a href="admin_projects.php">Projects</a>
      <a href="admin_assign.php" class="active">Assign</a>
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

    <h3 class="section-title">Assign Teacher to Project</h3>

    <?php if ($successMsg): ?>
      <div style="background:#e6f4ea; color:#2d6a4f; padding:10px 16px; border-radius:8px; margin-bottom:16px;">
        ✅ <?= htmlspecialchars($successMsg) ?>
      </div>
    <?php endif; ?>

    <?php if ($errorMsg): ?>
      <div style="background:#fde8e8; color:#c0392b; padding:10px 16px; border-radius:8px; margin-bottom:16px;">
        ❌ <?= htmlspecialchars($errorMsg) ?>
      </div>
    <?php endif; ?>

    <div class="big-card">
      <form method="POST">

        <div style="margin-bottom:16px;">
          <label style="font-weight:500; display:block; margin-bottom:6px;">Project</label>
          <select name="project_id" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; font-family:Poppins, sans-serif; font-size:14px;">
            <option value="">-- Select a project --</option>
            <?php foreach ($projects as $p): ?>
              <option value="<?= $p['id'] ?>">
                <?= htmlspecialchars($p['title']) ?> — <?= htmlspecialchars($p['student_name']) ?>
                <?= $p['teacher_name'] ? '(Currently: ' . htmlspecialchars($p['teacher_name']) . ')' : '(Unassigned)' ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div style="margin-bottom:24px;">
          <label style="font-weight:500; display:block; margin-bottom:6px;">Teacher</label>
          <select name="teacher_id" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; font-family:Poppins, sans-serif; font-size:14px;">
            <option value="">-- Select a teacher --</option>
            <?php foreach ($teachers as $t): ?>
              <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <button type="submit" class="login-btn">
          <i class="fa-solid fa-user-check" style="margin-right:8px;"></i>
          Assign Teacher
        </button>

      </form>
    </div>

    <!-- CURRENT ASSIGNMENTS TABLE -->
    <h3 class="section-title" style="margin-top:32px;">Current Assignments</h3>

    <table class="table">
      <tr>
        <th>Project</th>
        <th>Student</th>
        <th>Assigned Teacher</th>
        <th>Status</th>
      </tr>
      <?php foreach ($projects as $p): ?>
        <tr>
          <td><?= htmlspecialchars($p['title']) ?></td>
          <td><?= htmlspecialchars($p['student_name']) ?></td>
          <td><?= $p['teacher_name'] ? htmlspecialchars($p['teacher_name']) : '<span style="color:#e67e22;">Not assigned</span>' ?></td>
          <td>
            <span class="badge <?= $p['status'] ?>">
              <?= ucfirst(str_replace('_', ' ', $p['status'])) ?>
            </span>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>

  </div>

  <script src="script.js"></script>
</body>
</html>