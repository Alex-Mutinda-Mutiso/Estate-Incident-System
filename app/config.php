<?php
$config = [
  'db' => [
    // Default: Localhost (development)
    'host' => '127.0.0.1',
    'name' => 'estate_db',
    'user' => 'root',
    'pass' => '',
  ]
];

// InfinityFree (Production - free.nf account)
if (strpos($_SERVER['HTTP_HOST'], 'free.nf') !== false) {
  $config['db'] = [
    'host' => 'sql100.infinityfree.com',   // InfinityFree MySQL Host
    'name' => 'if0_41454862_estate_db',    // Your DB name
    'user' => 'if0_41454862',              // Your InfinityFree username
    'pass' => 'ALEXISmutinda1',            // Your InfinityFree DB password
  ];
}

return $config;