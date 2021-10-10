<?php

// DB library (https://github.com/ThingEngineer/PHP-MySQLi-Database-Class)
require_once('mysqlidb.php');

function renderJSON($data, $enable_cors = true) {
  ob_clean();
  if ($enable_cors) {
    header("Access-Control-Allow-Origin: *");
  }
  header('Content-Type: application/json; charset=utf-8');
  if (!is_array($data)) {
    echo $data;
  } else {
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
  }
  exit;
}

function fetch_without_response($url) {
  $context = stream_context_create(["http" => ["method"=>"GET", "timeout" => 1]]);
  try {
    file_get_contents($url, 0, $context);
  } catch (Exception $e) { }
}

// DB instance
function db($config) {
  return new MysqliDb([
    'host' => $config['db_host'],
    'username' => $config['db_user'],
    'password' => $config['db_pwd'],
    'db'=> $config['db_name'],
    'charset' => 'utf8mb4'
  ]);
}
