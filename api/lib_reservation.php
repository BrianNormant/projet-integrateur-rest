<?php
include './api/dijkstra.php';

function stations_exist($dbh, $station_id) : bool {
	$sth = $dbh->prepare(<<<SQL
	SELECT id FROM EQ06_Station WHERE id = ?;
	SQL);
	$sth->execute([$station_id]);

	return $sth->rowCount() > 0;
}

function rail_from_db($sth, $rail) {
	$sth->execute([$rail[0], $rail[1], $rail[1], $rail[0]]);
	$data = $sth->fetchAll()[0];
	return array(
		"id"   => $data["id"],
		"con1" => $data["conn1_station"],
		"con2" => $data["conn2_station"],
		"len"  => $data["longueur"],
	);
}


function get_rails($dbh, $origin, $destination) {
	# Find path
	$network = fetch_network_as_graph($dbh);
	$path = dijkstra($network, $origin, $destination);

	# Convert path to list of rails
	$rails = array();
	for ($i = 0; $i < sizeof($path) - 1; $i++)
		$rails[] = array($path[$i], $path[$i+1]);
	# Get Id of rails from DB
	$sth_rail = $dbh->prepare(<<<SQL
	SELECT id,conn1_station,conn2_station,longueur FROM EQ06_Rail WHERE conn1_station = ? AND conn2_station = ? OR conn1_station = ? AND conn2_station = ?;
	SQL);
	return array_map(fn($rail) => rail_from_db($sth_rail, $rail), $rails);
}

function is_reservation_free($dbh, $rails, $possibility, $company = '') : bool {
	$sth = $dbh->prepare(<<<SQL
	SELECT * FROM EQ06_Reservation RR
	INNER JOIN EQ06_Rail R ON RR.rail_id = R.id
	WHERE R.id = ? AND RR.dateReserv = ? AND RR.timeSlot = ? AND RR.company_id <> ?;
	SQL);
	foreach($rails as $rail) {
		$sth->execute([$rail["id"], $possibility["date"], $possibility["period"], $company]);
		if ($sth->rowCount() != 0) {
			return false;
		}
	}
	return true;
}

function is_reservation_reserved($dbh, $rails, $possibility) : bool {
	$sth = $dbh->prepare(<<<SQL
	SELECT id FROM EQ06_Reservation
	WHERE rail_id = ? AND dateReserv = ? AND timeSlot = ?;
	SQL);

	// return true if rail is reserved
	function check_for_rail($sth, $rail, $date, $slot) {
		$sth->execute([$rail, $date, $slot]);
		return $sth->rowCount() != 0;
	}

	$result = array_map(fn($rail) => !check_for_rail($sth, $rail["id"], $possibility["date"], $possibility["period"]), $rails);
	return !array_sum($result);
}

function create_reservation($date, $period) {
	$fare = 2500;
	$tax_rate = 2;
	$day = date_create($date)->getTimestamp() - date_create(date("Y-m-d"))->getTimestamp();
	if ($day < 60 * 60) {
		$fare = 3600;
	} else {
		$day = round($day / (60*60*24));
		for ($i = 0; $i < $day; $i++) $tax_rate = pow($tax_rate, 0.5);
		$fare *= $tax_rate;
		$fare = round($fare);
	}


	return array(
		"date" => $date,
		"period" => $period,
		"fare" => $fare,
	);
}

function gen_possibility($start) {
	$shifts = array( 'morning', 'evening', 'night' );
	$i = 0;

	while (true) {
		yield create_reservation($start->format("Y-m-d"), $shifts[$i++]);
		if ($i >= sizeof($shifts)) {
			$i = 0;
			$start = date_add($start, date_interval_create_from_date_string("1 day"));
		}
	}
}
/**
 * Commit a reservation on the database
 * reservations will be created and put on the database
 * if $user is null, it is assumed that user is jaimelestrains
 */
function commit_reservation($dbh, $rails, $reservation, $company = 'jaimelestrains') {
	function reserve_rail($sth, $reservation, $company, $rail_id) {
		return $sth->execute([
			':c_id' => $company,
			':fare' => $reservation["fare"],
			':date' => $reservation["date"],
			':timeSlot' => $reservation["period"],
			':rail_id'  => $rail_id,
		]);
	}
	$sth = $dbh->prepare(<<<SQL
	INSERT INTO EQ06_Reservation (company_id, fare, dateReserv, timeSlot, rail_id) 
	SELECT :c_id, :fare, STR_TO_DATE(:date, '%Y-%m-%d'), :timeSlot, :rail_id FROM DUAL
	WHERE NOT EXISTS (
		SELECT * FROM EQ06_Reservation WHERE company_id = :c_id AND dateReserv = STR_TO_DATE(:date, '%Y-%m-%d') AND rail_id = :rail_id AND timeSlot = :timeSlot
	);
	SQL);

	array_map(fn($rail) => reserve_rail($sth, $reservation, $company, $rail["id"]), $rails);
}


/**
 * Return an array of the station $train is going to visit next
 * $train = array ( "id", "stop")
 */
function get_following_stations($dbh, $train) {
	$sth = $dbh->prepare(<<<SQL
	SELECT R.conn1_station AS c1, R.conn2_station AS c2
	FROM EQ06_RailRoute RR
	INNER JOIN RAIL R ON R.id = RR.rail_id
	WHERE RR.route_id = ? AND RR.nb_stop > ?
	ORDER BY RR.nb_stop;
	SQL);
	$sth->execute(array($train["id"], $train["stop"]));
	$rails = $sth->fetchAll(PDO::FETCH_ASSOC);
	$rails = array_map(fn($row) => [$row["c1"], $row["c2"]], $rails);

	$stations = [in_array($rails[0][0], $rails[1])?$rails[0][1]:$rails[0][0]];

	for ($i = 1; $i < count($rails); $i++) {
		$stations[] = ($stations[$i-1] == $rails[$i-1][0])?$rails[$i-1][1]:$rails[$i-1][0];
	}
	return $stations;
}

function get_distance_between_station($dbh, $s1, $s2) : int {
	$rails = get_rails($dbh, $s1, $s2);
	$rails = array_map(fn($row) => $row["len"], $rails);
	return array_sum($rails);
}

// Litteralement de chatgpt:
// v ≈ √(2*745.7*p)/(m*0.5)
// with p in HP, m in kg and v in m/s
// return speed in kph
function get_avg_speed($charge, $power) {
	return 3.6 * sqrt((2 * 745.7 * $power) / ($charge * 0.5) );
}
?>
