<?php

require_once __DIR__.'/router.php';

$routes = array(
["get",   '/',                                       'index.php'],
["get",   '/index.php',                              'index.php'],
["get",   '/api/help',                               '/api/help.php'],
["get",   '/api/users',                              '/api/users.php'],
["get",   '/api/user/$user/solde',                   '/api/get_solde.php'],
["get",   '/api/stations',                           '/api/stations.php'],
["get",   '/api/stations/$station/arrivals',         '/api/station_arrivals.php'],
["get",   '/api/rails',                              '/api/rails.php'],
["get",   '/api/trains',                             '/api/trains.php'],
["get",   '/api/train/$train/details',               '/api/get_train_details.php'],
["get",   '/api/reservations/$origin/$destination',  '/api/reservation.php'],
["get",   '/api/list_reservations',                  '/api/list_reservations.php'],

["put",   '/api/login/$user',                        '/api/login.php'],
["put",   '/api/train/$origin/$destination',         '/api/put_train.php'],
["put",   '/api/reservations/$origin/$destination',  '/api/select_reservation.php'],

["post",  '/api/check_login/$user',                  '/api/check_login.php'],
["post",  '/api/user/$user/solde',                   '/api/modification_solde.php']
);

foreach($routes as list($method, $url, $endpoint)) {
	$method($url, $endpoint);
	$method("/projet6" . $url, $endpoint);
}

// http_response_code(404);
# any('/404', '/404.php'],
?>
