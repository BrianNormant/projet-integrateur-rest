<?php

include './api/connectDB.php';

$sth = $dbh->query(<<<SQL
SELECT * FROM EQ06_Train T
INNER JOIN EQ06_RailRoute RR ON T.route_id = RR.route_id
WHERE T.stop < RR.nb_stop
;


SQL);

var_dump($sth->fetchAll(PDO::FETCH_ASSOC));

?>
