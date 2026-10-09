<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$display_user = $_SESSION['username'] ?? $_SESSION['full_name'] ?? $_SESSION['user_email'] ?? $_SESSION['email'] ?? 'User';
?>
<nav class="navbar">
  <div class="nav-container">
    <a href="index.php" class="brand">🚨 Disaster Awareness & Emergency Portal</a>

    <div class="nav-links">
      <a href="index.php" class="nav-link">Home</a>
      <a href="index.php#alerts" class="nav-link">Alerts Hub</a>
      <a href="index.php#response" class="nav-link">Response Protocols</a>
      <a href="disasters.php" class="nav-link">Disasters</a>
      <a href="map.php" class="nav-link">Live Map 🗺️</a>
      <a href="shelters.php" class="nav-link">Shelter Homes 🏠</a>
      <a href="volunteers.php" class="nav-link">Volunteers 🧡</a>
      <a href="weather.php" class="nav-link">Live Weather</a>
      <a href="helpline.php" class="nav-link">Helplines</a>
      <a href="feedback.php" class="nav-link">Feedback</a>

      <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <a href="admin.php" class="nav-link admin-tag">Admin Panel</a>
      <?php endif; ?>
    </div>

    <div class="nav-actions">
      <span style="color: #94a3b8; font-size: 13px; margin-right: 10px;">Hi, <strong><?php echo htmlspecialchars($display_user); ?></strong></span>
      <a href="logout.php" class="btn-red">Logout</a>
    </div>
  </div>
</nav>