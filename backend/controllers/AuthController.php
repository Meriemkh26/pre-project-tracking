<?php
require_once '../models/User.php';

class AuthController {
    private $userModel;

    public function __construct($pdo) {
        $this->userModel = new User($pdo);
    }

    public function login($email, $password) {
        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            return ['success' => false, 'message' => 'Email not found'];
        }

        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Wrong password'];
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['name'];

        return ['success' => true, 'role' => $user['role']];
    }

    public function register($name, $email, $password, $role) {
        $existing = $this->userModel->findByEmail($email);

        if ($existing) {
            return ['success' => false, 'message' => 'Email already exists'];
        }

        $this->userModel->create($name, $email, $password, $role);
        return ['success' => true, 'message' => 'Account created successfully'];
    }

    public function logout() {
        session_destroy();
        header('Location: /pre-project-tracking/backend/public/');
        exit;
    }
}
?>