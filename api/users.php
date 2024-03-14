<?php

header('Content-Type: application/json; charset-utf-8');
http_response_code(400);

function format($user_name, $mail, $company) {
	return [
		"user_name" => $user_name,
		"mail"      => $mail,
		"company"   => $company
	];
}

$placeholder = array(
		format("brian",    "briannormant@bullshit.com",  "J'Aime-les-trains"),
		format("Etienne",  "etienne@ferland.jp",         "J'Hais-les-trains"),
		format("Java",     "java@worse.language",        "Oracle")
);

echo json_encode($placeholder);

?>

