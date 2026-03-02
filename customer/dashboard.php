<?php
/**
 * Customer Dashboard
 *
 * Personal hub for attendees to browse events and manage their bookings.
 */

session_start();

// Access control: Ensure user is logged in and is a customer
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'customer') {
    header("Location: ../login.php?error=Access denied. Please login as a customer.");
    exit();
}

$customer_name = $_SESSION['user_name'] ?? 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - Event Booking</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --bg: #f8fafc;
            --header: #ffffff;
            --card: #ffffff;
            --border: #e2e8f0;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg); color: #1e293b; }

        header {
            background-color: var(--header);
            border-bottom: 1px solid var(--border);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .nav-container { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 1.5rem; font-weight: 700; color: var(--primary); }
        .nav-links { display: flex; gap: 2rem; list-style: none; }
        .nav-links a { text-decoration: none; color: #64748b; font-weight: 500; transition: color 0.2s; }
        .nav-links a:hover { color: var(--primary); }

        .container { max-width: 1200px; margin: 2rem auto; padding: 0 1.5rem; }
        .hero { margin-bottom: 3rem; }
        .hero h1 { font-size: 2rem; margin-bottom: 0.5rem; }
        .hero p { color: #64748b; }

        .dashboard-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; }

        .section-title { font-size: 1.25rem; font-weight: 600; margin-bottom: 1.5rem; }
        .card { background: var(--card); border-radius: 12px; border: 1px solid var(--border); padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }

        .booking-item { display: flex; gap: 1rem; padding: 1rem 0; border-bottom: 1px solid var(--border); }
        .booking-item:last-child { border-bottom: none; }
        .booking-date { background: #eff6ff; color: var(--primary); padding: 0.5rem; border-radius: 8px; text-align: center; height: fit-content; min-width: 60px; font-weight: 600; }
        .booking-info h4 { margin-bottom: 0.25rem; }
        .booking-info p { font-size: 0.875rem; color: #64748b; }

        .sidebar-card { position: sticky; top: 80px; }
        .btn-outline { display: block; text-align: center; padding: 0.75rem; border: 1px solid var(--primary); color: var(--primary); border-radius: 8px; text-decoration: none; font-weight: 600; margin-top: 1rem; }
        .btn-outline:hover { background: #f5f3ff; }

        @media (max-width: 900px) {
            .dashboard-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <header>
        <div class="nav-container">
            <div class="logo">Evently.</div>
            <ul class="nav-links">
                <li><a href="#">Browse Events</a></li>
                <li><a href="#" style="color: var(--primary);">My Bookings</a></li>
                <li><a href="#">Profile</a></li>
                <li><a href="../logout.php" style="color: #ef4444;">Logout</a></li>
            </ul>
        </div>
    </header>

    <div class="container">
        <div class="hero">
            <h1>Welcome, <?php echo htmlspecialchars($customer_name); ?>!</h1>
            <p>Here's what you have coming up.</p>
        </div>

        <div class="dashboard-grid">
            <div class="main-column">
                <h3 class="section-title">Upcoming Bookings</h3>
                <div class="card">
                    <div class="booking-item">
                        <div class="booking-date">MAR<br>15</div>
                        <div class="booking-info">
                            <h4>Tech Summit 2026</h4>
                            <p>Ticket: General Admission • Venue: Convention Center</p>
                        </div>
                    </div>
                    <div class="booking-item">
                        <div class="booking-date">APR<br>02</div>
                        <div class="booking-info">
                            <h4>Acoustic Night Live</h4>
                            <p>Ticket: VIP Lounge • Venue: The Blue Room</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sidebar-column">
                <h3 class="section-title">Quick Actions</h3>
                <div class="card sidebar-card">
                    <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 1rem;">Looking for more fun?</p>
                    <a href="#" class="btn-outline">Browse All Events</a>
                    <a href="#" class="btn-outline" style="border-color:#e2e8f0; color:#1e293b;">Edit Profile</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>