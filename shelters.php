<?php
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

@include_once __DIR__ . '/config/database.php';

// Fetch active shelters from database
$shelters = [];
if (!empty($conn) && $conn instanceof mysqli) {
    try {
        $result = $conn->query("SELECT * FROM shelters WHERE is_open = 1 ORDER BY id DESC");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $shelters[] = $row;
            }
        }
    } catch (Throwable $e) {}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shelter Homes - Disaster Portal</title>
    
    <!-- Leaflet CSS for Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #0b1120; color: #f8fafc; min-height: 100vh; display: flex; flex-direction: column; }

        /* Navigation Header */
        .header-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #0f172a;
            padding: 16px 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .brand-logo { font-size: 18px; font-weight: bold; color: #38bdf8; text-decoration: none; }
        .nav-links a { color: #cbd5e1; text-decoration: none; margin-left: 15px; font-size: 14px; font-weight: 500; }
        .nav-links a:hover, .nav-links a.active { color: #38bdf8; }

        .container { max-width: 1100px; margin: 30px auto; padding: 0 20px; flex: 1; width: 100%; }
        h1 { color: #38bdf8; font-size: 24px; margin-bottom: 6px; }
        p.subtitle { color: #94a3b8; font-size: 14px; margin-bottom: 20px; }

        /* Map Styling */
        #map { width: 100%; height: 380px; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.15); margin-bottom: 30px; }

        /* Shelter List Cards */
        .shelter-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px; }
        .shelter-card {
            background: #1e293b;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .shelter-card h3 { color: #4ade80; font-size: 17px; margin-bottom: 10px; }
        .shelter-card p { color: #cbd5e1; margin-bottom: 6px; font-size: 13.5px; line-height: 1.4; }
        .status-badge { display: inline-block; margin-top: 8px; background: rgba(74, 222, 128, 0.15); color: #4ade80; border: 1px solid rgba(74, 222, 128, 0.3); padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        
        .leaflet-popup-content-wrapper { color: #0f172a; }
        footer { text-align: center; padding: 20px; color: #64748b; font-size: 13px; margin-top: auto; }
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <div class="header-nav">
        <a href="index.php" class="brand-logo">🛡️ Disaster Portal</a>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="disasters.php">Disasters</a>
            <a href="map.php">Live Map 🗺️</a>
            <a href="shelters.php" class="active">Shelter Homes 🏠</a>
            <a href="volunteers.php">Join Volunteer 🤝</a>
            <a href="dashboard.php">Dashboard</a>
        </div>
    </div>

    <div class="container">
        <h1>🏠 Emergency Relief Shelter Homes</h1>
        <p class="subtitle">Locate active relief camps and temporary shelter centers offering medical care, food, and safe housing.</p>

        <!-- Interactive Shelter Map -->
        <div id="map"></div>

        <!-- Shelter Cards Grid -->
        <h2 style="color: #f8fafc; font-size: 18px; margin-bottom: 15px;">Active Shelter Centers</h2>
        <div class="shelter-grid">
            <?php if (!empty($shelters)): ?>
                <?php foreach ($shelters as $s): ?>
                    <div class="shelter-card">
                        <h3><?php echo htmlspecialchars($s['name']); ?></h3>
                        <p><strong>📍 City:</strong> <?php echo htmlspecialchars($s['city']); ?></p>
                        <p><strong>🏠 Address:</strong> <?php echo htmlspecialchars($s['address']); ?></p>
                        <p><strong>📞 Contact:</strong> <?php echo htmlspecialchars($s['contact_number']); ?></p>
                        <p><strong>👥 Capacity:</strong> <?php echo htmlspecialchars($s['capacity']); ?> People</p>
                        <span class="status-badge">● OPEN & OPERATIONAL</span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: #94a3b8;">No shelters loaded. Make sure you ran the SQL query in phpMyAdmin.</p>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>© 2026 Disaster Awareness & Emergency Response Portal</p>
    </footer>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Center map over region
        var map = L.map('map').setView([21.7051, 72.9959], 7);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Plot shelter markers from PHP
        var shelters = <?php echo json_encode($shelters); ?>;
        shelters.forEach(function(s) {
            if (s.lat && s.lng) {
                L.marker([s.lat, s.lng]).addTo(map)
                    .bindPopup("<b>🏠 " + s.name + "</b><br>" + s.address + "<br><b>Contact:</b> " + s.contact_number);
            }
        });
    </script>

</body>
</html>