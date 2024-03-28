#!env bash

URL='https://equipe500.tch099.ovh/projet6/api'
USER='Etienne'
PASSWD='1234'
DATE=`date +%Y-%m-%d` # --date '5 day'`
PERIOD='evening'
# ORIGIN=205 # Belleville
# DESTINATION=196 # Windsor
ORIGIN=228 # Sudbury
DESTINATION=229 # White River

# login
TOKEN=`curl -X PUT $URL/login/$USER -H "Authorization: $PASSWD" | sed -E 's/\{"token":"(.*)"\}/\1/'`

HA="Authorization: $TOKEN"
# echo $TOKEN

# echo "Verify invalid user"

echo "Verify valid token"
curl -i -X POST $URL/check_login/$USER -H "$HA"

echo "Create reservation"

# curl -i -X PUT "$URL/reservations/$ORIGIN/$DESTINATION?date=$DATE&period=$PERIOD" -H "$HA"

echo "Put train"
# curl -i -X PUT $URL/train/$ORIGIN/$DESTINATION -H "$HA" -d '{"charge":200,"puissance":4500}'

echo "List arrival at station"
curl -i -X GET $URL/stations/203/arrivals -H "$HA"
