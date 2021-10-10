<?php
ob_start();
// Configuration
require_once('env.php');
// Functions
require_once('functions.php');

// Timezone
date_default_timezone_set($config['timezone']);
// New DB instance
$db = db($config);
// Test DB connection
$devices = [];
try {
  $devices = $db->get('devices');
} catch (Exception $e) {
  if ($config['dev_mode']) { var_dump($e); }
  exit('DB error');
}



//var_dump($devices);exit;
