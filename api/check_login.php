<?php
# header('Content-Type: plain/text; charset=utf-8');

if (!isset($_POST["token"])) {
	http_response_code(417);
	exit;
}

include './api/connectDB.php';
include './api/check_token.php';

$code = check_token($dbh, $_POST["token"], $user);

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
