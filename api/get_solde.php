<?php
header("Content-Type: application/json; charset:utf-8");

$token = file_get_contents("php://input");

include './api/connectDB.php';
include './api/check_token.php';

switch (check_token($dbh, $token, $user)) {
case 1:
	http_response_code(403);
	exit;
case 2:
	http_response_code(408);
	exit;
}

$sth = $dbh->prepare(<<<SQL
SELECT C.balance AS solde FROM EQ06_Token T
INNER JOIN EQ06_Account A ON T.userUsed = A.userName
INNER JOIN EQ06_Company C ON A.Company_id = C.name
WHERE T.token = ?;
SQL);
$sth->execute([$token]);
$solde = $sth->fetchAll()[0]["solde"];

http_response_code(200);
echo json_encode( array( "solde" => $solde ) );
