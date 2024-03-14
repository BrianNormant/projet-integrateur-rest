<?php

header('Content-Type: application/json; charset-utf-8');
http_response_code(400);

function format($con1, $con2) {
	return array(
		"con1" => $con1,
		"con2" => $con2
	);
};

// ref: https://www.viarail.ca/sites/all/files/media/destinations/images/img-carte-canada-all-fr.svg
$placeholder = array(
	format("Toronto",     "Brockville"),
	format("Brockville",  "Ottawa"),
	format("Brockville",  "Montreal"),
	format("Montreal",    "SainteFoy"),
	format("SainteFoy",   "Quebec"),
	format("Toronto",     "Winnipeg"),
	format("Winnipeg",    "Edmonton"),
	format("Edmonton",    "Vancouver"),
);

echo json_encode($placeholder);

?>
