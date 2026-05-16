<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /pre-project-tracking/frontend/login.php');
    exit;
}

require_once '../backend/config/database.php';

$userName = $_SESSION['user_name'];

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
    $deleteId = intval($_POST['delete_user_id']);
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$deleteId]);
    header('Location: admin_users.php?success=User deleted successfully');
    exit;
}
// Handle add user
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
      $newName = trim($_POST['new_name'] ?? '');
      $newEmail = trim($_POST['new_email'] ?? '');
      $newPassword = $_POST['new_password'] ?? '';
      $newRole = $_POST['new_role'] ?? 'student';

      if ($newName && $newEmail && $newPassword) {
          $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
          $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
          $stmt->execute([$newName, $newEmail, $hashed, $newRole]);
          header('Location: admin_users.php?success=User added successfully');
          exit;
      }
  }

$successMsg = $_GET['success'] ?? '';

// Get all users
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Users - PFE Tracker</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

 
  <div class="navbar admin-nav">
    <div class="nav-left">
      <div class="logo-text">
        <img src="logo.png" alt="Logo">
        <span>PFE Tracker</span>
      </div>
    </div>

    <div class="menu">
      <a href="admin_dashboard.php">Dashboard</a>
      <a href="admin_users.php" class="active">Users</a>
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

    <h3 class="section-title">All Users</h3>
    <!-- ADD USER FORM -->
    <div class="big-card" style="margin-bottom:24px;">
      <h4 style="margin-bottom:16px;"><i class="fa-solid fa-user-plus" style="margin-right:8px; color:#7c6fcd;"></i>Add New User</h4>
      <form method="POST">
        <div class="form-row">
          <div class="form-group">
            <label>Full Name</label>
            <input class="form-input" type="text" name="new_name" placeholder="Full Name" required>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input class="form-input" type="email" name="new_email" placeholder="Email" required>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Password</label>
            <input class="form-input" type="password" name="new_password" placeholder="Password" required>
          </div>
          <div class="form-group">
            <label>Role</label>
            <select name="new_role" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; font-family:Poppins, sans-serif; font-size:14px;">
              <option value="student">Student</option>
              <option value="teacher">Teacher</option>
              <option value="admin">Admin</option>
            </select>
          </div>
        </div>
        <button type="submit" name="add_user" class="login-btn" style="width:auto; padding:10px 30px ;display:block; margin: 16px auto 0;">
          <i class="fa-solid fa-plus" style="margin-right:8px;"></i>Add User
        </button>
      </form>
    </div>

    <?php if ($successMsg): ?>
      <div style="background:#e6f4ea; color:#2d6a4f; padding:10px 16px; border-radius:8px; margin-bottom:16px;">
        ✅ <?= htmlspecialchars($successMsg) ?>
      </div>
    <?php endif; ?>

    <table class="table">
      <tr>
        <th>Name</th>
        <th>Role</th>
        <th>Email</th>
        <th>Joined</th>
        <th>Actions</th>
      </tr>

      <?php if (empty($users)): ?>
        <tr>
          <td colspan="5" style="text-align:center; color:#999;">No users found.</td>
        </tr>
      <?php else: ?>
        <?php foreach ($users as $user): ?>
          <tr>
            <td>
              <div style="display:flex; align-items:center; gap:10px;">
                <div class="circle purple-bg" style="width:35px; height:35px; font-size:13px; display:flex; align-items:center; justify-content:center; border-radius:50%; color:white; font-weight:600;">
                  <?= strtoupper(substr($user['name'], 0, 2)) ?>
                </div>
                <?= htmlspecialchars($user['name']) ?>
              </div>
            </td>
            <td><?= ucfirst($user['role']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
            <td>
              <div class="table-actions">
                <form method="POST" style="display:inline;"
                  onsubmit="return confirm('Are you sure you want to delete this user?')">
                  <input type="hidden" name="delete_user_id" value="<?= $user['id'] ?>">
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