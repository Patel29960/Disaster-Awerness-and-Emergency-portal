<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Include database connection file safely
if (file_exists(__DIR__ . '/config/database.php')) {
    require_once __DIR__ . '/config/database.php';
} elseif (file_exists(__DIR__ . '/database.php')) {
    require_once __DIR__ . '/database.php';
}

// 2. Default session values
$user_name = $_SESSION['user_name'] ?? $_SESSION['username'] ?? "vidisha";
$user_role = $_SESSION['role'] ?? "User";

// 3. Only query the database if $conn exists and is connected
if (isset($conn) && $conn instanceof mysqli && !empty($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT username, role FROM users WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $user_name = $row['username'] ?? $user_name;
            $user_role = $row['role'] ?? $user_role;
        }
        $stmt->close();
    }
}
?>
            $user_name = $row['username'] ?? $user_name;
            $user_role = $row['role'] ?? $user_role;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Disaster Portal</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        body {
            background: linear-gradient(rgba(11, 17, 32, 0.92), rgba(11, 17, 32, 0.97)), 
                        url('https://images.unsplash.com/photo-1519681393784-d120267933ba?q=80&w=1920&auto=format&fit=crop') no-repeat center center fixed;
            background-size: cover;
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Header Navbar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(15, 23, 42, 0.85);
            padding: 16px 40px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            position: sticky; top: 0; z-index: 1000;
        }

        .brand-logo {
            font-size: 20px;
            font-weight: 700;
            color: #38bdf8;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-links { display: flex; align-items: center; gap: 20px; }
        .nav-links a { 
            color: #cbd5e1; 
            text-decoration: none; 
            font-size: 14px; 
            font-weight: 500; 
            transition: all 0.2s; 
        }
        .nav-links a:hover, .nav-links a.active { color: #38bdf8; }

        .logout-btn {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171 !important;
            padding: 6px 14px;
            border-radius: 6px;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .logout-btn:hover { background: rgba(239, 68, 68, 0.3); }

        /* Dashboard Container */
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
            flex: 1;
            width: 100%;
        }

        /* Hero Welcome Card */
        .welcome-card {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.9), rgba(15, 23, 42, 0.9));
            border: 1px solid rgba(56, 189, 248, 0.25);
            border-left: 5px solid #38bdf8;
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .welcome-text h1 {
            font-size: 28px;
            color: #f8fafc;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .welcome-text p {
            color: #94a3b8;
            font-size: 14px;
        }

        .role-badge {
            display: inline-block;
            background: rgba(56, 189, 248, 0.15);
            color: #38bdf8;
            border: 1px solid rgba(56, 189, 248, 0.3);
            font-size: 12px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
            margin-left: 8px;
            text-transform: capitalize;
        }

        .weather-pill {
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 10px 18px;
            border-radius: 30px;
            font-size: 13.5px;
            color: #cbd5e1;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Grid Layout for Action Cards */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 25px;
            margin-bottom: 35px;
        }

        .card {
            background: rgba(30, 41, 59, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 28px;
            backdrop-filter: blur(10px);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 3px;
            background: linear-gradient(90deg, transparent, rgba(56, 189, 248, 0.5), transparent);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .card:hover {
            transform: translateY(-6px);
            border-color: rgba(56, 189, 248, 0.4);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
        }

        .card:hover::before { opacity: 1; }

        .card-header-icon {
            width: 50px;
            height: 50px;
            background: rgba(56, 189, 248, 0.12);
            border: 1px solid rgba(56, 189, 248, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 18px;
        }

        .card h3 {
            font-size: 20px;
            color: #f8fafc;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .card p {
            font-size: 13.5px;
            color: #cbd5e1;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .card-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #0284c7, #0369a1);
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(2, 132, 199, 0.3);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #0369a1, #075985);
            box-shadow: 0 6px 20px rgba(2, 132, 199, 0.5);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
        }
        .btn-danger:hover {
            background: linear-gradient(135deg, #b91c1c, #991b1b);
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.5);
        }

        .btn-secondary {
            background: rgba(51, 65, 85, 0.8);
            color: #f1f5f9;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .btn-secondary:hover {
            background: rgba(71, 85, 105, 0.9);
            color: #38bdf8;
            border-color: rgba(56, 189, 248, 0.4);
        }

        /* Quick Info Bar */
        .quick-info-strip {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 18px 24px;
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 20px;
            text-align: center;
        }

        .info-stat h4 { font-size: 20px; color: #38bdf8; font-weight: 700; }
        .info-stat p { font-size: 12px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px; }

        footer {
            text-align: center;
            padding: 20px;
            color: #64748b;
            font-size: 13px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            margin-top: auto;
        }
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <nav class="navbar">
        <a href="index.php" class="brand-logo">
            <span>🛡️</span> Disaster Portal
        </a>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="disasters.php">Disasters</a>
            <a href="weather.php">Live Weather</a>
            <a href="helplines.php">Helplines</a>
            <a href="feedback.php">Feedback</a>
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </nav>

    <div class="container">

        <!-- Welcome Banner -->
        <div class="welcome-card">
            <div class="welcome-text">
                <h1>Welcome back, <?php echo htmlspecialchars($user_name); ?>! 👋</h1>
                <p>You are logged in as <span class="role-badge"><?php echo htmlspecialchars($user_role); ?></span></p>
            </div>
            <div class="weather-pill">
                <span>⛅</span> 30°C • Partly Sunny
            </div>
        </div>

        <!-- Action Cards Grid -->
        <div class="dashboard-grid">

            <div class="card">
                <div>
                    <div class="card-header-icon" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8;">
                        🌀
                    </div>
                    <h3>Disaster Information</h3>
                    <p>Learn about earthquakes, floods, cyclones, severe rainfall safety protocols, and sector forecasts.</p>
                </div>
                <a href="disasters.php" class="card-btn btn-primary">
                    View Disasters ➔
                </a>
            </div>

            <div class="card">
                <div>
                    <div class="card-header-icon" style="background: rgba(239, 68, 68, 0.15); color: #f87171;">
                        🚑
                    </div>
                    <h3>Emergency Services</h3>
                    <p>Quick access to national emergency helplines, NDRF response teams, and immediate relief contacts.</p>
                </div>
                <a href="helplines.php" class="card-btn btn-danger">
                    Emergency Numbers 📞
                </a>
            </div>

            <div class="card">
                <div>
                    <div class="card-header-icon" style="background: rgba(168, 85, 247, 0.15); color: #c084fc;">
                        💬
                    </div>
                    <h3>Submit Feedback</h3>
                    <p>Have questions, suggestions, or hazard reports? Reach out directly to the portal support team.</p>
                </div>
                <a href="feedback.php" class="card-btn btn-secondary">
                    Contact Us ✉️
                </a>
            </div>

        </div>

        <!-- Status Bar -->
        <div class="quick-info-strip">
            <div class="info-stat">
                <h4>🟢 Active</h4>
                <p>System Status</p>
            </div>
            <div class="info-stat">
                <h4>112 / 108</h4>
                <p>National Emergency Lines</p>
            </div>
            <div class="info-stat">
                <h4>1 Active</h4>
                <p>Heatwave Advisory</p>
            </div>
            <div class="info-stat">
                <h4>24/7</h4>
                <p>Monitoring Active</p>
            </div>
        </div>

    </div>

    <footer>
        <p>© 2026 Disaster Awareness & Emergency Response Portal. All rights reserved.</p>
    </footer>

</body>
</html>