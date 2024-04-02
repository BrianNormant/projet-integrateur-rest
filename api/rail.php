<?php

header('Content-Type: application/json; charset-utf-8');

function format($row) {
	return array(
		"id"   => $row["id"],
		"con1" => $row["conn1_station"],
		"con2" => $row["conn2_station"],
	);
}
include './api/connectDB.php';

$sth = $dbh->prepare(<<<SQL
SELECT id,conn1_station, conn2_station FROM EQ06_Rail
WHERE id = ?
SQL);

$sth->execute([$rail]);

if ($sth->rowCount() <= 0) {
	http_response_code(404);
	exit;
}

$rail = format($sth->fetchAll(PDO::FETCH_ASSOC)[0]);
echo json_encode($rail);
