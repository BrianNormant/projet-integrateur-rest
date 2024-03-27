<?php

header('Content-Type: application/json; charset: utf-8');
# http_response_code(400)

function format($row) {
	return array(
		"id"       =>  $row["id"],
		"rail_id"  =>  $row["rail_id"],
		"pos"      =>  $row["pos"]
	);
}

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
if (strcmp($company_info[0]["type"], "admin") == 0) {
	$sth = $dbh->query("SELECT id, currentRail AS rail_id, relative_position AS pos FROM EQ06_Train;");
	$trains = array_map("format", $sth->fetchAll());
} else {
	$company_id = $company_info[0]["id"];
	$sth = $dbh->prepare(<<<SQL
SELECT id, currentRail AS rail_id, relative_position AS pos FROM EQ06_Train WHERE company_id = ?;
SQL);
	$sth->execute([$company_id]);
	$trains = array_map("format", $sth->fetchAll());
}
echo json_encode($trains);

?>
