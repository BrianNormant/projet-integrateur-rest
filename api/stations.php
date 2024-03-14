<?php

include './api/connectDB.php';

header('Content-Type: application/json; charset-utf-8');
http_response_code(400);

function format($name, $pos_x , $pos_y) {
	return array(
		"name" => $name,
		"pos_x" => $pos_x,
		"pos_y" => $pos_y
	);
};

/*
 * https://www.viarail.ca/sites/all/files/media/destinations/images/img-carte-canada-all-fr.svg
 *
 * Prise des coordonées centré en haut a gauche.
 * Dimension de la carte: 1232 x 545
 *
 * Prise de dimension avec inkspace. Tranformation avec ./convertdim.c
 * */
$placeholder = array(
	format("Toronto",     1798,  1549),  #Toronto     861  417
	format("Brockville",  1459,  1352),  #Brockville  931  364
	format("Ottawa",      1575,  1293),  #Ottawa      907  348
	format("Montreal",    1382,  1189),  #Montreal    947  320
	format("Sainte-Foy",  1314,  977),   #Sainte-Foy  961  263
	format("Quebec",      1241,  914),   #Quebec      976  246
	format("Winnipeg",    3466,  1237),  #Winnipeg    517  333
	format("Edmonton",    4460,  855),   #Edmonton    312  230
	format("Vancouver",   5289,  1078)   #Vancouver   141  290
);

echo json_encode($placeholder);

?>
