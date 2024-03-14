<?php

header('Content-Type: plain/text; charset=utf-8');
http_response_code(400);

echo <<<END
Aide/Liste des endpoints de l'API REST

GET
api/help : affiche cette page

api/users : Listes des utilisateurs
	format: [ { "user_name", "mail", "company" }, ... ]

api/stations : Listes des stations et leurs positions sur le reseaux
	format : [ { "name", "pos_x", "pos_y" }, ... ]

api/rails : Listes des rails et leur stations de connections
	format : [ { "con1", "con2" } ]


PUT
api/login/:user_name : authentification
	password in clear in header
	format { "token" }

POST
api/check_login : vérification d'un token valide
	?token
	code 200 si valid 404 sinon


END;
?>
