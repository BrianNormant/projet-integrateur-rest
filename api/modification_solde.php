<?php
header('Content-Type: application/json; charset: utf-8');

if (!isset($_POST["modif"])) {
	http_response_code(417);
	exit;
}

if (!isset($_POST["token"])) {
	http_response_code(417);
	exit;
}

$modif = filter_input(INPUT_POST, 'modif', FILTER_VALIDATE_INT);
if ($modif === false) {
	http_response_code(417);
	exit;
}

// $token = file_get_contents("php://input");
$token = $_POST["token"];

include './api/connectDB.php';
include './api/check_token.php';

switch (check_token($dbh, $token, null)) {
case 1 :
	http_response_code(403);
	exit;
case 2:
	http_response_code(408);
	exit;
}

$sth = $dbh->prepare(<<<SQL
SELECT A.Company_id AS id FROM EQ06_Token T
INNER JOIN EQ06_Account A ON T.userUsed = A.userName
WHERE T.token = ?;
SQL);

$sth->execute([$token]);
$id = $sth->fetchAll()[0]["id"];

# Check if transaction is possible

$sth = $dbh->prepare("SELECT balance AS solde FROM EQ06_Company WHERE name = ?");
$sth->execute([$id]);
$balance = $sth->fetchAll()[0]["solde"];

if ($balance + $modif < 0) {
	http_response_code(406);
	exit;
}
$balance += $modif;

# Commit the transaction
$sth = $dbh->prepare("UPDATE EQ06_Company SET balance = ? WHERE name = ?;");
$sth->execute([$balance, $id]);

http_response_code(200);
?>
