<?php
# header('Content-Type: plain/text; charset=utf-8');


$headers = apache_request_headers();
if (!isset($headers["Authorization"])) {
	http_response_code(417);
	exit;
}
$token = $headers["Authorization"];



include './api/connectDB.php';
include './api/check_token.php';

$code = check_token($dbh, $token, $user);

switch ($code) {
case 0: 
	http_response_code(200);
	break;
case 1:
	http_response_code(404);
	break;
case 2:
	http_response_code(408);
	break;
}

?>
