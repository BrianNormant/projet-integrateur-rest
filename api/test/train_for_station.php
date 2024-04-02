<?php

include './api/connectDB.php';

$sth = $dbh->query(<<<SQL
SELECT T.id FROM EQ06_Train T
INNER JOIN EQ06_RailRoute RR ON T.route_id = RR.route_id
INNER JOIN EQ06_Rail R ON R.id = RR.rail_id
INNER JOIN EQ06_Station S ON S.id IN (R.conn1_station, R.conn2_station)
WHERE T.stop < RR.nb_stop AND T.lastStation NOT IN (R.conn1_station, R.conn2_station) AND S.id = 198
GROUP BY T.id;
SQL);

var_dump($sth->fetchAll(PDO::FETCH_ASSOC));

?>
