<?php
header('Content-Type: text/plain; charset=utf-8');
http_response_code(200);
?>
Aide/Liste des endpoints de API REST

GET
api/help : affiche cette page

api/users : Listes des utilisateurs
	format: [ { user_name", "mail", "company", "type" }, ... ]

api/user/:user/solde : Solde de l utilisateur
	require token in header
	format: { "solde" }

api/stations : Listes des stations et leurs positions sur le reseaux
	format : [ { "id", "name", "pos_x", "pos_y" }, ... ]

api/stations/:station : Liste des trains qui passeront par cette station
	format : [ {"id", "ETA"}, ... ]

api/rails : Listes des rails et leur stations de connections
	format : [ { "id", "con1", "con2" }, ... ]
api/rail/:id : Details sur un rails en particulier
	format : {"id", "con1", "con2"}
	return code:
		- 404 Rail doesn't exist
api/trains : Listes des trains en fonctionnement sur le reseau
	Authorization : <token>
	a company token gives access to all trains owned by the company
	an admin/maintainer token gives access to all trains on the network
	format : [ { "id", "rail_id", "pos" }, ... ]
	return code:
	- 403 : Invalid token
	- 408 : Expired token

api/stations/:id/arrival : Liste des trains qui passeront par cette station ainsi que leur ETA de leur position actuel jusqu'a cette station
	Authorization : <token> ; Un token de company donne la liste des trains de cette company, Un token d'admin liste tout les trains
	format : [ {"id", "ETA"}, ...]
	return code:
		- 403 Invalid token
		- 408 Expired token
		- 404 Station doesn't exist
		- 200 Ok
	


api/train/:train/details : Tout les details sur un train circulant sur le reseau
	Authorization :  <token>
	format : [ {
		"id", "rail", "pos", "charge", "puissance", "company_id",
		"route" : [ {"id", "origin", }, {"id", "stop 1"}, {"id", "stop 2"}, ..., {"id","destination"} ],
		"prev_station", "next_station"
		} ]
	return code:
	- 403 Invalid token
	- 408 Expired token
	- 404 Train doesn't exist
	- 200 Ok

api/reservations/:origin/:destination : Liste des reversations possible pour un trajet

	[[{"rail", "time"}, ...], ... ]
	return code:
	- 404 : Au moins 1 des ids est invalides
	- 200 : OK

api/list_reservations : Listes des reservations qu'une company a
	Authorization :  <token>
	[ {id, fare, dateReserv, period, rail_id} ,...]


PUT
api/login/:user : authentification
	Toutes les request privilegier sur l API auront besoin d un token valide 
	password in clear in body
	format { "token" }
	return code:
	- 404 : User doesnt Exist
	- 401 : Wrong Password
	- 200 : Access granted

PUT
api/reservations/:origin/:destination
	Authorization : <token>
	Query String:
		?date   : formated as "%Y-%m-%d"
		?period : one of morning, evening, night
	date and period avalaible for a given origin destination can be obtained
	trougth a GET request on the same endpoint
	the token must be one of an account with admin or company privilege
	the company linked to the token will be deducted the fare of requested reserrvations
	return code : 
	- 417 : one or more field is missing
	- 404 : origin and/or destination doesn't exist
	- 403 : invalid token OR wrong type of user
	- 408 : token expired
	- 406 : solde is to low for the given reservation

PUT
api/train/:origin/:dest
	Authorization : <token>
	Put a train the network
	Body: json with train data
	{ "charge", "puissance" }
	- 417 : one or more field is missing
	- 404 : origin and/or destination doesn't exist
	- 403 : invalid token OR wrong type of user
	- 408 : token expired
	- 406 : Failed reservation check
	


POST
api/check_login/:user : vérification d un token valide
	Authorization : <token>
	return code:
	- 200 : valide
	- 404 : non valide
	- 408 : token expiré
api/user/:user/solde : Modifier Solde utilisateur
	?token : token a utiliser
	?modif : ajout a retirer/ajouter au solde
	return code:
	- 404 : User doesn't exist
	- 403 : Invalid token
	- 408 : Expired token
	- 406 : Invalid request (Solde would be negative)

