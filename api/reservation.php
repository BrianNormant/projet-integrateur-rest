<?php
header("Content-type: application/json; charset=utf-8");

// This endpoint list 10 reservations possible for a given train


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

include './api/connectDB.php';
include './api/dijkstra.php';

## Check if stations exists

$sth = $dbh->prepare(<<<SQL
SELECT id FROM EQ06_Station WHERE id = ? OR id = ?;
SQL);
$sth->execute([$origin, $destination]);

$stations = $sth->fetchAll();
if (sizeof($stations) != 2) {
	http_response_code(404);
	exit;
}

# Find path
$network = fetch_network_as_graph($dbh);
$path = dijkstra($network, $origin, $destination);

# Convert path to list of rails
$rails = array();
for ($i = 0; $i < sizeof($path) - 1; $i++)
	$rails[] = array($path[$i], $path[$i+1]);
# Get Id of rails from DB
$sth_rail = $dbh->prepare(<<<SQL
SELECT id,conn1_station,conn2_station FROM EQ06_Rail WHERE conn1_station = ? AND conn2_station = ? OR conn1_station = ? AND conn2_station = ?;
SQL);
function rail_from_db($sth, $rail) {
	$sth->execute([$rail[0], $rail[1], $rail[1], $rail[0]]);
	$data = $sth->fetchAll()[0];
	return array(
		"id" => $data["id"],
		"con1" => $data["conn1_station"],
		"con2" => $data["conn2_station"],
	);
}
$rails = array_map(fn($rail) => rail_from_db($sth_rail, $rail), $rails);

$start = date_create_from_format("Y-m-d H:i:s", $dbh->query("SELECT NOW();")->fetch()['NOW()']);
$start = date_add($start, date_interval_create_from_date_string("1 day"));

$reservations = $dbh->query("SELECT * FROM EQ06_Reservation;")->fetchAll();

# Generate possible reservations excluding already reserved periods
$sth = $dbh->prepare(<<<SQL
SELECT * FROM EQ06_Reservation RR
INNER JOIN EQ06_Rail R ON RR.rail_id = R.id
WHERE R.id = ? AND RR.dateReserv = ? AND RR.timeSlot = ?;
SQL);
$shifts = array( 'morning', 'evening', 'night' );
$i = 0;
$possibilities = array();
while (sizeof($possibilities) < 10) {
	$possibility = array(
		"date"   => date_format($start, "Y-m-d"),
		"period" => $shifts[$i++],
	);
	if ($i >= sizeof($shifts)) {
		$i = 0;
		$start = date_add($start, date_interval_create_from_date_string("1 day"));
	}
	foreach($rails as $rail) {
		$sth->execute([$rail["id"], $possibility["date"], $possibility["period"]]);
		if ($sth->rowCount() != 0) {
			goto next;
		}
	}
	$possibilities[] = $possibility;
	next:
}

echo json_encode(array(
	"route" => $rails,
	"revervations" => $possibilities,
));

?>
