<?php
/**
 * Database Connection Setup
 *
 * This file handles the connection to the MySQL database 'event_booking'
 * using the mysqli extension.
 */

// Database configuration constants
if (!defined('DB_HOST'))
    define('DB_HOST', 'localhost');
if (!defined('DB_USER'))
    define('DB_USER', 'root');
if (!defined('DB_PASS'))
    define('DB_PASS', '');
if (!defined('DB_NAME'))
    define('DB_NAME', 'event_booking');

// Enable mysqli error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Create connection
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Set charset to utf8mb4 for full Unicode support
    $conn->set_charset("utf8mb4");

} catch (mysqli_sql_exception $e) {
    // Connection failed - show professional error and stop execution
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection failed. Please try again later. Error: " . $e->getMessage());
}
