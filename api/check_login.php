<?php
# header('Content-Type: plain/text; charset=utf-8');

if (!isset($_POST["token"])) {
	http_response_code(417);
	echo "missing token";
	exit;
}

include './api/connectDB.php';

$sth = $dbh->prepare(<<<END
SELECT T.token AS tok, T.dateExpiration AS date FROM EQ06_Token T
INNER JOIN EQ06_Account A ON T.userUsed = A.userName
WHERE A.userName = ? AND T.token = ?;
END);

$sth->execute([$user, $_POST["token"]]);
$data = $sth->fetchAll();

if (sizeof($data) == 0) {
	http_response_code(404);
	exit;
}
if (strtotime($data[0]["date"]) < time()) {
	http_response_code(408);
	exit;
};

http_response_code(200);
?>

