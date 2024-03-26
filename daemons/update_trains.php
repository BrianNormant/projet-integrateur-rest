<?php

include './api/connectDB.php';

// TODO convert this to  a SQL function


# --- Simply advance each train to next the % of completion
# TODO use rail length to determine of much advancement in X time
$sth = $dbh->query(<<<SQL
UPDATE EQ06_Train 
SET relative_position = relative_position + 1
WHERE relative_position < 100;
SQL);

printf("%d where moved\n", $sth->rowCount());

# TODO Handle train that have reach the final destination
$sth = $dbh->query(<<<SQL
UPDATE EQ06_Train T
INNER JOIN EQ06_Route R ON T.route_id = R.id
SET T.currentRail = NULL,
	T.lastStation = NULL,
	T.nextStation = NULL,
	T.relative_position = 100
WHERE R.destination_station = T.nextStation;
SQL);

printf("%s Have arrived at their final destination\n", $sth->rowCount());

$sth = $dbh->query(<<<SQL
UPDATE EQ06_Train T
JOIN (
    SELECT R.conn1_station AS c1, R.conn2_station AS c2
    FROM EQ06_RailRoute RR
    INNER JOIN EQ06_Rail R ON R.id = RR.rail_id 
    WHERE RR.route_id = 3 AND RR.nb_stop = 0 + 1
    LIMIT 1
) AS subquery ON 1=1
SET T.lastStation = T.nextStation,
    T.nextStation = EQ06_selectother(subquery.c1, subquery.c2, T.nextStation),
    T.currentRail = (
        SELECT RR.rail_id 
        FROM EQ06_RailRoute RR 
        WHERE RR.route_id = T.route_id AND RR.nb_stop = T.stop + 1
    ),
    T.relative_position = 0
WHERE T.relative_position = 100 AND currentRail IS NOT NULL;
SQL);

printf("%s trains have reach a station and a continuing to the next rail\n", $sth->rowCount());


?>
