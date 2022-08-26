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
  if ($config['dev_mode']) {
		dev_log('DB error', 4, $e);
	}
	renderJSON(['status' => 'error', 'data' => ['code' => '4', 'message' => 'DB error']]);
}



//var_dump($devices);exit;
