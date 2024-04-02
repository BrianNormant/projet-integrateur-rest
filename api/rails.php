<?php

header('Content-Type: application/json; charset-utf-8');
http_response_code(200);

function formatRow($row) {
	return array(
		"id"   => $row[0],
		"con1" => $row[1],
		"con2" => $row[2]
	);
}
include './api/connectDB.php';

$sth = $dbh->query('SELECT id, conn1_station, conn2_station FROM EQ06_Rail');
$rails = array_map("formatRow", $sth->fetchAll());


echo json_encode($rails);

?>
