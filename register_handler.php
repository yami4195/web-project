<?php
/**
 * User Registration Handler
 *
 * This file processes registration requests, validates inputs,
 * hashes passwords, and saves new user records to the database.
 */

require_once 'includes/db.php';

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize inputs
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'customer'; // Default to customer if not provided

    // 1. Validation
    $errors = [];

    if (empty($name)) {
        $errors[] = "Name is required.";
    }

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    if (empty($role)) {
        $errors[] = "Role is required.";
    }

    // If there are validation errors, stop and show them
    if (!empty($errors)) {
        die("Registration failed: " . implode(" ", $errors));
    }

    try {
        // 2. Check if email already exists
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            die("Error: A user with this email already exists.");
        }
        $check_stmt->close();

        // 3. Hash the password securely
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 4. Insert the new user into the database
        $insert_stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $insert_stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

        if ($insert_stmt->execute()) {
            echo "Success: User registered successfully!";
        } else {
            echo "Error: Could not complete registration.";
        }
        $insert_stmt->close();

    } catch (mysqli_sql_exception $e) {
        error_log("Registration error: " . $e->getMessage());
        die("Database error: " . $e->getMessage());
    }

} else {
    // If not a POST request, redirect or show error
    die("Invalid request method. Please use POST.");
}
