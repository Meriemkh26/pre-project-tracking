<?php
session_start();

require_once '../config/database.php';
require_once '../controllers/AuthController.php';

$auth = new AuthController($pdo);

$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request = str_replace('/pre-project-tracking/backend/public', '', $request);
$route = $_GET['route'] ?? $request;

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
                header('Location: /pre-project-tracking/backend/public/dashboard.php');
                exit;
            } else {
                header('Location: /pre-project-tracking/backend/public/?error=' . urlencode($result['message']));
                exit;
            }
        }
        require '../views/login.php';
        break;

    default:
        http_response_code(404);
        echo "404 - Page not found";
        break;
}
?>