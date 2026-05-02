<?php
session_start();

require_once '../config/database.php';
require_once '../controllers/AuthController.php';

if (!isset($pdo)) {
    die("Database connection not available.");
}
$auth = new AuthController($pdo);

$request_uri = $_SERVER['REQUEST_URI'];
$base_path = '/pre-project-tracking/backend/public';
$request = str_replace($base_path, '', $request_uri);
$route = $_GET['route'] ?? parse_url($request, PHP_URL_PATH);

if ($route !== '/' && substr($route, -1) === '/') {
    $route = rtrim($route, '/');
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($route) {
    case '/':
    case '':
    case '/login':
    case 'login':
        if ($method === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $result = $auth->login($email, $password);
            if ($result['success']) {
                $role = $result['role'];
                if ($role === 'student') header('Location: /pre-project-tracking/frontend/student_dashboard.php');
                elseif ($role === 'teacher') header('Location: /pre-project-tracking/frontend/teacher_dashboard.php');
                elseif ($role === 'admin') header('Location: /pre-project-tracking/frontend/admin_dashboard.php');
                exit;
            } else {
                header('Location: /pre-project-tracking/frontend/login.php?error=' . urlencode($result['message']));
                exit;
            }
        }
        header('Location: /pre-project-tracking/frontend/login.php');
        exit;

    case '/register':
    case 'register':
        if ($method === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $role = trim($_POST['role'] ?? 'student');

            $result = $auth->register($name, $email, $password, $role);

            if ($result['success']) {
                header('Location: /pre-project-tracking/frontend/login.php?success=Account created! You can now log in.');
                exit;
            } else {
                header('Location: /pre-project-tracking/frontend/register.php?error=' . urlencode($result['message']));
                exit;
            }
        }
        header('Location: /pre-project-tracking/frontend/register.php');
        exit;

    case '/logout':
    case 'logout':
        $auth->logout();
        break;

    default:
        http_response_code(404);
        echo "404 - Page not found";
        exit;
}
?>