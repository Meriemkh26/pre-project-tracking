<?php
// Ensure session is started if not already
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
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
        
        
        return ['success' => true, 'role' => $user['role'], 'status' => $user['status']];
    }
        //---registeration function (Modified) ---
        public function register($name, $email, $password, $role) {
            $existing = $this->userModel->findByEmail($email);

            if ($existing) {
                return ['success' => false, 'message' => 'Email already exists'];
            }

            // Store registration details in session for verification step
            $_SESSION['registration_data'] = [
                'name' => $name,
                'email' => $email,
                'password' => $password, // Password will be hashed later
                'role' => $role,
                'status' => 'pending_verification' // Mark as pending
            ];

            // Redirect to the QR verification page
            // The actual redirect will happen in index.php after this function returns
            return ['success' => true, 'message' => 'Please verify your identity with your QR code.'];
        }

        // --- New Function: Complete Registration after QR Scan ---
        public function completeRegistration($scanned_qr_data) {
            // Check if there's pending registration data in the session
            if (!isset($_SESSION['registration_data'])) {
                return json_encode(['success' => false, 'message' => 'No pending registration found. Please try again.']);
            }

            $reg_data = $_SESSION['registration_data'];
            $role = $reg_data['role'];

            // --- DEMO VERIFICATION LOGIC ---
            // In a real app, you'd query a database or external service here
            // to validate the scanned_qr_data against the user's intended role.
            $valid_qr_data = [
                'student' => 'STUDENT_QR_12345',
                'teacher' => 'TEACHER_QR_67890',
                'admin'   => 'ADMIN_QR_ABCDE'
            ];

            // Check if the scanned QR data is valid for the intended role
            if (!isset($valid_qr_data[$role]) || $valid_qr_data[$role] !== $scanned_qr_data) {
                // Clear pending data and return error
                unset($_SESSION['registration_data']);
                return ['success' => false, 'message' => 'Invalid QR code or role mismatch. Please try again.'];
            }
            // --- END DEMO VERIFICATION LOGIC ---

            // If QR code is valid, proceed to create the user
            $name = $reg_data['name'];
            $email = $reg_data['email'];
            $password = $reg_data['password']; // Password is still plain here
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Use the User model to create the user
            $createUserSuccess = $this->userModel->create($name, $email, $hashed_password, $role);

            if ($createUserSuccess) {
                // Clear the pending registration data from session
                unset($_SESSION['registration_data']);
                
                // Optional: Log the user in immediately after successful registration
                // $user = $this->userModel->findByEmail($email); // Fetch the newly created user
                // $_SESSION['user_id'] = $user['id'];
                // $_SESSION['user_role'] = $user['role'];
                // $_SESSION['user_name'] = $user['name'];
                // $_SESSION['user_status'] = 'active'; // Assuming 'active' is the default status after verification

                return ['success' => true, 'message' => 'Account created successfully. You can now log in.'];
            } else {
                // If user creation failed (e.g., DB error)
                unset($_SESSION['registration_data']);
                return json_encode(['success' => false, 'message' => 'Failed to create account. Please try again later.']);
            }
        }

    public function logout() {
        session_destroy();
        header('Location: /pre-project-tracking/frontend/login.php');
        exit;
    }
}
?>