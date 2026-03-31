<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Estate Incident Reporting System</title>
  <link rel="stylesheet" href="css/styles.css">
  <style>
    body {
      user-select: none;
    }
  </style>
  <script>
    document.addEventListener('contextmenu', function(e) {
      e.preventDefault();
      alert("Copying is disabled on this site.");
    });

    document.addEventListener('keydown', function(e) {
      if ((e.ctrlKey && (e.key === 'c' || e.key === 'u' || e.key === 's')) ||
          (e.ctrlKey && e.shiftKey && e.key === 'i')) {
        e.preventDefault();
        alert("Keyboard shortcuts are disabled.");
      }
    });
  </script>
</head>
<body>
  <?php include __DIR__ . '/partials/nav.php'; ?>
  <div class="container">
    <?php include __DIR__ . '/partials/flash.php'; ?>
    <?= $content ?? '' ?>
  </div>
</body>
</html>