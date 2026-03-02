<?php
/**
 * Login Handler
 *
 * This file processes login requests, verifies credentials,
 * and redirects users to their respective dashboards based on roles.
 */

session_start();
require_once 'includes/db.php';

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize inputs
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // 1. Validation
    if (empty($email) || empty($password)) {
        header("Location: login.php?error=Please enter both email and password.");
        exit();
    }

    try {
        // 2. Fetch user from database
        $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            // 3. Verify password
            if (password_verify($password, $user['password'])) {
                // Login success!
                // Regenerate session ID for security
                session_regenerate_id(true);

                // Store user info in session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                // 4. Role-based redirection
                switch ($user['role']) {
                    case 'admin':
                        header("Location: admin/dashboard.php");
                        break;
                    case 'organizer':
                        header("Location: organizer/dashboard.php");
                        break;
                    case 'customer':
                        header("Location: customer/dashboard.php");
                        break;
                    default:
                        header("Location: index.php"); // Fallback
                        break;
                }
                exit();
            } else {
                // Password incorrect
                header("Location: login.php?error=Invalid email or password.");
                exit();
            }
        } else {
            // User not found
            header("Location: login.php?error=Invalid email or password.");
            exit();
        }

        $stmt->close();

    } catch (mysqli_sql_exception $e) {
        error_log("Login error: " . $e->getMessage());
        header("Location: login.php?error=A database error occurred. Please try again later.");
        exit();
    }

} else {
    // If not a POST request, redirect to login page
    header("Location: login.php");
    exit();
}
