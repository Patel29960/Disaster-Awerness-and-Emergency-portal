<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($fullName) && !empty($email) && !empty($password)) {
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sss", $fullName, $email, $password);
            $stmt->execute();
            
            $_SESSION['user_name'] = $fullName;
            $_SESSION['user_email'] = $email;
            $_SESSION['role'] = 'user';

            header("Location: dashboard.php");
            exit;
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Disaster Emergency System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: #0b1120; color: #f8fafc; display: flex; flex-direction: column; min-height: 100vh; }
        
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 16px 32px; background: #0f172a; border-bottom: 1px solid #1e293b; }
        .brand { font-size: 20px; font-weight: bold; color: #ffffff; text-decoration: none; }
        .nav-links a { color: #94a3b8; text-decoration: none; font-size: 14px; margin-left: 16px; font-weight: 500; }
        .nav-links a:hover { color: #38bdf8; }

        .main-content { flex: 1; display: flex; justify-content: center; align-items: center; padding: 40px 20px; }
        .reg-box { background: #1e293b; border: 1px solid #334155; width: 100%; max-width: 420px; padding: 32px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
        .reg-box h2 { color: #ffffff; text-align: center; font-size: 24px; margin-bottom: 6px; }
        .reg-box p { color: #94a3b8; text-align: center; font-size: 13px; margin-bottom: 24px; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 13px; color: #cbd5e1; margin-bottom: 6px; font-weight: 600; }
        .form-group input { width: 100%; padding: 12px; background: #0f172a; border: 1px solid #334155; border-radius: 6px; color: #ffffff; font-size: 14px; outline: none; }
        .form-group input:focus { border-color: #22c55e; }
        .btn-submit { width: 100%; padding: 12px; background: #16a34a; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 15px; transition: 0.2s; }
        .btn-submit:hover { background: #15803d; }
        .error-badge { background: #7f1d1d; color: #fca5a5; padding: 10px; border-radius: 6px; font-size: 13px; margin-bottom: 18px; text-align: center; }
        .links { text-align: center; margin-top: 20px; font-size: 13px; color: #94a3b8; }
        .links a { color: #38bdf8; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="brand">🚨 Disaster Portal</a>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="disasters.php">Disasters</a>
            <a href="shelters.php">Shelter Homes</a>
            <a href="volunteers.php">Volunteers</a>
            <a href="login.php" style="color: #0284c7;">Sign In</a>
        </div>
    </nav>

    <div class="main-content">
        <div class="reg-box">
            <h2>Create Account</h2>
            <p>Join the Emergency Response Portal</p>

            <?php if ($error): ?>
                <div class="error-badge"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="register.php">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" placeholder="John Doe" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="user@example.com" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn-submit">Register Account</button>
            </form>

            <div class="links">
                Already registered? <a href="login.php">Sign In Here</a> | <a href="volunteers.php">Become a Volunteer</a>
            </div>
        </div>
    </div>

</body>
</html>
