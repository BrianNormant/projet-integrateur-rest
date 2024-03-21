<?php
include './api/dijkstra.php';

function stations_exist($dbh, $station_id) : bool {
	$sth = $dbh->prepare(<<<SQL
	SELECT id FROM EQ06_Station WHERE id = ?;
	SQL);
	$sth->execute([$station_id]);

	return $sth->rowCount() > 0;
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
	return array_map(fn($rail) => rail_from_db($sth_rail, $rail), $rails);
}

function is_reservation_free($dbh, $rails, $possibility) : bool {
	$sth = $dbh->prepare(<<<SQL
	SELECT * FROM EQ06_Reservation RR
	INNER JOIN EQ06_Rail R ON RR.rail_id = R.id
	WHERE R.id = ? AND RR.dateReserv = ? AND RR.timeSlot = ?;
	SQL);
	foreach($rails as $rail) {
		$sth->execute([$rail["id"], $possibility["date"], $possibility["period"]]);
		if ($sth->rowCount() != 0) {
			return false;
		}
	}
	return true;
}

function create_reservation($date, $period) {
	$fare = 2500;
	$tax_rate = 2;
	$day = date_create($date)->getTimestamp() - date_create(date("Y-m-d"))->getTimestamp();
	$day = round($day / (60*60*24));
	for ($i = 0; $i < $day; $i++) $tax_rate = pow($tax_rate, 0.5);
	$fare *= $tax_rate;
	$fare = round($fare);

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
	INSERT INTO EQ06_Reservation (company_id, fare, dateReserv, timeSlot, rail_id) VALUES
	( :c_id, :fare, STR_TO_DATE(:date, '%Y-%m-%d'), :timeSlot, :rail_id);
	SQL);

	array_map(fn($rail) => reserve_rail($sth, $reservation, $company, $rail["id"]), $rails);
}
?>
