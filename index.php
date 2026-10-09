<?php
require_once 'auth_check.php';
require_once __DIR__ . '/config/database.php';

$user_display_name = $_SESSION['username'] ?? $_SESSION['full_name'] ?? 'Shahtrisha531';
$user_email_display = $_SESSION['email'] ?? $_SESSION['user_email'] ?? 'shahtrisha531@gmail.com';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disaster Awareness & Emergency Portal</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="container">

        <!-- USER WELCOME BANNER -->
        <div class="card" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <div>
                <h2 style="color: #ffffff; font-size: 22px; margin-bottom: 4px;">Welcome back, <?php echo htmlspecialchars($user_display_name); ?>!</h2>
                <p style="color: #94a3b8; font-size: 13.5px;"><?php echo htmlspecialchars($user_email_display); ?></p>
            </div>
            <div>
                <span style="background-color: #0284c7; color: #ffffff; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700;">CITIZEN ACCOUNT</span>
            </div>
        </div>

        <!-- HERO HEADER -->
        <header class="hero-banner">
            <h1>National Disaster Awareness Portal</h1>
            <p>Empowering communities through real-time alerts, rapid emergency response protocols, and public safety guidelines.</p>
        </header>

        <!-- 1. ALERTS HUB & LIVE ADVISORIES -->
        <section id="alerts" class="section-block">
            <h2 class="section-title">🚨 Alerts Hub & Live Advisories</h2>
            <div class="card-grid">
                <div class="card alert-card border-red">
                    <span class="badge badge-red">RED ALERT</span>
                    <h3 style="color:#fff;">Severe Coastal Flood Warning</h3>
                    <p style="color:#94a3b8; font-size: 13.5px;">Heavy storm surges predicted along eastern coastal belts. Mandatory evacuation for low-lying areas.</p>
                    <span class="card-meta">Updated 10 mins ago • National Early Warning System</span>
                </div>

                <div class="card alert-card border-orange">
                    <span class="badge badge-orange">ORANGE WATCH</span>
                    <h3 style="color:#fff;">Heatwave Advisory</h3>
                    <p style="color:#94a3b8; font-size: 13.5px;">Temperatures expected to exceed 42°C in northern regions. Limit outdoor activities between 11 AM - 4 PM.</p>
                    <span class="card-meta">Updated 1 hour ago • Met Department</span>
                </div>

                <div class="card alert-card border-blue">
                    <span class="badge badge-blue">INFORMATION</span>
                    <h3 style="color:#fff;">Seismic Activity Monitor</h3>
                    <p style="color:#94a3b8; font-size: 13.5px;">Minor tremors recorded at 3.4 Magnitude. No tsunami threat reported. Normal operations continue.</p>
                    <span class="card-meta">Updated 3 hours ago • Seismic Center</span>
                </div>
            </div>
        </section>

        <!-- 2. RESPONSE PROTOCOLS -->
        <section id="response" class="section-block">
            <h2 class="section-title">⚡ Response Protocols</h2>
            <div class="card-grid">
                <div id="immediate" class="card">
                    <h3 style="color:#fff;">📢 Immediate Action</h3>
                    <ul class="bullet-list">
                        <li>Evacuate to higher ground during flood warnings.</li>
                        <li>Drop, Cover, and Hold On during seismic shocks.</li>
                        <li>Keep emergency go-bags with essentials ready.</li>
                    </ul>
                </div>

                <div id="firstaid" class="card">
                    <h3 style="color:#fff;">🚑 First Aid & Rescue</h3>
                    <ul class="bullet-list">
                        <li>Apply immediate pressure to bleeding wounds.</li>
                        <li>Clear airway paths for unconscious victims.</li>
                        <li>Contact national emergency helplines instantly.</li>
                    </ul>
                </div>

                <div id="agency" class="card">
                    <h3 style="color:#fff;">🚀 Agency Mobilization</h3>
                    <ul class="bullet-list">
                        <li>Rapid deployment of NDRF and local responders.</li>
                        <li>Emergency shelter setup & ration distribution.</li>
                        <li>Satellite communications link activation.</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- 3. EMERGENCY GUIDELINES & PRECAUTIONS -->
        <section id="guidelines" class="section-block">
            <h2 class="section-title">🛡️ Emergency Guidelines & Precautions</h2>
            <div class="card-grid grid-4">
                <div class="card">
                    <h3 style="color:#fff;">Winter (Cold Wave)</h3>
                    <ul class="check-list">
                        <li class="check-yes">✓ Keep dry. Change wet clothing quickly to prevent heat loss.</li>
                        <li class="check-yes">✓ Drink warm water and eat hot food.</li>
                        <li class="check-no">✗ Don't step outside early morning or late night unless necessary.</li>
                    </ul>
                    <a href="disasters.php" class="link-more">View More +</a>
                </div>

                <div class="card">
                    <h3 style="color:#fff;">Earthquakes</h3>
                    <ul class="check-list">
                        <li class="check-yes">✓ Repair deep plaster cracks in ceilings and foundations.</li>
                        <li class="check-yes">✓ Anchor overhead lighting fixtures to the ceiling.</li>
                        <li class="check-no">✗ Do not move from where you are during shaking.</li>
                    </ul>
                    <a href="disasters.php" class="link-more">View More +</a>
                </div>

                <div class="card">
                    <h3 style="color:#fff;">Landslides</h3>
                    <ul class="check-list">
                        <li class="check-yes">✓ Prepare tours to hilly regions according to weather alerts.</li>
                        <li class="check-yes">✓ Move away from landslide paths or downstream valleys.</li>
                        <li class="check-no">✗ Avoid construction and staying in vulnerable areas.</li>
                    </ul>
                    <a href="disasters.php" class="link-more">View More +</a>
                </div>

                <div class="card">
                    <h3 style="color:#fff;">Tsunami Safety</h3>
                    <ul class="check-list">
                        <li class="check-yes">✓ Know the height of your street above sea level.</li>
                        <li class="check-yes">✓ Plan evacuation routes from your home or workplace.</li>
                        <li class="check-no">✗ DO NOT wait for a tsunami warning to be announced once felt.</li>
                    </ul>
                    <a href="disasters.php" class="link-more">View More +</a>
                </div>
            </div>
        </section>

        <!-- 4. OUR ACTIVITIES -->
        <section id="activities" class="section-block">
            <h2 class="section-title">💡 Our Activities</h2>
            <div class="card-grid grid-4 text-center">
                <div class="card">
                    <div class="activity-icon">🛡️</div>
                    <h3 style="color:#fff; font-size: 16px;">Disaster Risk Reduction</h3>
                    <p style="color:#94a3b8; font-size:12.5px;">Creating national guidelines, promoting safe infrastructure, and reducing vulnerability.</p>
                </div>
                <div class="card">
                    <div class="activity-icon">👥</div>
                    <h3 style="color:#fff; font-size: 16px;">Capacity Building</h3>
                    <p style="color:#94a3b8; font-size:12.5px;">Training first responders, organizing mock drills, and strengthening response teams.</p>
                </div>
                <div class="card">
                    <div class="activity-icon">📖</div>
                    <h3 style="color:#fff; font-size: 16px;">Awareness & Education</h3>
                    <p style="color:#94a3b8; font-size:12.5px;">Spreading knowledge through campaigns, workshops, and digital outreach.</p>
                </div>
                <div class="card">
                    <div class="activity-icon">⚙️</div>
                    <h3 style="color:#fff; font-size: 16px;">Research & Innovation</h3>
                    <p style="color:#94a3b8; font-size:12.5px;">Integrating technology and data for early warning systems and mitigation.</p>
                </div>
            </div>
        </section>

        <!-- 5. ABOUT US & DEVELOPMENT TEAM -->
        <section id="about" class="section-block">
            <h2 id="team" class="section-title">👥 About Us & Development Team</h2>
            <div class="team-grid">
                <div class="team-card">
                    <div class="avatar-circle">TS</div>
                    <h3>Trisha Shah</h3>
                    <div class="team-role">PROJECT LEAD & DEVELOPER</div>
                    <p>Spearheaded project architecture, system design, and implementation of live weather and alert modules.</p>
                </div>

                <div class="team-card">
                    <div class="avatar-circle">PP</div>
                    <h3>Prachi Patel</h3>
                    <div class="team-role">UI/UX DESIGNER & ANALYST</div>
                    <p>Designed user interface structures, responsive layout integration, and interactive guideline components.</p>
                </div>

                <div class="team-card">
                    <div class="avatar-circle">VP</div>
                    <h3>Vraj Patel</h3>
                    <div class="team-role">BACKEND & DATABASE SPECIALIST</div>
                    <p>Engineered data pipelines, database management, session authentication, and emergency helpline routing.</p>
                </div>
            </div>
        </section>

    </div>

    <footer>
        <p style="text-align: center; color: #94a3b8; font-size: 13px; margin-top: 40px; border-top: 1px solid #1e293b; padding-top: 20px;">
            &copy; <?php echo date('Y'); ?> Disaster Awareness & Emergency Portal. All Rights Reserved.
        </p>
    </footer>

</body>
</html>