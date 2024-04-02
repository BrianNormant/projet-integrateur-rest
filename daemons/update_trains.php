<?php

include './api/connectDB.php';
include './api/lib_reservation.php';

# --- Simply advance each train to next the % of completion

$TIME_DELAY = 5 / 60; // in hour

$sth = $dbh->query(<<<SQL
UPDATE EQ06_Train T
INNER JOIN EQ06_Route R ON T.route_id = R.id
SET T.currentRail = NULL,
	T.lastStation = NULL,
	T.nextStation = NULL,
    T.relative_position = 100,
    T.stop = NULL
WHERE R.destination_station = T.nextStation AND T.relative_position > 99;
SQL);

printf("%s Have arrived at their final destination\n", $sth->rowCount());

$sth = $dbh->query(<<<SQL
UPDATE EQ06_Train T
SET T.lastStation = T.nextStation,
    T.nextStation = EQ06_selectother(
                        (SELECT R.conn1_station 
                         FROM EQ06_RailRoute RR
                         INNER JOIN EQ06_Rail R ON R.id = RR.rail_id 
                         WHERE RR.route_id = T.route_id AND RR.nb_stop = T.stop + 1
                         LIMIT 1),
                        (SELECT R.conn2_station 
                         FROM EQ06_RailRoute RR
                         INNER JOIN EQ06_Rail R ON R.id = RR.rail_id 
                         WHERE RR.route_id = T.route_id AND RR.nb_stop = T.stop + 1
                         LIMIT 1),
                        T.nextStation),
    T.currentRail = (
                        SELECT RR.rail_id 
                        FROM EQ06_RailRoute RR 
                        WHERE RR.route_id = T.route_id AND RR.nb_stop = T.stop + 1
                     ),
    T.relative_position = 0,
    T.stop = T.stop + 1
WHERE T.relative_position = 100 AND T.currentRail IS NOT NULL;
SQL);

printf("%s trains have reach a station and a continuing to the next rail\n", $sth->rowCount());


$sth = $dbh->query(<<<SQL
SELECT T.id AS id, longueur, charge, puissance, relative_position AS pos FROM EQ06_Train T
INNER JOIN EQ06_Rail RR ON T.currentRail = RR.id
WHERE relative_position < 100;
SQL);

$trains = $sth->fetchAll(PDO::FETCH_ASSOC);
foreach($trains as $train) {
	$speed = get_avg_speed($train["charge"], $train["puissance"]);
	$dist = $TIME_DELAY * $speed; // distance pacouru depuis la derniere update
		
	$newpos = $dist * 100 / $train["longueur"];
	$newpos += $train["pos"];
	if ($newpos > 99.5) $newpos = 100;

	$id = $train["id"];
	printf("train %d at pos %f\n", $id, $newpos);

	$dbh->query("UPDATE EQ06_Train SET relative_position = $newpos WHERE id = $id;");
}
printf("%d where moved\n", $sth->rowCount());

?>
