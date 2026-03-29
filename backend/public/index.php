<?php
    require_once '../config/database.php';

    $request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $request = str_replace('/pre-project-tracking/backend/public', '', $request);

    switch ($request) {
        case '/':
        case '':
        case '/login':
            require '../views/login.php';
            break;
        default:
            http_response_code(404);
            echo "404 - Page not found";
            break;
    }
?>
