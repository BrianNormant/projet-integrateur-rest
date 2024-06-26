<?php
header('Content-Type: application/json; charset=utf-8');

// TODO encode password with SHA256, BASE64, whatever
$headers = apache_request_headers();
if (!isset($headers["Authorization"])) {
	http_response_code(417);
	exit;
}
$password = $headers["Authorization"];

include './api/connectDB.php';

$sth = $dbh->prepare("SELECT username FROM EQ06_Account WHERE username = ?;");
$sth->execute([$user]);

$user_data = $sth->fetchAll();
if (sizeof($user_data) == 0) {
	http_response_code(404);
	exit;
}

$sth = $dbh->prepare("SELECT username, CompanyType as type FROM EQ06_Account WHERE username = ? AND password = ?;");
$sth->execute([$user, $password]);
$user_data = $sth->fetchAll();
if (sizeof($user_data) == 0) {
	http_response_code(401);
	exit;
}

http_response_code(200);
$token = bin2hex(random_bytes(50));
$user_id = $user_data[0]["username"];
$type = $user_data[0]["type"];

// insert or replace?
$sth = $dbh->prepare("INSERT INTO EQ06_Token (token, userUsed, dateExpiration) VALUES (?,?,DATE_ADD(CURDATE(), INTERVAL 1 DAY));");
$sth->execute([$token, $user_id]);

echo json_encode(array( "token" => $token, "type" => $type ));
?>

