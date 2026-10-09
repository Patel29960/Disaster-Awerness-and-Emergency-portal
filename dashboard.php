<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/database.php';

// Auth Guard
if (!isset($_SESSION['user_email']) && !isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit;
}

$isAdmin = (($_SESSION['role'] ?? '') === 'admin');

// Fetch Live Alerts
$alerts = [];
if (isset($conn) && $conn) {
    $res = $conn->query("SELECT * FROM alerts ORDER BY id DESC");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $alerts[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Disaster Awareness Portal</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: #0b1120; color: #f8fafc; padding-bottom: 50px; }
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 16px 32px; background: #0f172a; border-bottom: 1px solid #1e293b; }
        .brand { font-size: 18px; font-weight: bold; color: #ffffff; text-decoration: none; }
        .nav-links a { color: #94a3b8; text-decoration: none; font-size: 14px; margin-left: 16px; font-weight: 500; }
        .nav-links a:hover { color: #38bdf8; }
        .btn-admin { background: #dc2626; color: white !important; padding: 6px 12px; border-radius: 6px; font-weight: bold; }

        .container { max-width: 1000px; margin: 30px auto; padding: 0 20px; }
        .welcome-card { background: #1e293b; border: 1px solid #334155; border-radius: 12px; padding: 24px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; }
        .user-role { display: inline-block; padding: 4px 10px; background: #0284c7; color: white; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .user-role.admin { background: #dc2626; }

        .alerts-section { background: #1e293b; border: 1px solid #334155; border-radius: 12px; padding: 24px; margin-bottom: 24px; }
        .alerts-section h2 { color: #ef4444; font-size: 18px; margin-bottom: 16px; }

        .alert-box { background: #0f172a; border-left: 4px solid #ef4444; padding: 16px; border-radius: 6px; margin-bottom: 12px; }
        .alert-box h3 { color: #ffffff; font-size: 16px; }
        .alert-box p { color: #cbd5e1; font-size: 14px; margin-top: 6px; }

        .actions-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; }
        .action-card { background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 18px; text-decoration: none; color: white; transition: 0.2s; }
        .action-card:hover { border-color: #38bdf8; transform: translateY(-2px); }
        .action-card h4 { color: #38bdf8; font-size: 16px; margin-bottom: 6px; }
        .action-card p { color: #94a3b8; font-size: 13px; }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="brand">🚨 Disaster Awareness Portal</a>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="disasters.php">Disasters</a>
            <a href="shelters.php">Shelter Homes</a>
            <a href="volunteers.php">Volunteers</a>
            <?php if ($isAdmin): ?>
                <a href="admin.php" class="btn-admin">Admin Console</a>
            <?php endif; ?>
            <a href="logout.php" style="color: #ef4444; font-weight: bold;">Logout</a>
        </div>
    </nav>

    <div class="container">
        <!-- User Banner -->
        <div class="welcome-card">
            <div>
                <h1 style="font-size: 22px; color: white;">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>!</h1>
                <p style="color: #94a3b8; font-size: 14px; margin-top: 4px;"><?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?></p>
            </div>
            <div>
                <span class="user-role <?php echo $isAdmin ? 'admin' : ''; ?>">
                    <?php echo $isAdmin ? 'ADMINISTRATOR' : 'CITIZEN ACCOUNT'; ?>
                </span>
            </div>
        </div>

        <!-- Live Emergency Alerts -->
        <div class="alerts-section">
            <h2>🚨 Live Emergency Broadcasts & Bulletins</h2>
            <?php if (empty($alerts)): ?>
                <p style="color: #94a3b8; font-size: 14px;">No active emergency broadcasts at this time. Stay safe!</p>
            <?php else: ?>
                <?php foreach ($alerts as $alt): ?>
                    <div class="alert-box">
                        <h3><?php echo htmlspecialchars($alt['title'] ?? 'Broadcast Alert'); ?></h3>
                        <p><?php echo htmlspecialchars($alt['message'] ?? ''); ?></p>
                        <small style="color: #38bdf8; margin-top: 8px; display: block; font-weight: 600;">
                            Severity: <?php echo htmlspecialchars($alt['severity'] ?? 'ALERT'); ?>
                        </small>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Quick Citizen Services -->
        <h3 style="font-size: 18px; margin-bottom: 14px;">Emergency Quick Links</h3>
        <div class="actions-grid">
            <a href="shelters.php" class="action-card">
                <h4>🏠 Find Shelter Homes</h4>
                <p>Locate operational emergency shelter centers near you.</p>
            </a>
            <a href="volunteers.php" class="action-card">
                <h4>🤝 Join Volunteers</h4>
                <p>Register as a field helper or relief support squad member.</p>
            </a>
            <a href="helpline.php" class="action-card">
                <h4>📞 Emergency Numbers</h4>
                <p>Access direct helpline contacts for rescue services.</p>
            </a>
        </div>
    </div>

</body>
</html>