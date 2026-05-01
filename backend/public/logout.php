<?php
session_start();
session_destroy();
header('Location: /pre-project-tracking/backend/public/?route=login'); // Redirect to login page
exit;
?>