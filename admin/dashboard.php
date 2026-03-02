<?php
/**
 * Admin Dashboard
 *
 * Provides administrative controls for users, events, and reports.
 */

session_start();

// Access control: Ensure user is logged in and is an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php?error=Access denied. Admins only.");
    exit();
}

$admin_name = $_SESSION['user_name'] ?? 'Administrator';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Event Booking</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --sidebar-bg: #111827;
            --sidebar-text: #9ca3af;
            --sidebar-hover: #1f2937;
            --bg: #f3f4f6;
            --card: #ffffff;
            --border: #e5e7eb;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            color: white;
            display: flex;
            flex-direction: column;
            padding: 1.5rem;
            flex-shrink: 0;
        }

        .sidebar h2 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 2rem;
            color: white;
        }

        .nav-links {
            list-style: none;
            flex-grow: 1;
        }

        .nav-links li {
            margin-bottom: 0.5rem;
        }

        .nav-links a {
            display: block;
            padding: 0.75rem 1rem;
            color: var(--sidebar-text);
            text-decoration: none;
            border-radius: 8px;
            transition: background 0.2s, color 0.2s;
        }

        .nav-links li.active a,
        .nav-links a:hover {
            background-color: var(--sidebar-hover);
            color: white;
        }

        .logout-btn {
            margin-top: auto;
            color: #f87171;
            padding: 0.75rem 1rem;
            text-decoration: none;
            display: block;
            font-weight: 500;
        }

        /* Main Content Styling */
        .main-content {
            flex-grow: 1;
            padding: 2rem;
            overflow-y: auto;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        header h1 {
            font-size: 1.5rem;
            color: #111827;
        }

        .card {
            background: var(--card);
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .card h3 {
            margin-bottom: 1rem;
            font-size: 1.125rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 1rem;
            border-bottom: 2px solid var(--border);
            color: #4b5563;
            font-size: 0.875rem;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
            font-size: 0.875rem;
        }

        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            background: #dcfce7;
            color: #166534;
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
            }
        }
    </style>
</head>

<body>
    <aside class="sidebar">
        <h2>Admin Panel</h2>
        <ul class="nav-links">
            <li class="active"><a href="#">Dashboard</a></li>
            <li><a href="#">Manage Users</a></li>
            <li><a href="#">Manage Events</a></li>
            <li><a href="#">Reports</a></li>
        </ul>
        <a href="../logout.php" class="logout-btn">Log out</a>
    </aside>

    <main class="main-content">
        <header>
            <h1>Welcome, <?php echo htmlspecialchars($admin_name); ?>!</h1>
        </header>

        <section class="card">
            <h3>Recent Users</h3>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Registered</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>John Smith</td>
                        <td>john.smith@example.com</td>
                        <td><span class="status-badge">Organizer</span></td>
                        <td>2026-03-01</td>
                    </tr>
                    <tr>
                        <td>Sarah Jones</td>
                        <td>sarah@test.com</td>
                        <td><span class="status-badge" style="background:#e0e7ff; color:#3730a3;">Customer</span></td>
                        <td>2026-02-28</td>
                    </tr>
                    <tr>
                        <td>Mike Miller</td>
                        <td>mike@evently.com</td>
                        <td><span class="status-badge">Organizer</span></td>
                        <td>2026-02-27</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </main>
</body>

</html>