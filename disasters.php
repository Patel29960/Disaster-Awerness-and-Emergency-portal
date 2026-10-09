<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/database.php';

// Fetch disasters from database or emergency engine
$disasters = [];
if (isset($conn) && $conn) {
    $result = $conn->query("SELECT * FROM disasters");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $disasters[] = $row;
        }
    }
}

// Default fallback data if database is empty or missing key columns
if (empty($disasters)) {
    $disasters = [
        [
            'title' => 'Flood Safety Protocol',
            'description' => 'Move immediately to higher ground. Avoid driving or walking through moving water. Keep your emergency kit ready with essential medications and clean drinking water.',
            'severity' => 'Danger / High Risk'
        ],
        [
            'title' => 'Earthquake Emergency Response',
            'description' => 'Drop, Cover, and Hold On. Stay away from heavy furniture, windows, and exterior walls. If outdoors, move to an open area clear of buildings and power lines.',
            'severity' => 'Warning'
        ],
        [
            'title' => 'Cyclone & High Wind Advisory',
            'description' => 'Secure outdoor objects that could become projectiles. Board up windows or close shutters. Stay inside away from glass until authorities give the clear signal.',
            'severity' => 'Warning'
        ],
        [
            'title' => 'Fire Safety & Evacuation',
            'description' => 'Evacuate immediately upon official alert. Crawl low under smoke to find exit routes. Never use elevators during a structural fire emergency.',
            'severity' => 'Info'
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disaster Protocols & Guidelines</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #0b1120; color: #f8fafc; padding: 20px; line-height: 1.6; }
        
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 15px 30px; background: #0f172a; border-radius: 8px; margin-bottom: 30px; }
        .brand { font-size: 20px; font-weight: bold; color: #ffffff; text-decoration: none; }
        .nav-links a { color: #94a3b8; text-decoration: none; margin-left: 18px; font-size: 14px; transition: 0.2s; }
        .nav-links a:hover, .nav-links a.active { color: #38bdf8; font-weight: 600; }
        .btn-dash { background: #ef4444; color: white !important; padding: 6px 14px; border-radius: 6px; font-weight: bold; }

        .header-section { text-align: center; margin-bottom: 40px; }
        .header-section h1 { font-size: 28px; margin-bottom: 8px; color: #ffffff; }
        .header-section p { color: #94a3b8; font-size: 14px; max-width: 600px; margin: 0 auto; }

        .disaster-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; max-width: 1100px; margin: 0 auto; }
        .disaster-card { background: #1e293b; border: 1px solid #334155; border-radius: 10px; padding: 20px; transition: transform 0.2s; }
        .disaster-card:hover { transform: translateY(-3px); border-color: #38bdf8; }
        .disaster-title { font-size: 18px; font-weight: bold; color: #38bdf8; margin-bottom: 10px; display: flex; align-items: center; gap: 8px; }
        .disaster-desc { font-size: 14px; color: #cbd5e1; }
        .badge { display: inline-block; font-size: 11px; font-weight: bold; padding: 3px 8px; border-radius: 4px; background: #334155; color: #38bdf8; margin-bottom: 10px; }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="brand">Disaster Portal</a>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="disasters.php" class="active">Disasters</a>
            <a href="shelters.php">Shelter Homes 🏠</a>
            <a href="volunteers.php">Volunteers 🤝</a>
            <a href="feedback.php">Feedback</a>
            <a href="dashboard.php" class="btn-dash">Dashboard</a>
        </div>
    </nav>

    <div class="header-section">
        <h1>Disaster Protocols & Guidelines</h1>
        <p>Immediate action guidelines, safety procedures, and preparedness protocols for various natural and technological emergencies.</p>
    </div>

    <div class="disaster-grid">
        <?php foreach ($disasters as $item): ?>
            <?php 
                // Safe extraction with default fallback values (Fixes Undefined Array Key Warnings)
                $title = $item['title'] ?? $item['name'] ?? 'Safety Guideline';
                $description = $item['description'] ?? $item['message'] ?? 'Follow emergency management directions during active hazards.';
                $severity = $item['severity'] ?? 'General Advisory';
            ?>
            <div class="disaster-card">
                <span class="badge"><?php echo htmlspecialchars($severity); ?></span>
                <div class="disaster-title">⚠️ <?php echo htmlspecialchars($title); ?></div>
                <div class="disaster-desc"><?php echo htmlspecialchars($description); ?></div>
            </div>
        <?php endforeach; ?>
    </div>

</body>
</html>