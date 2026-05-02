<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
    header('Location: /pre-project-tracking/frontend/login.php');
    exit;
}
$userName = $_SESSION['user_name'];
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

  <!-- TOP ROLE SWITCH -->
  <div class="top-roles">
    <button class="top-role">Student</button>
    <button class="top-role active-teacher">Teacher</button>
    <button class="top-role">Admin</button>
  </div>

  <!-- NAVBAR -->
  <div class="navbar teacher-nav">

    <div class="nav-left">

      <!-- BACK BUTTON -->
      <button class="back-btn" onclick="history.back()" data-tooltip="Go back">
        <i class="fa-solid fa-arrow-left"></i>
      </button>

      <div class="logo-text">
        <img src="logo.png" alt="Logo">
        <span>PFE Tracker</span>
      </div>

    </div>

    <div class="menu">
      <a href="teacher_dashboard.html">Dashboard</a>
      <a href="teacher_students.html">Students</a>
      <a href="teacher_projects.html">Projects</a>
      <a href="teacher_reports.html">Reports</a>
    </div>

    <div class="nav-right">

      <!-- NOTIFICATION -->
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
              <p>Sara submitted a new file</p>
              <small>1 hour ago</small>
            </div>
          </div>
          <div class="notif-item">
            <div class="notif-dot"></div>
            <div>
              <p>Project review pending</p>
              <small>3 hours ago</small>
            </div>
          </div>
        </div>
      </div>

      <!-- USER -->
      <div class="user-menu" data-tooltip="Account">
        <div class="user-trigger">
          <div class="avatar"></div>
          <span class="user-name-text"></span>
          <span class="dropdown-arrow"><i class="fa-solid fa-chevron-down"></i></span>
        </div>

        <div class="dropdown">
          <div class="dropdown-item" onclick="window.location.href='teacher_profile.html'">
            <i class="fa-regular fa-user"></i> Profile
          </div>
          <div class="dropdown-item" onclick="logout()">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- CONTENT -->
  <div class="container">

    <h3 class="section-title">My Profile</h3>

    <div class="big-card">

      <!-- PROFILE HEADER -->
      <div class="profile-header">
        <div class="profile-avatar" id="profileAvatar">??</div>
        <div class="profile-info">
          <h3 id="profileName">Loading...</h3>
          <p id="profileRole">Teacher</p>
          <p id="profileEmail" style="color:#61a2b1; font-size:13px;"></p>
        </div>
      </div>

      <hr class="form-divider">

      <!-- FORM -->
      <div class="profile-form">

        <p class="form-section-title">Personal Information</p>

        <div class="form-row">
          <div class="form-group">
            <label for="editName">Full Name</label>
            <input class="form-input" type="text" id="editName" placeholder="Full Name">
          </div>
          <div class="form-group">
            <label for="editEmail">Email</label>
            <input class="form-input" type="email" id="editEmail" placeholder="Email address">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="editDept">Department</label>
            <input class="form-input" type="text" id="editDept" placeholder="e.g. Computer Science">
          </div>
          <div class="form-group">
            <label for="editSpecialization">Specialization</label>
            <input class="form-input" type="text" id="editSpecialization" placeholder="e.g. Web Development">
          </div>
        </div>

        <hr class="form-divider">
        <p class="form-section-title">Change Password</p>

        <div class="form-group">
          <label>Current Password</label>
          <div class="form-password">
            <input class="form-input" type="password" id="currentPassword" placeholder="Current password">
            <i class="fa-regular fa-eye toggle-eye" onclick="toggleEye('currentPassword', this)"></i>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>New Password</label>
            <div class="form-password">
              <input class="form-input" type="password" id="newPassword" placeholder="New password">
              <i class="fa-regular fa-eye toggle-eye" onclick="toggleEye('newPassword', this)"></i>
            </div>
          </div>
          <div class="form-group">
            <label>Confirm Password</label>
            <div class="form-password">
              <input class="form-input" type="password" id="confirmNewPassword" placeholder="Confirm new password">
              <i class="fa-regular fa-eye toggle-eye" onclick="toggleEye('confirmNewPassword', this)"></i>
            </div>
          </div>
        </div>

        <button class="save-btn" onclick="saveTeacherProfile()">
          <i class="fa-solid fa-floppy-disk" style="margin-right:8px;"></i>Save Changes
        </button>

        <!-- SUCCESS MESSAGE -->
        <div class="toast success-toast" id="successToast">
          <i class="fa-solid fa-circle-check"></i>
          <span>Profile updated successfully!</span>
        </div>

        <!-- ERROR MESSAGE -->
        <div class="toast error-toast" id="errorToast">
          <i class="fa-solid fa-circle-xmark"></i>
          <span id="errorMessage">An error occurred</span>
        </div>

      </div>
    </div>
  </div>

  <script src="script.js"></script>

</body>
</html>
