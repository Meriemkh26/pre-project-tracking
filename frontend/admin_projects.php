<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /pre-project-tracking/frontend/login.php');
    exit;
}

require_once '../backend/config/database.php';

$userName = $_SESSION['user_name'];

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_project_id'])) {
    $deleteId = intval($_POST['delete_project_id']);
    $stmt = $pdo->prepare("DELETE FROM feedback WHERE project_id = ?");
    $stmt->execute([$deleteId]);
    $stmt = $pdo->prepare("DELETE FROM files WHERE project_id = ?");
    $stmt->execute([$deleteId]);
    $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->execute([$deleteId]);
    header('Location: admin_projects.php?success=Project deleted successfully');
    exit;
}

$successMsg = $_GET['success'] ?? '';

// Get all projects
$stmt = $pdo->query("SELECT p.*, u.name as student_name, t.name as teacher_name FROM projects p JOIN users u ON p.student_id = u.id LEFT JOIN users t ON p.teacher_id = t.id ORDER BY p.submitted_at DESC");
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
      <a href="admin_projects.php" class="active">Projects</a>
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

    <h3 class="section-title">All Projects</h3>

    <?php if ($successMsg): ?>
      <div style="background:#e6f4ea; color:#2d6a4f; padding:10px 16px; border-radius:8px; margin-bottom:16px;">
        ✅ <?= htmlspecialchars($successMsg) ?>
      </div>
    <?php endif; ?>

    <table class="table">
      <tr>
        <th>Project Title</th>
        <th>Student</th>
        <th>Teacher</th>
        <th>Submitted</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>

      <?php if (empty($projects)): ?>
        <tr>
          <td colspan="6" style="text-align:center; color:#999;">No projects found.</td>
        </tr>
      <?php else: ?>
        <?php foreach ($projects as $p): ?>
          <tr>
            <td><strong><?= htmlspecialchars($p['title']) ?></strong></td>
            <td><?= htmlspecialchars($p['student_name']) ?></td>
            <td><?= $p['teacher_name'] ? htmlspecialchars($p['teacher_name']) : '<span style="color:#999;">Not assigned</span>' ?></td>
            <td><?= date('M d, Y', strtotime($p['submitted_at'])) ?></td>
            <td>
              <span class="badge <?= $p['status'] ?>">
                <?= ucfirst(str_replace('_', ' ', $p['status'])) ?>
              </span>
            </td>
            <td>
              <div class="table-actions">
                <form method="POST" style="display:inline;"
                  onsubmit="return confirm('Delete this project? This will also delete its files and feedback.')">
                  <input type="hidden" name="delete_project_id" value="<?= $p['id'] ?>">
                  <button type="submit" class="btn btn-sm btn-danger" data-tooltip="Delete">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </table>

  </div>

  <script src="script.js"></script>
</body>
</html>