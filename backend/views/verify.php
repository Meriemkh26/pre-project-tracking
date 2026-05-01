<?php
// Ensure session is started if not already
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: /pre-project-tracking/backend/public/dashboard.php');
    exit;
}

// Redirect to registration if there's no pending registration data
if (!isset($_SESSION['registration_data']) || !is_array($_SESSION['registration_data'])) {
        header('Location: /pre-project-tracking/backend/public/?route=register&error=' . urlencode('Please complete your initial registration first.'));
        exit;
}

// Get pending registration details for display/context
$pending_data = $_SESSION['registration_data'];
$intended_role = $pending_data['role'] ?? 'user'; 
$user_name = $pending_data['name'] ?? 'New User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Identity - PFE Tracker</title>
    <!-- Link to your frontend CSS -->
    <link rel="stylesheet" href="/pre-project-tracking/frontend/css/style.css">
    <style>
        /* Basic styling for the verification page */
        .verification-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 80vh; /* Adjust as needed */
            text-align: center;
            padding: 20px;
            background-color: #f4f7f6; /* Light background */
        }
        .verification-card {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
        }
        .verification-card h1 {
            color: #333;
            margin-bottom: 10px;
        }
        .verification-card p {
            color: #666;
            margin-bottom: 25px;
            font-size: 1.1em;
        }
        /* Styles for input fields */
        .input-group {
            margin-bottom: 20px;
            text-align: left; /* Align labels to the left */
        }
        .input-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        .input-group input[type="text"] {
            width: calc(100% - 20px); /* Full width minus padding */
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        /* Style for the submit button */
        .verification-card .submit-btn { 
            background: linear-gradient(135deg, #7F77DD, #D4537E); 
            color: #fff;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background 0.3s ease;
            width: 100%; /* Make button full width */
        }
        .verification-card .submit-btn:hover {
            opacity: 0.9;
        }
        .alert-error {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .footer-link a {
            color: #7F77DD; 
            text-decoration: none;
        }
        .footer-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="verification-card">
            <h1>Verify Your Identity</h1>
            <p>
                Hello, <strong><?= htmlspecialchars($user_name) ?></strong>!
                You are registering as a <strong><?= htmlspecialchars(ucfirst($intended_role)) ?></strong>.
                <br>
                Please enter your card ID to complete your registration.
            </p>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <!-- Input Field for Card ID -->
            <div class="input-group">
                <label for="card_id">Card ID:</label>
                <input type="text" id="card_id" name="card_id" placeholder="Enter your card ID" required>
            </div>

            <!-- Submit Button -->
            <button id="verify-id-btn" class="submit-btn">Verify ID</button>

            <p class="footer-link" style="margin-top: 20px;">
                <a href="/pre-project-tracking/backend/public/?route=register">Go back to registration</a>
            </p>
        </div>
    </div>

    <script>
        const cardIdInput = document.getElementById('card_id');
        const verifyIdButton = document.getElementById('verify-id-btn');

        // Function to send card ID to the backend
        function submitCardId(cardId) {
            // IMPORTANT: Ensure this route matches what you define in index.php
            fetch('/pre-project-tracking/backend/public/?route=verify-id', { 
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ card_id: cardId }), // Send card_id
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert(data.message || 'Verification successful! You can now log in.');
                    window.location.href = '/pre-project-tracking/backend/public/?route=login';
                } else {
                    alert(data.message || 'Verification failed. Please try again.');
                    // Reload the current page to allow retry, showing the error
                    window.location.href = '/pre-project-tracking/backend/public/?route=verify-id&error=' + encodeURIComponent(data.message || 'Verification failed.');
                }
            })
            .catch((error) => {
                console.error('Error during fetch:', error);
                alert('An error occurred during verification. Please check the console for details.');
                window.location.href = '/pre-project-tracking/backend/public/?route=verify-id&error=' + encodeURIComponent('Network error during verification.');
            });
        }

        // Event listener for the verify button
        verifyIdButton.addEventListener('click', () => {
            const cardId = cardIdInput.value.trim();
            if (cardId) {
                verifyIdButton.disabled = true; // Disable button to prevent multiple submissions
                verifyIdButton.textContent = 'Verifying...';
                submitCardId(cardId);
            } else {
                alert('Please enter your card ID.');
            }
        });

        // Optional: Allow pressing Enter in the input field to submit
        cardIdInput.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault(); 
                verifyIdButton.click(); 
            }
        });

    </script>
</body>
</html>