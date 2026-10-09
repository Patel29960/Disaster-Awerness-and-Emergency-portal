<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

@include_once __DIR__ . '/config/database.php';

// Fetch active disaster incidents and shelters for map plotting
$map_locations = [];

if (!empty($conn) && $conn instanceof mysqli) {
    try {
        // Fetch Shelters
        $res_shelters = $conn->query("SELECT name, city, address, contact_number, lat, lng, 'shelter' as type FROM shelters WHERE is_open = 1");
        if ($res_shelters) {
            while ($row = $res_shelters->fetch_assoc()) {
                $map_locations[] = $row;
            }
        }
        
        // Fetch Disasters
        $res_disasters = $conn->query("SELECT title as name, location as address, severity, lat, lng, 'disaster' as type FROM disasters WHERE lat IS NOT NULL AND lng IS NOT NULL");
        if ($res_disasters) {
            while ($row = $res_disasters->fetch_assoc()) {
                $map_locations[] = $row;
            }
        }
    } catch (Throwable $e) {}
}

// Fallback sample locations if database has no map markers yet
if (empty($map_locations)) {
    $map_locations = [
        [
            'name' => 'Central Emergency Relief Shelter',
            'city' => 'Bharuch',
            'address' => 'Station Road, Community Hall',
            'contact_number' => '+91 9876543210',
            'lat' => 21.7051,
            'lng' => 72.9959,
            'type' => 'shelter'
        ],
        [
            'name' => 'Flood Control Relief Camp',
            'city' => 'Vadodara',
            'address' => 'RC Dutt Road, Sports Complex',
            'contact_number' => '+91 9812345678',
            'lat' => 22.3072,
            'lng' => 73.1812,
            'type' => 'shelter'
        ],
        [
            'name' => 'Coastal Surge Hazard Area',
            'city' => 'Surat',
            'address' => 'Dumas Coastal Belt',
            'severity' => 'HIGH',
            'lat' => 21.1702,
            'lng' => 72.8311,
            'type' => 'disaster'
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Interactive Emergency Map - Disaster Portal</title>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #0b1120; color: #f8fafc; min-height: 100vh; display: flex; flex-direction: column; }

        /* Navigation Header */
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

        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; width: 100%; flex: 1; }

        .map-header { margin-bottom: 20px; }
        .map-header h1 { color: #38bdf8; font-size: 24px; margin-bottom: 6px; }
        .map-header p { color: #94a3b8; font-size: 14px; }

        /* CRITICAL: Explicit height so Leaflet map renders properly */
        #map {
            width: 100%;
            height: 520px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            margin-bottom: 25px;
            z-index: 1;
        }

        /* Map Legend */
        .legend-card {
            background: #1e293b;
            padding: 18px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            gap: 25px;
            align-items: center;
        }
        .legend-item { display: flex; align-items: center; gap: 8px; font-size: 13.5px; color: #cbd5e1; }
        .dot { width: 12px; height: 12px; border-radius: 50%; display: inline-block; }
        .dot.green { background: #4ade80; box-shadow: 0 0 8px #4ade80; }
        .dot.red { background: #f87171; box-shadow: 0 0 8px #f87171; }

        footer { text-align: center; padding: 20px; color: #64748b; font-size: 13px; margin-top: auto; }
    </style>
</head>
<body>

    <!-- Top Navigation Bar -->
    <nav class="navbar">
        <a href="index.php" class="brand-logo">Disaster Portal</a>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="disasters.php">Disasters</a>
            <a href="map.php" class="active">Live Map 🗺️</a>
            <a href="shelters.php">Shelter Homes 🏠</a>
            <a href="volunteers.php">Volunteers 🤝</a>
            <a href="weather.php">Live Weather</a>
            <a href="helplines.php">Helplines</a>
            <a href="feedback.php">Feedback</a>
            <a href="dashboard.php" class="btn-dashboard">Dashboard</a>
        </div>
    </nav>

    <div class="container">
        <div class="map-header">
            <h1>🗺️ Live GIS Emergency & Shelter Map</h1>
            <p>Real-time spatial mapping of active disaster advisory zones and emergency shelter centers.</p>
        </div>

        <!-- Interactive Map Container -->
        <div id="map"></div>

        <!-- Legend -->
        <div class="legend-card">
            <strong style="color: #f8fafc;">Map Legend:</strong>
            <div class="legend-item"><span class="dot green"></span> Safe Relief Shelters</div>
            <div class="legend-item"><span class="dot red"></span> Active Hazard / Disaster Zones</div>
        </div>
    </div>

    <footer>
        <p>© 2026 Disaster Awareness & Emergency Response Portal</p>
    </footer>

    <!-- Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 1. Initialize Map Centered on Gujarat/Central Region
            var map = L.map('map').setView([21.8000, 73.0000], 8);

            // 2. Load OpenStreetMap Tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // 3. Load Locations Data from PHP safely
            var locations = <?php echo json_encode($map_locations, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

            // 4. Plot Markers
            if (Array.isArray(locations) && locations.length > 0) {
                locations.forEach(function(loc) {
                    var lat = parseFloat(loc.lat);
                    var lng = parseFloat(loc.lng);

                    if (!isNaN(lat) && !isNaN(lng)) {
                        var popupContent = "";
                        
                        if (loc.type === 'shelter') {
                            popupContent = "<div style='color:#0f172a;'><b>🏠 Shelter Home:</b> " + (loc.name || '') + "<br>" +
                                           "<b>Address:</b> " + (loc.address || '') + "<br>" +
                                           "<b>Contact:</b> " + (loc.contact_number || 'N/A') + "</div>";
                        } else {
                            popupContent = "<div style='color:#0f172a;'><b>🚨 Hazard Zone:</b> " + (loc.name || '') + "<br>" +
                                           "<b>Location:</b> " + (loc.address || '') + "</div>";
                        }

                        L.marker([lat, lng]).addTo(map).bindPopup(popupContent);
                    }
                });
            }

            // Force map recalculation to handle full-width rendering properly
            setTimeout(function() {
                map.invalidateSize();
            }, 300);
        });
    </script>
</body>
</html>