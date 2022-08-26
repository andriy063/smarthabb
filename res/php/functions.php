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

function fwr($url, $str) {
	$v = [1, 'error'];
	$fp = stream_socket_client('ssl://'.$url.':443', $v['0'], $v['1'], 5, STREAM_CLIENT_ASYNC_CONNECT);
	if (!$fp) {
	  // error
	} else {
	    fwrite($fp, "GET ".$str." HTTP/1.0\r\nHost: ".$url."\r\nAccept: */*\r\n\r\n");
	    fclose($fp);
	}
}

function dev_log($m = NULL, $c = -1, $e = NULL) {
	renderJSON(['status' => 'error', 'data' => ['code' => $c, 'error' => $e, 'php_error_get_last' => error_get_last(), 'message' => $m]]);
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

/*
jsonfdb(['file' => $path.$chat_id, 'data' => ['field1' => '7765'], 'action' => 'update' ]);
jsonfdb(['file' => $path.$chat_id, 'data' => ['field1'], 'action' => 'get' ]);
jsonfdb(['file' => $path.$chat_id, 'data' => ['field2' => ['keke' => 333, 'uuu' => 'ds']], 'action' => 'append' ]);
jsonfdb(['file' => $path.$chat_id, 'data' => ['field1'], 'action' => 'delete' ]);
jsonfdb(['file' => $path.$chat_id, 'data' => ['field2' => 1], 'action' => 'delete_append' ]); // 1 - ключ масиву

*/
function jsonfdb($config) {
	$file = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].$config['file']), true);
	if (empty($file)) {
		$r = file_put_contents($_SERVER['DOCUMENT_ROOT'].$config['file'], json_encode(['created' => date('d.m.Y H:i:s'), 'data' => []], JSON_PRETTY_PRINT));
		$file = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].$config['file']), true);
	}
	$file['updated'] = time();
	$ret = [];
	foreach ($config['data'] as $key => $value) {
		if ($config['action'] == 'update') {
			$file['data'][$key] = $value;
		}
		if ($config['action'] == 'append') {
			$file['data'][$key][] = $value;
		}
		if ($config['action'] == 'get') {
			$ret[$value] = !empty($file['data'][$value]) ? $file['data'][$value] : NULL;
		}
		if ($config['action'] == 'delete') {
			unset($file['data'][$value]);
		}
		if ($config['action'] == 'delete_append') {
			unset($file['data'][$key][$value]);
		}
	}

	if (!empty($ret)) {
		return $ret;
	} else {
		file_put_contents($_SERVER['DOCUMENT_ROOT'].$config['file'], json_encode($file, JSON_PRETTY_PRINT));
		return true;
	}
}
