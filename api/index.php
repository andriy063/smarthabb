<?php
require_once('../res/php/init.php');

$act = !empty($_GET['act']) ? $_GET['act'] : NULL;

// Authorization
if (empty($_REQUEST['auth']) || (!empty($_REQUEST['auth']) && (md5($_REQUEST['auth']) !== md5($config['api_token'])))) {
  $act = 'unauthorized';
}

switch ($act) {

  case 'ping':


    renderJSON(['status' => 'success', 'data' => ['message' => 'ok', 'timestamp' => time()]]);
  break;

  case 'time':
    renderJSON(['status' => 'success', 'data' => ['message' => date('d.m.Y H:i:s'), 'timestamp' => time()]]);
  break;

  case 'unauthorized':
    renderJSON(['status' => 'error', 'data' => ['code' => '1', 'message' => 'Unauthorized']]);
  break;
  default:
    renderJSON(['status' => 'error', 'data' => ['code' => '3', 'message' => 'Unknown method']]);
  break;
}
