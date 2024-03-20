<?php
header('Content-Type: application/json; charset-utf-8');
http_response_code(400);

function format($row) {
	return array(
		"user_name"  =>  $row["userName"],
		"mail"       =>  $row["mail"],
		"company"    =>  $row["Company_id"],
	);
}
include './api/connectDB.php';

$sth = $dbh->query('SELECT userName, mail, Company_id FROM EQ06_Account');
$users = array_map("format", $sth->fetchAll());

echo json_encode($users);

?>
