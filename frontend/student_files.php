<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header('Location: /pre-project-tracking/frontend/login.php');
    exit;
}

require_once '../backend/config/database.php';

$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];

// Get student's project
$stmt = $pdo->prepare("SELECT * FROM projects WHERE student_id = ? LIMIT 1");
$stmt->execute([$userId]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle file upload
$uploadSuccess = '';
$uploadError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file']) && $_FILES['file']['error'] !== 4) {
    if (!$project) {
        $uploadError = 'You need a project before uploading files.';
    } else {
        $file = $_FILES['file'];
        $filename = basename($file['name']);
        $uploadDir = 'C:/xampp/htdocs/pre-project-tracking/uploads/';

        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $filepath = $uploadDir . time() . '_' . $filename;

        if ($file['error'] !== 0) {
    $uploadError = 'Upload error code: ' . $file['error'];
} elseif (move_uploaded_file($file['tmp_name'], $filepath)) {
    $stmt = $pdo->prepare("INSERT INTO files (project_id, filename, filepath, uploaded_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$project['id'], $filename, $filepath]);
    $uploadSuccess = 'File uploaded successfully!';
} else {
    $uploadError = 'Failed to move file. tmp: ' . $file['tmp_name'] . ' | dest: ' . $filepath;
}
    }
}

// Get uploaded files
$files = [];
if ($project) {
    $stmt = $pdo->prepare("SELECT * FROM files WHERE project_id = ? ORDER BY uploaded_at DESC");
    $stmt->execute([$project['id']]);
    $files = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Project Files - PFE Tracker</title>
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
      <a href="student_myproject.php">My Project</a>
      <a href="student_files.php" class="active">Files</a>
      <a href="student_feedback.php">Feedback</a>
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

    <h3 class="section-title">Project Files</h3>

    <?php if ($uploadSuccess): ?>
      <div style="background:#e6f4ea; color:#2d6a4f; padding:10px 16px; border-radius:8px; margin-bottom:16px;">
        ✅ <?= $uploadSuccess ?>
      </div>
    <?php endif; ?>

    <?php if ($uploadError): ?>
      <div style="background:#fde8e8; color:#c0392b; padding:10px 16px; border-radius:8px; margin-bottom:16px;">
        ❌ <?= $uploadError ?>
      </div>
    <?php endif; ?>

    <div class="big-card">

      <form method="POST" enctype="multipart/form-data">
        <div style="margin-top:12px; text-align:center;">
          <i class="fa-solid fa-cloud-arrow-up" style="font-size:40px; color:#7c6fcd;"></i>
          <h3>Upload Files</h3>
          <p class="light-text">Select a file below</p>
          <input type="file" name="file" id="fileInput" style="margin-top:10px;">
          <button type="submit" class="login-btn" style="margin-top:10px; width:auto; padding: 10px 30px;">
            <i class="fa-solid fa-upload" style="margin-right:8px;"></i> Upload
          </button>
        </div>
      </form>

      <div class="file-list" style="margin-top:24px;">
        <?php if (empty($files)): ?>
          <p class="light-text">No files uploaded yet.</p>
        <?php else: ?>
          <?php foreach ($files as $file): ?>
            <div class="file-item" style="display:flex; justify-content:space-between; align-items:center; padding:12px 0; border-bottom:1px solid #eee;">
              <div>
                <i class="fa-regular fa-file" style="margin-right:8px; color:#7c6fcd;"></i>
                <strong><?= htmlspecialchars($file['filename']) ?></strong>
                <p class="light-text" style="margin:0; font-size:13px;">
                  Uploaded: <?= date('M d, Y', strtotime($file['uploaded_at'])) ?>
                </p>
              </div>
              <a href="<?= htmlspecialchars($file['filepath']) ?>" download
                style="color:#7c6fcd; text-decoration:none;">
                <i class="fa-solid fa-download"></i>
              </a>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

    </div>

  </div>

  <script src="script.js"></script>
  <script>
  // Override initFileUpload to do nothing on this page
  // PHP handles the upload instead
  function initFileUpload() {}
</script>
</body>
</html>