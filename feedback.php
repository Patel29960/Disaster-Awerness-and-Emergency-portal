<?php
session_start();
$user_name = $_SESSION['user_name'] ?? $_SESSION['username'] ?? "Trisha Shah";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback & Incident Report - Disaster Portal</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: linear-gradient(rgba(15, 23, 42, 0.90), rgba(15, 23, 42, 0.95)), url('https://images.unsplash.com/photo-1592210454359-9043f067919b?q=80&w=1920&auto=format&fit=crop'); background-size: cover; color: #fff; min-height: 100vh; }
        .navbar { display: flex; justify-content: space-between; align-items: center; background: #0f172a; padding: 15px 35px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
        .navbar a { color: #cbd5e1; text-decoration: none; margin-left: 20px; font-weight: 500; }
        .navbar a:hover, .navbar a.active { color: #38bdf8; }
        .form-card { max-width: 600px; margin: 40px auto; background: rgba(30, 41, 59, 0.85); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 30px; }
        input, textarea, select { width: 100%; padding: 12px; margin: 10px 0 20px 0; border-radius: 6px; border: 1px solid rgba(255, 255, 255, 0.2); background: rgba(15, 23, 42, 0.6); color: #fff; }
        button { width: 100%; padding: 12px; background: #0284c7; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; }
        button:hover { background: #0369a1; }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="index.php" style="font-size:20px; font-weight:bold; color:#fff; margin:0;">Disaster Portal</a>
        <div>
            <a href="index.php">Home</a>
            <a href="disasters.php">Disasters</a>
            <a href="weather.php">Live Weather</a>
            <a href="helplines.php">Emergency Helplines</a>
            <a href="feedback.php" class="active">Feedback</a>
            <a href="dashboard.php" style="background:#dc2626; padding:6px 14px; border-radius:6px; color:#fff;">Dashboard</a>
        </div>
    </nav>
    <div class="form-card">
        <h2 style="margin-bottom: 15px; text-align:center;">Report Hazard or Provide Feedback</h2>
        <form onsubmit="event.preventDefault(); alert('Report submitted successfully.');">
            <label>Reporter Name</label>
            <input type="text" value="<?php echo htmlspecialchars($user_name); ?>" required>
            
            <label>Incident Type</label>
            <select>
                <option>Waterlogging / Flood Hazard</option>
                <option>Electrical / Power Outage</option>
                <option>Road Block / Landslide</option>
                <option>General Feedback</option>
            </select>

            <label>Details / Description</label>
            <textarea rows="4" placeholder="Describe the hazard location and severity..." required></textarea>

            <button type="submit">Submit Incident Report</button>
        </form>
    </div>
</body>
</html>