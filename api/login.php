<?php

header('Content-Type: application/json; charset=utf-8');

// TODO encode password with SHA256, BASE64, whatever
$password = file_get_contents("php://input");

/*
include './api/connectDB.php';

$sth = $dbh->prepare("SELECT name FROM User06 WHERE name = ?;");
$sth->execute([$user]);

$user_data = $sth->fetchAll();
if (sizeof($user_data) == 0) {
	http_response_code(404);
	exit;
}

$sth = $dbh->prepare("SELECT user_id FROM User06 WHERE name = ? AND password = ?;");
$sth->execute([$user, $password]);
$user_data = $sth->fetchAll();
if (sizeof($user_data) == 0) {
	http_response_code(401);
	exit;
}

http_response_code(200);
$user_id = $user_data[0]->user_id;
// insert or replace?
$sth = $dbh->prepare("INSERT INTO Token06 (user_id) VALUES (?);");
$sth->execute([$user_id]);
$sth = $dbh->prepare("SELECT token FROM Token06 WHERE user_id = ?;");
$sth->execute([$user_id]);
$token = sth->fetch()->token;

echo json_encode(array( "token" => $token ));
*/


$placeholder = array(
		"brian"    =>  "1234",
		"Etienne"  =>  "4221",
		"Java"     =>  "badme"
);

if ( ! array_key_exists($user, $placeholder ) ) {
		http_response_code(404);
} else if ( $placeholder[$user] == $password ) {
		http_response_code(200);
		$bytes = bin2hex(random_bytes(20));

		file_put_contents( "./tokens", sprintf("%s:%s\n", $user, $bytes), FILE_APPEND );

		echo json_encode( array( "token" => $bytes ) );
} else {
		http_response_code(401);
}
?>

