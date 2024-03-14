<?php

header('Content-Type: application/json; charset=utf-8');

$password = file_get_contents("php://input");

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

