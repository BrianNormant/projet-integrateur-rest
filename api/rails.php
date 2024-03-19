<?php

header('Content-Type: application/json; charset-utf-8');
http_response_code(400);

function formatRow($row) {
	return array(
		"con1" => $row[0],
		"con2" => $row[1]
	);
}
include './api/connectDB.php';

$sth = $dbh->query('SELECT conn1_station, conn2_station FROM EQ06_Rail');
$rails = array_map("formatRow", $sth->fetchAll());


echo json_encode($rails);

?>
