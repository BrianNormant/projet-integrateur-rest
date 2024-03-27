<?php

header('Content-Type: application/json; charset: utf-8');

$headers = apache_request_headers();
if (!isset($headers["Authorization"])) {
	http_response_code(417);
	exit;
}
$token = $headers["Authorization"];

include './api/connectDB.php';
include './api/check_token.php';

# check if token is valid
switch (check_token($dbh, $token, null)) {
case 1 :
	http_response_code(403);
	exit;
case 2:
	http_response_code(408);
	exit;
}

function format($row) {
	return array(
		"id"      =>  $row["id"],
		"fare"    =>  $row["fare"],
		"date"    =>  $row["dateReserv"],
		"period"  =>  $row["timeSlot"],
		"rail"    =>  $row["rail_id"]
	);
}

# check CompanyType
$sth = $dbh->prepare(<<<SQL
SELECT A.Company_id AS id,A.CompanyType AS type FROM EQ06_Token T
INNER JOIN EQ06_Account A ON A.userName = T.userUsed
WHERE T.token = ?;
SQL);


$sth->execute([$token]);
$company_info = $sth->fetchAll();
if (sizeof($company_info) == 0) {
	// DataBase is in a inconciliable state...
	http_response_code(500);
	exit;
}
http_response_code(200);
// if company is admin, list all reservations
if (strcmp($company_info[0]["type"], "admin") == 0) {
	$sth = $dbh->query(<<<SQL
	SELECT id, fare, dateReserv, timeSlot, rail_id FROM EQ06_Reservation;
	SQL);

	$reservations = array_map("format", $sth->fetchAll());
} else {
	// list resvervations of company
	$company_id = $company_info[0]["id"];
	$sth = $dbh->prepare(<<<SQL
	SELECT id, fare, dateReserv, timeSlot, rail_id FROM EQ06_Reservation WHERE company_id = ?;
	SQL);

	$sth->execute([$company_id]);
	$reservations = array_map("format", $sth->fetchAll());
}

echo json_encode($reservations);

?>
