<?php
    // Ensure session is started if not already
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Redirect to dashboard if the user is already logged in
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
        <title>Register - PFE Tracker</title>
        <!-- Link to your frontend CSS -->
        <link rel="stylesheet" href="/pre-project-tracking/frontend/css/style.css">
        <style>
            /* Basic styling for the role buttons, similar to login */
            .role-switcher {
                display: flex;
                gap: 10px;
                margin-bottom: 20px;
                justify-content: center;
            }
            .role-btn {
                padding: 10px 15px;
                border: 1px solid #888780;
                background: transparent;
                color: #888780;
                cursor: pointer;
                border-radius: 5px;
                font-weight: bold;
                transition: background 0.3s, color 0.3s;
            }
            .role-btn.active {
                color: #fff;
            }
            .login-bg .login-card .login-btn {
                /* Inherit login button style or define specific one */
                background: linear-gradient(135deg, #7F77DD, #D4537E); /* Default gradient */
                color: #fff;
                border: none;
                padding: 12px 20px;
                border-radius: 5px;
                cursor: pointer;
                font-size: 16px;
                font-weight: bold;
                margin-top: 20px;
                width: 100%;
            }
            .login-bg .login-card .login-btn:hover {
                opacity: 0.9;
            }
            .alert-error, .alert-success {
                padding: 10px;
                margin-bottom: 15px;
                border-radius: 5px;
                text-align: center;
            }
            .alert-error {
                background-color: #f8d7da;
                color: #721c24;
                border: 1px solid #f5c6cb;
            }
            .alert-success {
                background-color: #d4edda;
                color: #155724;
                border: 1px solid #c3e6cb;
            }
        </style>
    </head>
    <body>
        <div class="login-bg">
            <div class="login-card">
                <div class="login-logo">
                    <!-- Your SVG Logo -->
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                        <path d="M2 17l10 5 10-5"/>
                        <path d="M2 12l10 5 10-5"/>
                    </svg>
                </div>
                <h1 class="login-title">Create your account</h1>
                <p class="login-sub">Sign up for PFE Tracker</p>

                <div class="role-switcher">
                    <button class="role-btn active" id="btn-student" onclick="setRole('student')" type="button">Student</button>
                    <button class="role-btn" id="btn-teacher" onclick="setRole('teacher')" type="button">Teacher</button>
                    <button class="role-btn" id="btn-admin" onclick="setRole('admin')" type="button">Admin</button>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
                <?php endif; ?>
                <?php if (isset($_GET['message'])): ?>
                    <div class="alert-success"><?= htmlspecialchars($_GET['message']) ?></div>
                <?php endif; ?>

                <!-- Form POSTs to index.php with route=register -->
                <form method="POST" action="/pre-project-tracking/backend/public/?route=register">
                    <input type="hidden" name="role" id="role-input" value="student">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" placeholder="Your Full Name" required>
                    </div>
                    <div class="form-group">
                        <label>Email address</label>
                        <input type="email" name="email" placeholder="you@university.dz" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="login-btn" id="register-btn">Sign up</button>
                </form>

                <p class="login-footer">Already have an account? <a href="/pre-project-tracking/backend/public/?route=login">Sign in</a></p>
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
                // Update the button style for the submit button based on selected role
                document.getElementById('register-btn').style.background =
                    `linear-gradient(135deg, ${colors[role]}, #D4537E)`; // Using a secondary color for gradient
            }

            // Initialize the role selection on page load if needed (optional)
            // Example: setRole('student'); // Or based on a default value if any
        </script>
    </body>
    </html>