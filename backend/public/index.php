<?php
session_start();

// Include necessary files
require_once '../config/database.php'; // Assuming this provides your $pdo connection
require_once '../controllers/AuthController.php';

// Initialize the AuthController with the database connection
// Ensure $pdo is available here from database.php
if (!isset($pdo)) {
    die("Database connection not available."); // Basic error handling
}
$auth = new AuthController($pdo);

// --- Routing Logic ---
$request_uri = $_SERVER['REQUEST_URI'];
$base_path = '/pre-project-tracking/backend/public'; 

// Clean the request URI to get the route
$request = str_replace($base_path, '', $request_uri);
$route = $_GET['route'] ?? parse_url($request, PHP_URL_PATH); 

if ($route !== '/' && substr($route, -1) === '/') {
    $route = rtrim($route, '/');
}

// Get the HTTP request method
$method = $_SERVER['REQUEST_METHOD'];

// --- Route Handling ---
switch ($route) {
    // --- Login Routes ---
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
                header('Location: /pre-project-tracking/backend/public/?route=login&error=' . urlencode($result['message']));
                exit;
            }
        }
        require '../views/login.php'; 
        break;

    // --- Registration Routes ---
    case '/register':
    case 'register':
        if ($method === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $role = trim($_POST['role'] ?? 'student'); 

            $result = $auth->register($name, $email, $password, $role);

            if ($result['success']) {
                // <<< IMPORTANT: Redirect to the NEW verify-id route >>>
                header('Location: /pre-project-tracking/backend/public/?route=verify-id'); // Changed route
                exit; 
            } else {
                header('Location: /pre-project-tracking/backend/public/?route=register&error=' . urlencode($result['message']));
                exit;
            }
        }
        require '../views/register.php'; 
        break;

    // --- ID Verification Route ---
    case '/verify-id': // <<< NEW ROUTE NAME >>>
    case 'verify-id':
        if ($method === 'POST') {
            // This case handles the POST request from the verify.php JavaScript
            // The AuthController::verifyCardId method will handle the JSON response
            $auth->verifyCardId(); // Call the method that handles JSON response
            // No require_once here, as the method outputs JSON and exits
        } else {
            // For GET requests, display the verification view
            // Ensure the file exists at ../views/verify.php
            require '../views/verify.php'; 
        }
        break;

    // --- Logout Route ---
    case '/logout':
    case 'logout':
         $auth->logout(); 
         break;

    // --- Default 404 Handler ---
    default:
        http_response_code(404);
        echo "404 - Page not found";
        exit; 
}
?>