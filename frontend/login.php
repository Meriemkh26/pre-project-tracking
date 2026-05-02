<?php
session_start();
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['user_role'];
    if ($role === 'student') header('Location: ../frontend/student_dashboard.php');
    elseif ($role === 'teacher') header('Location: ../frontend/teacher_dashboard.php');
    elseif ($role === 'admin') header('Location: ../frontend/admin_dashboard.php');
    exit;
}

$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - PFE Tracker</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="login-body">

  <div class="login-card">

    <div class="logo">
      <img src="logo.png" alt="PFE Tracker Logo">
    </div>

    <h1 class="title">Welcome Back</h1>
    <p class="subtitle">Sign in to continue to PFE Tracker</p>

    <?php if ($error): ?>
      <div class="error-message" style="color:red; margin-bottom:10px;">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <!-- ROLE SELECTION -->
    <div class="role-tabs">
      <button class="role student active" onclick="setRole('student')">Student</button>
      <button class="role teacher" onclick="setRole('teacher')">Teacher</button>
      <button class="role admin" onclick="setRole('admin')">Admin</button>
    </div>

    <div class="role-hint" id="roleHint">
      <i class="fa-solid fa-circle-info" style="margin-right: 6px;"></i>
      <span>Logging in as: Student</span>
    </div>

    <form method="POST" action="../backend/public/index.php?route=login">
      <input type="hidden" name="role" id="roleInput" value="student">

      <div class="input-box">
        <input type="email" name="email" id="email" placeholder="Email address" autocomplete="email" required>
      </div>

      <div class="input-box password-box">
        <input type="password" name="password" id="password" placeholder="Password" autocomplete="current-password" required>
        <i class="fa-regular fa-eye toggle-password" onclick="togglePassword('password', this)"></i>
      </div>

      <button type="submit" class="login-btn">
        <i class="fa-solid fa-right-to-bracket" style="margin-right: 8px;"></i>
        Sign In
      </button>
    </form>

    <p class="register-text">
      Don't have an account? <a href="register.php">Create one here</a>
    </p>

  </div>

  <!-- ERROR TOAST -->
  <div class="toast error-toast" id="errorToast">
    <i class="fa-solid fa-circle-xmark"></i>
    <span id="errorMessage"></span>
  </div>

  <script src="script.js"></script>
  <script>
    function setRole(role) {
      selectedRole = role;
      const buttons = document.querySelectorAll(".role");
      buttons.forEach(btn => btn.classList.remove("active"));
      if (role === 'student') buttons[0].classList.add("active");
      if (role === 'teacher') buttons[1].classList.add("active");
      if (role === 'admin') buttons[2].classList.add("active");

      document.getElementById("roleInput").value = role;
      document.getElementById("roleHint").innerHTML =
        '<i class="fa-solid fa-circle-info" style="margin-right:6px;"></i> Logging in as: '
        + role.charAt(0).toUpperCase() + role.slice(1);
    }
  </script>

</body>
</html>