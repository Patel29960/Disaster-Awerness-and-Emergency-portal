<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Safely include auth check if it exists
if (file_exists('auth_check.php')) {
    include_once 'auth_check.php';
}

// Safely include database connection if it exists
if (file_exists(__DIR__ . '/config/database.php')) {
    include_once __DIR__ . '/config/database.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Helplines - Disaster Portal</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <?php 
    if (file_exists('navbar.php')) {
        include 'navbar.php'; 
    } 
    ?>

    <div class="container">
        
        <!-- HERO BANNER -->
        <header class="hero-banner">
            <h1>🚨 Emergency Helplines & Contacts</h1>
            <p>Immediate 24/7 national emergency hotlines and response centers.</p>
        </header>

        <!-- EMERGENCY HOTLINES GRID -->
        <section class="section-block">
            <h2 class="section-title">National Emergency Response</h2>
            <div class="card-grid">
                
                <div class="card border-red">
                    <span class="badge badge-red">DISASTER CONTROL</span>
                    <h3 style="color: #ffffff; margin-top: 5px;">Disaster Management</h3>
                    <p style="font-size: 30px; font-weight: 800; color: #ef4444; margin: 10px 0;">1070 / 1077</p>
                    <p style="color: #94a3b8; font-size: 13px;">State & District Emergency Operations</p>
                </div>

                <div class="card border-blue">
                    <span class="badge badge-blue">UNIFIED HELPLINE</span>
                    <h3 style="color: #ffffff; margin-top: 5px;">National Emergency</h3>
                    <p style="font-size: 30px; font-weight: 800; color: #38bdf8; margin: 10px 0;">112</p>
                    <p style="color: #94a3b8; font-size: 13px;">Police, Fire, Rescue & Medical</p>
                </div>

                <div class="card border-orange">
                    <span class="badge badge-orange">MEDICAL DISPATCH</span>
                    <h3 style="color: #ffffff; margin-top: 5px;">Ambulance Services</h3>
                    <p style="font-size: 30px; font-weight: 800; color: #f59e0b; margin: 10px 0;">108 / 102</p>
                    <p style="color: #94a3b8; font-size: 13px;">Medical Emergency & Transit</p>
                </div>

                <div class="card">
                    <span class="badge">FIRE RESCUE</span>
                    <h3 style="color: #ffffff; margin-top: 5px;">Fire Services</h3>
                    <p style="font-size: 26px; font-weight: 800; color: #ef4444; margin: 10px 0;">101</p>
                    <p style="color: #94a3b8; font-size: 13px;">Fire hazards & Extraction</p>
                </div>

                <div class="card">
                    <span class="badge">WOMEN SAFETY</span>
                    <h3 style="color: #ffffff; margin-top: 5px;">Women Helpline</h3>
                    <p style="font-size: 26px; font-weight: 800; color: #38bdf8; margin: 10px 0;">1091</p>
                    <p style="color: #94a3b8; font-size: 13px;">24/7 Dedicated Safety Support</p>
                </div>

                <div class="card">
                    <span class="badge">NDRF HEADQUARTERS</span>
                    <h3 style="color: #ffffff; margin-top: 5px;">NDRF Control Room</h3>
                    <p style="font-size: 22px; font-weight: 800; color: #f59e0b; margin: 10px 0;">011-24363260</p>
                    <p style="color: #94a3b8; font-size: 13px;">National Disaster Response Force</p>
                </div>

            </div>
        </section>

    </div>

    <footer>
        <p style="text-align: center; color: #94a3b8; font-size: 13px; margin-top: 40px; border-top: 1px solid #1e293b; padding-top: 20px;">
            &copy; <?php echo date('Y'); ?> Disaster Awareness & Emergency Portal
        </p>
    </footer>

</body>
</html>