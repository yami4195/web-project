<?php
/**
 * Super Admin Setup Script
 *
 * This script is a temporary tool to create an initial super admin user.
 * 
 * SECURITY WARNING:
 * DELETE THIS FILE FROM YOUR SERVER IMMEDIATELY AFTER USE.
 * Leaving this file on a public server is a MAJOR security risk.
 */

require_once 'includes/db.php';

// Configuration for the super admin
$admin_name = 'Admin Test';
$admin_email = 'admin@example.com';
$admin_password = 'admin123';
$admin_role = 'admin';

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Admin Setup - Evently</title>
    <style>
        body { font-family: -apple-system, blinkmacsystemfont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background: #f3f4f6; margin: 0; }
        .card { background: white; padding: 2.5rem; border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); max-width: 500px; width: 100%; border-top: 5px solid #4f46e5; }
        .success { color: #059669; background: #ecfdf5; padding: 1rem; border-radius: 8px; font-weight: 600; margin-bottom: 1.5rem; }
        .error { color: #dc2626; background: #fef2f2; padding: 1rem; border-radius: 8px; font-weight: 600; margin-bottom: 1.5rem; }
        .warning { color: #92400e; background: #fffbeb; padding: 1rem; border-radius: 8px; font-size: 0.9rem; border-left: 4px solid #f59e0b; }
        h1 { margin-top: 0; font-size: 1.5rem; color: #111827; }
        hr { border: 0; border-top: 1px solid #e5e7eb; margin: 1.5rem 0; }
    </style>
</head>
<body>
    <div class='card'>
        <h1>Admin Account Setup</h1>";

try {
    // 1. Check if admin already exists
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $admin_email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<div class='error'>Error: A user with the email '{$admin_email}' already exists.</div>";
    } else {
        // 2. Hash the password
        $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

        // 3. Insert the admin
        $insert_stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $insert_stmt->bind_param("ssss", $admin_name, $admin_email, $hashed_password, $admin_role);

        if ($insert_stmt->execute()) {
            echo "<div class='success'>Admin user created successfully!</div>";
            echo "<p><strong>Email:</strong> {$admin_email}<br>";
            echo "<strong>Password:</strong> {$admin_password}</p>";
            echo "<p>You can now <a href='login.php'>Login here</a>.</p>";
        } else {
            echo "<div class='error'>Error: Could not execute the insertion.</div>";
        }
        $insert_stmt->close();
    }
    $check_stmt->close();

} catch (mysqli_sql_exception $e) {
    echo "<div class='error'>Database Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<p>Please ensure the 'users' table exists with columns: id, name, email, password, role.</p>";
}

echo "<hr>
        <div class='warning'>
            <strong>ACTION REQUIRED:</strong> For security, please <strong>DELETE this file</strong> (<code>create_admin.php</code>) from your project folder immediately.
        </div>
    </div>
</body>
</html>";
