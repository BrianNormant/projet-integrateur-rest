<?php

require_once __DIR__.'/router.php';

$routes = array(
	["get",  '/',                      'index.php'],
	["get",  '/index.php',             'index.php'],
	["get",  '/api/help',              '/api/help.php'],
	["get",  '/api/users',             '/api/users.php'],
	["get",  '/api/user/$user/solde',  '/api/get_solde.php'],
	["get",  '/api/stations',          '/api/stations.php'],
	["get",  '/api/rails',             '/api/rails.php'],
	["get",  '/api/trains',            '/api/trains.php'],
	["get",  '/api/train/$train/details', '/api/get_train_details.php'],
	["get",  '/api/reservations/$origin/$destination', '/api/reservation.php'],
	["put",  '/api/login/$user',       '/api/login.php'],
	["post",  '/api/check_login/$user',  '/api/check_login.php'],
	["post",  '/api/user/$user/solde',   '/api/modification_solde.php']
	# ["any",   '/404',              'views/404.php'],
);

foreach($routes as list($method, $url, $endpoint)) {
	$method($url, $endpoint);
	$method("/projet6" . $url, $endpoint);
}
?>
