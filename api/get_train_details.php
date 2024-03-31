<?php

header("Content-Type: application/json; charset: utf-8");

$headers = apache_request_headers();
if (!isset($headers["Authorization"])) {
	http_response_code(417);
	exit;
}
$token = $headers["Authorization"];

include './api/connectDB.php';
include './api/check_token.php';
include './api/lib_reservation.php';

switch(check_token($dbh, $token, null)) {
case 1:
	http_response_code(403);
	exit;
case 2:
	http_response_code(408);
	exit;
}

$user = get_user_for_token($dbh, $token);

$perm = get_perm_for_user($dbh, $user);

if ($perm == 'admin') {
	$sth = $dbh->prepare(<<<SQL
	SELECT id, company_id, charge, puissance, relative_position AS pos, currentRail AS rail, lastStation AS prev_station, nextStation AS next_station, route_id 
	FROM EQ06_Train WHERE id = ?;
	SQL);
	$sth->execute([$train]);
	$trains = $sth->fetchAll();
	if (sizeof($trains) == 0) {
		http_response_code(404);
		exit;
	}
} else {
	$sth = $dbh->prepare(<<<SQL
	SELECT id, T.company_id, charge, puissance, relative_position AS pos, currentRail AS rail, lastStation AS prev_station, nextStation AS next_station, route_id 
	FROM EQ06_Train T
	INNER JOIN EQ06_Account A ON A.Company_id = T.company_id
	WHERE A.userName = ? AND T.id = ?;
	SQL);
	$sth->execute([$user, $train]);
	$trains = $sth->fetchAll();
	if (sizeof($trains) == 0) {
		http_response_code(404);
		exit;
	}
}




## Get route as an ordered array

$route = array();
$sth = $dbh->prepare(<<<SQL
SELECT origin_station, destination_station FROM EQ06_Route
WHERE id = ?;
SQL);
$sth->execute([$trains[0]["route_id"]]);

$data = $sth->fetchAll();

$origin = $data[0]["origin_station"];
$dest   = $data[0]["destination_station"];

$route [] = $origin;

$sth = $dbh->prepare(<<<SQL
SELECT R.conn1_station AS c1, R.conn2_station AS c2, RR.nb_stop FROM EQ06_RailRoute RR
INNER JOIN EQ06_Rail R ON R.id = RR.rail_id
WHERE RR.route_id = ?
ORDER BY RR.nb_stop;
SQL);
$sth->execute([$trains[0]['route_id']]);


/* [ { c1, c2, stop }, ... ]
 * 	Obtain \/
 * [ c1, c2, c3, c4, .. cN]
 *
 */
$data = $sth->fetchAll();

foreach ($data as $row) {
	$last = $route[] = array_pop($route);
	$route[] = ($row["c1"] == $last)? $row["c2"] : $row["c1"];
}

# now convert each station_id to its id + name
$sth_station = $dbh->prepare("SELECT id, nameStation FROM EQ06_Station WHERE id = ?;");
function stationid_to_details($sth, $id) {
	$sth->execute([$id]);
	$local = $sth->fetchAll()[0];
	return array(
		"id"   => $local["id"],
		"name" => $local["nameStation"],
	);
}
$route = array_map(fn($id) => stationid_to_details($sth_station, $id), $route);

## Get rail details;

$rail_id = $trains[0]["rail"];
$sth = $dbh->prepare("SELECT id, conn1_station AS con1, conn2_station AS con2 FROM EQ06_Rail WHERE id = ?");
$sth->execute([$rail_id]);
$rail_info = $sth->fetchAll()[0];
$rail = array(
	"id" => $rail_info["id"],
	"con1" => stationid_to_details($sth_station, $rail_info["con1"]),
	"con2" => stationid_to_details($sth_station, $rail_info["con2"]),
);


function format($row, $route, $rail, $sth_station) {
	return array(
		"id"            =>  $row["id"],
		"rail"       	=>  $rail,
		"pos"           =>  $row["pos"],
		"charge"        =>  $row["charge"],
		"puissance"     =>  $row["puissance"],
		"company_id"    =>  $row["company_id"],
		"prev_station"  =>  stationid_to_details($sth_station, $row["prev_station"]),
		"next_station"  =>  stationid_to_details($sth_station, $row["next_station"]),
		"route"         =>  $route,
		"speed"         =>  get_avg_speed($row["charge"], $row["puissance"]),
	);
}

http_response_code(200);
echo json_encode(format($trains[0], $route, $rail, $sth_station))

?>
