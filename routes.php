<?php

require_once __DIR__.'/router.php';

$routes = array(
	["get",   '/',                 'index.php'],
	["get",   '/index.php',        'index.php'],
	["get",   '/api/help',         '/api/help.php'],
	["get",   '/api/users',        '/api/users.php'],
	["get",   '/api/stations',     '/api/stations.php'],
	["put",   '/api/login/$user',  '/api/login.php'],
	["post",  '/api/check_login',  '/api/check_login.php'],
	["any",   '/404',              'views/404.php'],
);

foreach($routes as list($method, $url, $endpoint)) {
	$method($url, $endpoint);
	$method("/projet6" . $url, $endpoint);
}

?>
