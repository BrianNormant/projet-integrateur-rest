<?php

/**
 * Check if a given token is valid
 * Return 0 if valid
 * Return 1 if invalid
 * Return 2 if expired
 */
function check_token($dbh, $token, $user) : int {
	if (is_null($user)) {
		$sth = $dbh->prepare(<<<SQL
		SELECT T.token AS tok, T.dateExpiration AS date FROM EQ06_Token T
		INNER JOIN EQ06_Account A ON T.userUsed = A.userName
		WHERE T.token = ?;
		SQL);
		$sth->execute([$token]);
		$db_token = $sth->fetchAll();
	} else {
		$sth = $dbh->prepare(<<<SQL
		SELECT T.token AS tok, T.dateExpiration AS date FROM EQ06_Token T
		INNER JOIN EQ06_Account A ON T.userUsed = A.userName
		WHERE T.token = ? AND A.userName = ?;
		SQL);
		$sth->execute([$token, $user]);
		$db_token = $sth->fetchAll();
	}
	if (sizeof($db_token) == 0) return 1;
	if (strtotime($db_token[0]["date"]) < time()) return 2;
	return 0;
}

/**
 * Determine the user associated with the token
 */
function get_user_for_token($dbh, $token) : string | false {
	$sth = $dbh->prepare(<<<SQL
	SELECT A.userName as user FROM EQ06_Token T
	INNER JOIN EQ06_Account A ON A.userName = T.userUsed
	WHERE T.token = ?;
	SQL);
	$sth->execute([$token]);
	$users = $sth->fetchAll();
	if (sizeof($users) == 0) return false;
	return $users[0]["user"];
}

function get_perm_for_user($dbh, $user) : string | false {
	$sth = $dbh->prepare(<<<SQL
	SELECT CompanyType as `type` FROM EQ06_Account
	WHERE userName = ?;
	SQL);
	$sth->execute([$user]);
	$types = $sth->fetchAll();
	if (sizeof($types) == 0) return false;
	return $types[0]["type"];
}

?>
