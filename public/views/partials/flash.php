<?php
$messages = get_flash();
foreach ($messages as $type => $msg): ?>
  <div class="flash <?= $type ?>"><?= htmlspecialchars($msg) ?></div>
<?php endforeach; ?>