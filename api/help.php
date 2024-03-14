<?php

header('Content-Type: plain/text; charset=utf-8');
http_response_code(400);

echo <<<END
Aide/Liste des endpoints de l'API REST

GET
api/help : affiche cette page

api/users : Listes des utilisateurs
	format: [ { "user_name", "mail", "company" }, ... ]



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
