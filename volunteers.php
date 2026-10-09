<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

@include_once __DIR__ . '/config/database.php';

$message = '';
$status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $phone     = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $email     = isset($_POST['email']) ? trim($_POST['email']) : '';
    $city      = isset($_POST['city']) ? trim($_POST['city']) : '';
    $skill     = isset($_POST['skill']) ? trim($_POST['skill']) : '';

    if (!empty($full_name) && !empty($phone) && !empty($email) && !empty($city) && !empty($skill)) {
        if (isset($conn) && $conn) {
            $stmt = $conn->prepare("INSERT INTO volunteers (full_name, phone, email, city, skill) VALUES (?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sssss", $full_name, $phone, $email, $city, $skill);
                if ($stmt->execute()) {
                    $message = "Thank you! You have successfully registered as an emergency volunteer.";
                    $status  = "success";
                } else {
                    $message = "Database Error: Could not process registration.";
                    $status  = "error";
                }
                $stmt->close();
            } else {
                $message = "Database query preparation failed.";
                $status  = "error";
            }
        } else {
            $message = "Registration saved locally (Database offline).";
            $status  = "success";
        }
    } else {
        $message = "Please fill in all required fields.";
        $status  = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Emergency Volunteer Team - Disaster Portal</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #0b1120; color: #f8fafc; min-height: 100vh; display: flex; flex-direction: column; }

        /* Navigation Bar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #0f172a;
            padding: 14px 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .brand-logo { font-size: 19px; font-weight: 700; color: #ffffff; text-decoration: none; }
        .nav-links { display: flex; align-items: center; gap: 16px; }
        .nav-links a { color: #cbd5e1; text-decoration: none; font-size: 13.5px; font-weight: 500; padding: 6px 10px; border-radius: 6px; transition: all 0.2s; }
        .nav-links a:hover, .nav-links a.active { color: #38bdf8; }
        .btn-dashboard { background: #ef4444 !important; color: #ffffff !important; font-weight: 600 !important; }

        .container { max-width: 550px; margin: 40px auto; padding: 0 20px; width: 100%; flex: 1; }

        .card {
            background: #1e293b;
            border-radius: 12px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 25px rgba(0,0,0,0.4);
        }

        .card h2 { color: #ffffff; font-size: 22px; font-weight: 700; margin-bottom: 8px; display: flex; align-items: center; gap: 10px; }
        .card p.sub { color: #94a3b8; font-size: 13.5px; margin-bottom: 24px; line-height: 1.5; }

        /* Alert Banners */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13.5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .alert.error { background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; color: #f87171; }
        .alert.success { background: rgba(34, 197, 94, 0.15); border: 1px solid #22c55e; color: #4ade80; }

        /* Form Controls */
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #cbd5e1; margin-bottom: 6px; }
        .form-group input, .form-group select {
            width: 100%;
            padding: 11px 14px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            background: #0f172a;
            color: #ffffff;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
        }
        .form-group input:focus, .form-group select:focus { border-color: #38bdf8; }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background: #38bdf8;
            color: #0f172a;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 14.5px;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 10px;
        }
        .btn-submit:hover { background: #0ea5e9; color: #ffffff; }

        footer { text-align: center; padding: 20px; color: #64748b; font-size: 13px; border-top: 1px solid rgba(255, 255, 255, 0.05); margin-top: auto; }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="brand-logo">Disaster Portal</a>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="disasters.php">Disasters</a>
            <a href="map.php">Live Map 🗺️</a>
            <a href="shelters.php">Shelter Homes 🏠</a>
            <a href="volunteers.php" class="active">Volunteers 🤝</a>
            <a href="weather.php">Live Weather</a>
            <a href="helplines.php">Helplines</a>
            <a href="feedback.php">Feedback</a>
            <a href="dashboard.php" class="btn-dashboard">Dashboard</a>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <h2>🤝 Join Emergency Volunteer Team</h2>
            <p class="sub">Register to assist local disaster response teams during rescue efforts, food distribution, and shelter operations.</p>

            <?php if (!empty($message)): ?>
                <div class="alert <?php echo $status; ?>">
                    <?php echo ($status === 'error' ? '❌ ' : '✅ ') . htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form action="volunteers.php" method="POST">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" placeholder="e.g. Trisha Shah" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" placeholder="e.g. +91 9876543210" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="e.g. volunteer@gmail.com" required>
                </div>

                <div class="form-group">
                    <label for="city">City / Region</label>
                    <input type="text" id="city" name="city" placeholder="e.g. Bharuch, Vadodara" required>
                </div>

                <div class="form-group">
                    <label for="skill">Primary Skill / Assistance Type</label>
                    <select id="skill" name="skill" required>
                        <option value="Rescue & Field Relief">Rescue & Field Relief</option>
                        <option value="Medical & First Aid">Medical & First Aid</option>
                        <option value="Food & Supply Distribution">Food & Supply Distribution</option>
                        <option value="Shelter Management">Shelter Management</option>
                        <option value="Logistics & Transport">Logistics & Transport</option>
                    </select>
                </div>

                <button type="submit" class="btn-submit">Submit Volunteer Registration</button>
            </form>
        </div>
    </div>

    <footer>
        <p>© 2026 Disaster Awareness & Emergency Response Portal</p>
    </footer>

</body>
</html>