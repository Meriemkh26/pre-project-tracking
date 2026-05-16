<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /pre-project-tracking/frontend/login.php');
    exit;
}

require_once '../backend/config/database.php';

$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];

// Handle profile update
$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (!$name || !$email) {
        $errorMsg = 'Name and email are required.';
    } else {
        if (!empty($newPassword)) {
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!password_verify($currentPassword, $user['password'])) {
                $errorMsg = 'Current password is incorrect.';
            } elseif ($newPassword !== $confirmPassword) {
                $errorMsg = 'New passwords do not match.';
            } elseif (strlen($newPassword) < 6) {
                $errorMsg = 'New password must be at least 6 characters.';
            } else {
                $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
                $stmt->execute([$name, $email, $hashed, $userId]);
                $_SESSION['user_name'] = $name;
                $userName = $name;
                $successMsg = 'Profile updated successfully!';
            }
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
            $stmt->execute([$name, $email, $userId]);
            $_SESSION['user_name'] = $name;
            $userName = $name;
            $successMsg = 'Profile updated successfully!';
        }
    }
}

// Get current user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile - PFE Tracker</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>


  <div class="navbar admin-nav">
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
      <a href="admin_dashboard.php">Dashboard</a>
      <a href="admin_users.php">Users</a>
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

      <div class="user-menu" data-tooltip="Account">
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

    <h3 class="section-title">My Profile</h3>

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

      <div class="profile-header">
        <div class="profile-avatar" id="profileAvatar">
          <?= strtoupper(substr($user['name'], 0, 2)) ?>
        </div>
        <div class="profile-info">
          <h3><?= htmlspecialchars($user['name']) ?></h3>
          <p>Admin</p>
          <p style="color:#DE8389; font-size:13px;"><?= htmlspecialchars($user['email']) ?></p>
        </div>
      </div>

      <hr class="form-divider">

      <form method="POST">
        <div class="profile-form">

          <p class="form-section-title">Personal Information</p>

          <div class="form-row">
            <div class="form-group">
              <label>Full Name</label>
              <input class="form-input" type="text" name="name"
                value="<?= htmlspecialchars($user['name']) ?>" placeholder="Full Name" required>
            </div>
            <div class="form-group">
              <label>Email</label>
              <input class="form-input" type="email" name="email"
                value="<?= htmlspecialchars($user['email']) ?>" placeholder="Email address" required>
            </div>
          </div>

          <hr class="form-divider">
          <p class="form-section-title">Change Password</p>

          <div class="form-group">
            <label>Current Password</label>
            <div class="form-password">
              <input class="form-input" type="password" name="current_password"
                id="currentPassword" placeholder="Current password">
              <i class="fa-regular fa-eye toggle-eye" onclick="toggleEye('currentPassword', this)"></i>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>New Password</label>
              <div class="form-password">
                <input class="form-input" type="password" name="new_password"
                  id="newPassword" placeholder="New password">
                <i class="fa-regular fa-eye toggle-eye" onclick="toggleEye('newPassword', this)"></i>
              </div>
            </div>
            <div class="form-group">
              <label>Confirm Password</label>
              <div class="form-password">
                <input class="form-input" type="password" name="confirm_password"
                  id="confirmNewPassword" placeholder="Confirm new password">
                <i class="fa-regular fa-eye toggle-eye" onclick="toggleEye('confirmNewPassword', this)"></i>
              </div>
            </div>
          </div>

          <button type="submit" class="save-btn">
            <i class="fa-solid fa-floppy-disk" style="margin-right:8px;"></i>Save Changes
          </button>

        </div>
      </form>
    </div>
  </div>

  <script src="script.js"></script>
</body>
</html>