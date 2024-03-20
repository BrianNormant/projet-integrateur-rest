<?php
header('Content-Type: application/json; charset-utf-8');
http_response_code(400);

include './api/connectDB.php';

function format($row) {
	return array(
		"id"     =>  $row["id"],
		"name"   =>  $row["nameStation"],
		"pos_x"  =>  $row["posX"],
		"pos_y"  =>  $row["posY"]
	);
}

$sth = $dbh->query('SELECT id, nameStation, posX, posY FROM EQ06_Station');
$stations = array_map("format", $sth->fetchAll());
/*
 * https://www.viarail.ca/sites/all/files/media/destinations/images/img-carte-canada-all-fr.svg
 *
 * Prise des coordonées centré en haut a gauche.
 * Dimension de la carte: 1232 x 545
 * Dimension de la mise a l'echelle: 5972 * 2025
 * 
 * Prise de dimension avec inkspace. Tranformation avec ./convertdim.c
	format("Toronto",     1798,  1549),  #Toronto     861  417
	format("Brockville",  1459,  1352),  #Brockville  931  364
	format("Ottawa",      1575,  1293),  #Ottawa      907  348
	format("Montreal",    1382,  1189),  #Montreal    947  320
	format("Sainte-Foy",  1314,  977),   #Sainte-Foy  961  263
	format("Quebec",      1241,  914),   #Quebec      976  246
	format("Winnipeg",    3466,  1237),  #Winnipeg    517  333
	format("Edmonton",    4460,  855),   #Edmonton    312  230
	format("Vancouver",   5289,  1078)   #Vancouver   141  290
 */

echo json_encode($stations);
?>
