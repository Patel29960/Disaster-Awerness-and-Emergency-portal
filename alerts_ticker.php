<?php
require_once 'database.php'; // Uses your database connection file
$alert_query = "SELECT title, message, created_at FROM emergency_alerts WHERE is_active = 1 ORDER BY created_at DESC";
$alert_result = $conn->query($alert_query);
?>

<div class="alerts-ticker-bar">
  <div class="ticker-title">
    <span>Alerts List</span>
    <button type="button" class="ticker-toggle" onclick="toggleTicker()">⏸</button>
  </div>

  <div class="ticker-content-wrap">
    <div class="ticker-scroll" id="tickerScroll">
      <?php if ($alert_result && $alert_result->num_rows > 0): ?>
        <?php while($alert = $alert_result->fetch_assoc()): ?>
          <span class="ticker-item">
            🚨 <strong><?php echo htmlspecialchars($alert['title']); ?>:</strong> 
            <?php echo htmlspecialchars($alert['message']); ?> 
            (<?php echo date('d M, H:i', strtotime($alert['created_at'])); ?>)
          </span>
        <?php endwhile; ?>
      <?php else: ?>
        <span class="ticker-item">No active emergency advisories at this time. All systems normal.</span>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
function toggleTicker() {
  const el = document.getElementById('tickerScroll');
  el.style.animationPlayState = (el.style.animationPlayState === 'paused') ? 'running' : 'paused';
}
</script>