<?php
header('Content-Type: application/json; charset: utf-8');

$headers = apache_request_headers();
if (!isset($headers["Authorization"])) {
	http_response_code(417);
	exit;
}
$token = $headers["Authorization"];

include './api/connectDB.php';
include './api/check_token.php';
include './api/lib_reservation.php';

# check if token is valid
switch (check_token($dbh, $token, null)) {
case 1:
	http_response_code(403);
	exit;
case 2:
	http_response_code(408);
	exit;
}

$sth = $dbh->prepare(<<<SQL
SELECT A.Company_id AS id,A.CompanyType AS type FROM EQ06_Token T
INNER JOIN EQ06_Account A ON A.userName = T.userUsed
WHERE T.token = ?;
SQL);


$sth->execute([$token]);
$company_info = $sth->fetchAll();
if (sizeof($company_info) == 0) {
	// DataBase is in a inconciliable state...
	http_response_code(500);
	exit;
}
http_response_code(200);
if (strcmp($company_info[0]["type"], "admin") == 0) {
	// This is an admin token...
	$sth = $dbh->prepare(<<<SQL
	SELECT T.id AS id, T.stop AS stop FROM EQ06_Train T
	INNER JOIN EQ06_RailRoute RR ON T.route_id = RR.route_id
	INNER JOIN EQ06_Rail R ON R.id = RR.rail_id
	INNER JOIN EQ06_Station S ON S.id IN (R.conn1_station, R.conn2_station)
	WHERE T.stop < RR.nb_stop AND T.lastStation NOT IN (R.conn1_station, R.conn2_station) AND S.id = ?
	GROUP BY T.id, T.stop;
	SQL);
	
	
	$sth->execute(array($station));

} else {
	// This is a regular token ...
	$sth = $dbh->prepare(<<<SQL
	SELECT T.id AS id, T.stop AS stop FROM EQ06_Train T
	INNER JOIN EQ06_RailRoute RR ON T.route_id = RR.route_id
	INNER JOIN EQ06_Rail R ON R.id = RR.rail_id
	INNER JOIN EQ06_Station S ON S.id IN (R.conn1_station, R.conn2_station)
	WHERE T.stop < RR.nb_stop AND T.lastStation NOT IN (R.conn1_station, R.conn2_station) AND S.id = ? AND T.company_id = ?
	GROUP BY T.id, T.stop;
	SQL);

	$user = get_user_for_token($dbh, $token);
	$company = get_company_for_user($dbh, $user);
	$sth->execute(array($station, $company));
}


$trains = $sth->fetchAll(PDO::FETCH_ASSOC);

$result = array();
foreach ($trains as $train) {
	$data = $dbh->query(sprintf("SELECT nextStation, relative_position, currentRail, charge, puissance FROM EQ06_Train WHERE id = %d", $train["id"]))->fetchAll(PDO::FETCH_ASSOC)[0];
	$s1 = $data["nextStation"];


	$dist = ($s1 == $station)?0:get_distance_between_station($dbh, $s1, $station);
	
	
	$len = $dbh->query("SELECT longueur FROM EQ06_Rail WHERE id = ". $data["currentRail"] . ";")->fetchAll(PDO::FETCH_ASSOC)[0]["longueur"];

	$dist += $len * ($data["relative_position"]/100);
	$eta  = $dist / get_avg_speed($data["charge"], $data["puissance"]);

	$result[] = array(
		"id"  => $train["id"],
		"ETA" => $eta
	);
}

echo json_encode($result);
