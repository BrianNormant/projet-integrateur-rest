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
include './api/lib_reservation.php';

if (   !stations_exist($dbh, $origin) 
	|| !stations_exist($dbh, $destination) ) {
	http_response_code(404);
	exit;
}

$rails = get_rails($dbh, $origin, $destination);


# Generate possible reservations excluding already reserved periods
$start = date_create_from_format("Y-m-d H:i:s", $dbh->query("SELECT NOW();")->fetch()['NOW()']);
$start = date_add($start, date_interval_create_from_date_string("1 day"));
$possibilities = array();
foreach(gen_possibility($start) as $possibility) {
	if (is_reservation_free($dbh, $rails, $possibility))
		$possibilities[] = $possibility;
	if (sizeof($possibilities) >= 10) break;
}

echo json_encode(array(
	"route" => $rails,
	"revervations" => $possibilities,
));

?>
