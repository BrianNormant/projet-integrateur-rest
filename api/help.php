<?php

header('Content-Type: plain/text; charset=utf-8');
http_response_code(400);

echo <<<END
Aide/Liste des endpoints de API REST

GET
api/help : affiche cette page

api/users : Listes des utilisateurs
	format: [ { "user_name", "mail", "company" }, ... ]

api/:user/solde : Solde de l utilisateur
	require token in header
	format: { "solde" }

api/stations : Listes des stations et leurs positions sur le reseaux
	format : [ { "name", "pos_x", "pos_y" }, ... ]

api/rails : Listes des rails et leur stations de connections
	format : [ { "con1", "con2" }, ... ]

api/trains : Listes des trains en fonctionnement sur le reseau
	format : [ { "id", "rail-id", "position-on-rail" }, ... ]

api/:train/itineraire : Intineraire d un train
	format : { "origin", "arret 1", ... ,"destination" }


PUT
api/:user_name/login : authentification
	password in clear in header
	format { "token" }
	return code:
	- 404 : User doesnt Exist
	- 401 : Wrong Password
	- 200 : Access granted

POST
api/:user_name/check_login : vérification d un token valide
	?token
	return code:
	- 200 valid
	- 404 non valid

END;
?>
