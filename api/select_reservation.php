<?php

header('Content-Type: plain/text; charset: utf-8');


## Common error handling
if (!is_numeric($origin) || !is_numeric($destination)) {
	http_response_code(417);
	exit;
}
if ($origin <= 0 || $destination <= 0) {
	http_response_code(417);
	exit;
}
if ($origin == $destination) {
	http_response_code(417);
	exit;
}

$contents = explode("\n", file_get_contents("php://input"));
$token = $contents[0];
$date = $contents[1];
$period = $contents[2];

if (!($period == 'morning' 
	||$period == 'evening'
	||$period == 'night')) {
	http_response_code(417);
	exit;
}

// TODO Check if date is properly formated

include './api/connectDB.php';
include './api/lib_reservation.php';
include './api/check_token.php';
if (   !stations_exist($dbh, $origin) 
	|| !stations_exist($dbh, $destination) ) {
	http_response_code(404);
	exit;
}

switch (check_token($dbh, $token, null)) {
case 1:
	http_response_code(403);
	exit;
case 2:
	http_response_code(408);
	exit;
};

$possibility = create_reservation($date, $period);

$rails = get_rails($dbh, $origin, $destination);

if (!is_reservation_free($dbh, $rails, $possibility)) {
	http_response_code(409);
	exit;
}

$user = get_user_for_token($dbh, $token);

http_response_code(200);
if (get_perm_for_user($dbh, $user) == 'admin') {
	commit_reservation($dbh, $rails, $possibility);
} else {
	# Get user solde
	$sth = $dbh->prepare(<<<SQL
	SELECT balance AS solde FROM EQ06_Company C
	INNER JOIN EQ06_Account A ON C.name = A.Company_id
	WHERE A.userName = ?;
	SQL);
	$sth->execute([$user]);
	$solde = $sth->fetchAll()[0]["solde"];

	if ($solde - $possibility["fare"] < 0) {
		http_response_code(406);
		exit;
	}
	commit_reservation($dbh, $rails ,$possibility, get_company_for_user($dbh, $user));
}

?>
