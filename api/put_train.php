<?php
header('Content-Type: plain/text; charset: utf-8');

if (!is_numeric($origin) || !is_numeric($destination)) {
	http_response_code(417);
	exit;
}
if ($origin <= 0 || $destination <= 0) {
	http_response_code(417);
	exit;
}
if ($origin == $destination) {
	http_response_code(417);
	exit;
}

$body = file_get_contents('php://input');
$body = json_decode($body, true);
if (!isset($body["charge"]) || !isset($body["puissance"])) {
	http_response_code(417);
}

$headers = apache_request_headers();
if (!isset($headers["Authorization"])) {
	http_response_code(417);
	exit;
}
$token = $headers["Authorization"];

include './api/connectDB.php';
include './api/lib_reservation.php';
include './api/check_token.php';
if (   !stations_exist($dbh, $origin) 
	|| !stations_exist($dbh, $destination) ) {
	http_response_code(404);
	exit;
}

switch (check_token($dbh, $token, null)) {
case 1:
	http_response_code(403);
	exit;
case 2:
	http_response_code(408);
	exit;
};

$x = intval(date("H"));
switch (true) {
case 6  < $x && $x <= 14 : 
	$period = 'morning'; 
	break;
case 14 < $x && $x <= 22 : 
	$period = 'evening'; 
	break;
default: 
	$period = 'night';
}
$possibility = create_reservation(date("Y-m-d"), $period);
$rails = get_rails($dbh, $origin, $destination);

if (!is_reservation_reserved($dbh, $rails, $possibility)) {
	http_response_code(406);
	exit;
}

// Reservation is reserved, We can put the train on the network

$company = get_company_for_user($dbh, get_user_for_token($dbh, $token));

http_response_code(200);
$sql = <<<SQL
INSERT INTO EQ06_Route (origin_station, destination_station) VALUES
	(:org, :dst);
SET @id_route = LAST_INSERT_ID();

INSERT INTO EQ06_RailRoute (rail_id, route_id, nb_stop) VALUES

SQL;
for ($i = 0; $i < count($rails); $i++) {
	$rail = $rails[$i];
	$sql .= sprintf("(%d, @id_route, %d)", $rail["id"], $i);
	if ($i < count($rails) - 1) {
		$sql .= ",\n";
	} else {
		$sql .= ";\n";
	}
}
$sql .= <<<SQL
INSERT INTO EQ06_Train (charge, puissance, company_id, route_id, relative_position, currentRail, lastStation, nextStation) VALUES
(:charge, :puiss, :c_id, @id_route, 0, :cur_rail, :ls, :ns);;
SQL;

$dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
$sth = $dbh->prepare($sql);
if (!$sth) {
	echo "\nPDO::errorInfo():\n";
    print_r($dbh->errorInfo());
}


$ns = ($rails[0]["con1"] == $origin)? $rails[0]["con2"] : $rails[0]["con1"];
if (!$sth->execute([
	"org" => $origin, "dst" => $destination,
	"charge" => $body["charge"], "puiss" => $body["puissance"],
	"c_id" => $company, "cur_rail" => $rails[0]["id"], 
	"ls" => $origin, "ns" => $ns
])) {
	echo "\nPDO::errorInfo():\n";
    print_r($dbh->errorInfo());
}

$sth->fetchAll();
