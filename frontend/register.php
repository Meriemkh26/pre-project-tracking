<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: /pre-project-tracking/frontend/student_dashboard.php');
    exit;
}
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - PFE Tracker</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="login-body">

  <div class="login-card">

    <div class="logo">
      <img src="logo.png" alt="PFE Tracker Logo">
    </div>

    <h1 class="title">Create Account</h1>
    <p class="subtitle">Join PFE Tracker today</p>

    <?php if ($error): ?>
      <div class="error-message" style="color:red; margin-bottom:10px;">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <!-- ROLE SELECTION -->
    <div class="role-tabs">
      <button type="button" class="role student active" onclick="setRole('student')">Student</button>
      <button type="button" class="role teacher" onclick="setRole('teacher')">Teacher</button>
      <button type="button" class="role admin" onclick="setRole('admin')">Admin</button>
    </div>

    <form method="POST" action="../backend/public/index.php?route=register">
      <input type="hidden" name="role" id="roleInput" value="student">

      <div class="input-box">
        <input type="text" name="name" placeholder="Full Name" autocomplete="name" required>
      </div>

      <div class="input-box">
        <input type="email" name="email" placeholder="Email address" autocomplete="email" required>
      </div>

      <div class="input-box password-box">
        <input type="password" name="password" id="password" placeholder="Password" autocomplete="new-password" required>
        <i class="fa-regular fa-eye toggle-password" onclick="togglePassword('password', this)"></i>
      </div>

      <div class="input-box password-box">
        <input type="password" id="confirmPassword" placeholder="Confirm Password" autocomplete="new-password" required>
        <i class="fa-regular fa-eye toggle-password" onclick="togglePassword('confirmPassword', this)"></i>
      </div>

      <button type="submit" class="login-btn">
        <i class="fa-solid fa-user-plus" style="margin-right: 8px;"></i>
        Create Account
      </button>
    </form>

    <p class="register-text">
      Already have an account? <a href="login.php">Sign in here</a>
    </p>

  </div>

  <div class="toast error-toast" id="errorToast">
    <i class="fa-solid fa-circle-xmark"></i>
    <span id="errorMessage"></span>
  </div>

  <script src="script.js"></script>
  <script>
    function setRole(role) {
      const buttons = document.querySelectorAll(".role");
      buttons.forEach(btn => btn.classList.remove("active"));
      if (role === 'student') buttons[0].classList.add("active");
      if (role === 'teacher') buttons[1].classList.add("active");
      if (role === 'admin') buttons[2].classList.add("active");
      document.getElementById("roleInput").value = role;
    }
  </script>

</body>
</html>