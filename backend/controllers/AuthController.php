<?php
require_once '../models/User.php';

class AuthController {
    private $userModel;

    public function __construct($pdo) {
        $this->userModel = new User($pdo);
    }

    // Handle login
    public function login($email, $password) {
        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            return ['success' => false, 'message' => 'Email not found'];
        }

        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Wrong password'];
        }

        // Start session and store user info
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['name'];

        return ['success' => true, 'role' => $user['role']];
    }

    // Handle register
    public function register($name, $email, $password, $role) {
        $existing = $this->userModel->findByEmail($email);

        if ($existing) {
            return ['success' => false, 'message' => 'Email already exists'];
        }

        $this->userModel->create($name, $email, $password, $role);
        return ['success' => true, 'message' => 'Account created successfully'];
    }

    // Handle logout
    public function logout() {
        session_start();
        session_destroy();
        header('Location: /login');
    }
}
?>