<?php
header('Content-Type: application/json; charset-utf-8');
http_response_code(200);

function format($row) {
	return array(
		"user_name"  =>  $row["userName"],
		"mail"       =>  $row["mail"],
		"company"    =>  $row["Company_id"],
		"type"       =>  $row["CompanyType"],
	);
}
include './api/connectDB.php';

$sth = $dbh->query('SELECT userName, mail, Company_id, CompanyType FROM EQ06_Account');
$users = array_map("format", $sth->fetchAll());

echo json_encode($users);

?>
