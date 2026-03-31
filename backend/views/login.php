<?php

if (isset($_SESSION['user_id'])) {
    header('Location: /pre-project-tracking/backend/public/dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PFE Tracker</title>
    <link rel="stylesheet" href="/pre-project-tracking/frontend/css/style.css">
</head>
<body>
    <div class="login-bg">
        <div class="login-card">
            <div class="login-logo">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                    <path d="M2 17l10 5 10-5"/>
                    <path d="M2 12l10 5 10-5"/>
                </svg>
            </div>
            <h1 class="login-title">Welcome back</h1>
            <p class="login-sub">Sign in to PFE Tracker</p>

            <div class="role-switcher">
                <button class="role-btn active" id="btn-student" onclick="setRole('student')" type="button">Student</button>
                <button class="role-btn" id="btn-teacher" onclick="setRole('teacher')" type="button">Teacher</button>
                <button class="role-btn" id="btn-admin" onclick="setRole('admin')" type="button">Admin</button>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <form method="POST" action="/pre-project-tracking/backend/public/index.php?route=login">
                <input type="hidden" name="role" id="role-input" value="student">
                <div class="form-group">
                    <label>Email address</label>
                    <input type="email" name="email" placeholder="you@university.dz" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                <button type="submit" class="login-btn" id="login-btn">Sign in</button>
            </form>

            <p class="login-footer">Don't have an account? <a href="#">Register</a></p>
        </div>
    </div>

    <script>
        function setRole(role) {
            const colors = { student: '#7F77DD', teacher: '#D4537E', admin: '#EF9F27' };
            ['student', 'teacher', 'admin'].forEach(r => {
                const btn = document.getElementById('btn-' + r);
                btn.classList.remove('active');
                btn.style.background = 'transparent';
                btn.style.color = '#888780';
            });
            const active = document.getElementById('btn-' + role);
            active.classList.add('active');
            active.style.background = colors[role];
            active.style.color = '#fff';
            document.getElementById('role-input').value = role;
            document.getElementById('login-btn').style.background =
                `linear-gradient(135deg, ${colors[role]}, #D4537E)`;
        }
    </script>
</body>
</html>