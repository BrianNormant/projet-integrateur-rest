<?php

# header('Content-Type: plain/text; charset=utf-8');

if (!isset($_POST["token"])) {
	http_response_code(417);
	echo "missing token";
	exit;
}

/*
include './api/connectDB.php';

$sth = $dbh->prepare(<<<END
SELECT T.token FROM Token06 T
INNER JOIN User06 U ON T.user_id = U.user_id
WHERE U.name = ? AND T.token = ?;
END);

$sth->execute([$user, $_POST["token"]]);

if ($sth->fetchAll() == 0) {
	http_response_code(404);
	exit;
}
http_response_code(200);
exit;
*/


$content = file_get_contents("./tokens");

$MAX_LOOP = 500;

$LOOP = 0;
while (sscanf($content, "%[^:]:%s\n%n", $user, $token, $n)) {
	if ($token == $_POST["token"]) {
		http_response_code(200);
		exit;
	}
	$content = substr($content, $n);
	$LOOP++;
	if ($LOOP >= $MAX_LOOP) {
		http_response_code(500);
		exit;
	}
}
http_response_code(404);
?>

