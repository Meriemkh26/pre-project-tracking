<?php
require_once '../config/database.php';

$request = $_SERVER['REQUEST_URI'];

switch ($request) {
    case '/':
    case '/login':
        require '../views/login.php';
        break;
    default:
        http_response_code(404);
        echo "404 - Page not found";
        break;
}
?>