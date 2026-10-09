<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

@include_once __DIR__ . '/config/database.php';

$disasters = [];

// Fetch all disasters from MySQL database
if (isset($conn) && $conn) {
    $result = @$conn->query("SELECT * FROM disasters");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $disasters[] = [
                'icon'  => '⚠️',
                'title' => $row['title'],
                'color' => '#38bdf8',
                'desc'  => $row['description']
            ];
        }
    }
}

// Fallback: Display all 8 categories if the database table is empty or offline
if (empty($disasters)) {
    $disasters = [
        ['icon' => '🌀', 'title' => 'Cyclones & Hurricanes', 'color' => '#38bdf8', 'desc' => 'Stay indoors, close doors/windows, store clean drinking water, and keep battery-operated lights ready.'],
        ['icon' => '🌊', 'title' => 'Floods & Heavy Rain', 'color' => '#38bdf8', 'desc' => 'Move to higher floors, avoid electric poles, and do not attempt to walk or drive through floodwaters.'],
        ['icon' => '🏚️', 'title' => 'Earthquakes', 'color' => '#f87171', 'desc' => 'Drop to hands and knees, take cover under a sturdy desk or table, and cover your head until shaking stops.'],
        ['icon' => '🌊', 'title' => 'Tsunamis & Coastal Surges', 'color' => '#0ea5e9', 'desc' => 'If near the coast and you feel shaking or see water receding rapidly, move inland to high ground immediately.'],
        ['icon' => '⛰️', 'title' => 'Landslides & Mudslides', 'color' => '#fb923c', 'desc' => 'Evacuate hazard paths, move to elevated solid bedrock areas, stay alert for rumbling sounds or mud flows.'],
        ['icon' => '🔥', 'title' => 'Forest Fires & Wildfires', 'color' => '#ef4444', 'desc' => 'Clear flammable brush away from homes, close air vents, and keep N95 masks ready for smoke protection.'],
        ['icon' => '☀️', 'title' => 'Extreme Heatwaves', 'color' => '#f59e0b', 'desc' => 'Drink plenty of water, avoid outdoor activities between 11 AM and 4 PM, wear lightweight cotton clothing.'],
        ['icon' => '☣️', 'title' => 'Industrial & Chemical Hazards', 'color' => '#a855f7', 'desc' => 'Stay indoors, seal windows/doors with wet towels, switch off ACs, and cover your mouth with a wet cloth.']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disaster Protocols & Guidelines - Disaster Portal</title>
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

        .container { max-width: 1200px; margin: 35px auto; padding: 0 20px; width: 100%; flex: 1; }

        .page-header { text-align: center; margin-bottom: 30px; }
        .page-header h1 { color: #ffffff; font-size: 28px; font-weight: 700; margin-bottom: 10px; }
        .page-header p { color: #94a3b8; font-size: 14.5px; max-width: 650px; margin: 0 auto; line-height: 1.5; }

        /* Grid Layout */
        .disaster-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 22px;
            margin-bottom: 40px;
        }

        .disaster-card {
            background: rgba(30, 41, 59, 0.7);
            border-radius: 12px;
            padding: 24px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(8px);
            transition: transform 0.2s, border-color 0.2s;
            display: flex;
            flex-direction: column;
        }

        .disaster-card:hover {
            transform: translateY(-4px);
            border-color: rgba(56, 189, 248, 0.4);
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
        }

        .card-icon { font-size: 22px; }

        .card-title {
            font-size: 16px;
            font-weight: 700;
            color: #38bdf8;
        }

        .card-desc {
            color: #cbd5e1;
            font-size: 13.5px;
            line-height: 1.6;
            flex: 1;
        }

        footer { text-align: center; padding: 20px; color: #64748b; font-size: 13px; border-top: 1px solid rgba(255, 255, 255, 0.05); margin-top: auto; }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="brand-logo">Disaster Portal</a>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="disasters.php" class="active">Disasters</a>
            <a href="map.php">Live Map 🗺️</a>
            <a href="shelters.php">Shelter Homes 🏠</a>
            <a href="volunteers.php">Volunteers 🤝</a>
            <a href="weather.php">Live Weather</a>
            <a href="helplines.php">Helplines</a>
            <a href="feedback.php">Feedback</a>
            <a href="dashboard.php" class="btn-dashboard">Dashboard</a>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>Disaster Protocols & Guidelines</h1>
            <p>Immediate action guidelines, safety procedures, and preparedness protocols for various natural and technological emergencies.</p>
        </div>

        <div class="disaster-grid">
            <?php foreach ($disasters as $item): ?>
                <div class="disaster-card">
                    <div class="card-header">
                        <span class="card-icon"><?php echo $item['icon']; ?></span>
                        <h2 class="card-title" style="color: <?php echo $item['color']; ?>;"><?php echo htmlspecialchars($item['title']); ?></h2>
                    </div>
                    <p class="card-desc"><?php echo htmlspecialchars($item['desc']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <footer>
        <p>© 2026 Disaster Awareness & Emergency Response Portal</p>
    </footer>

</body>
</html>