#!env bash

URL='https://equipe500.tch099.ovh/projet6/api'
USER='Etienne'
PASSWD='1234'
DATE=`date +%Y-%m-%d`
PERIOD='evening'
ORIGIN=205 # Belleville
DESTINATION=196 # Windsor

echo "login and get token"
# login
TOKEN=`curl -X PUT $URL/login/$USER -H "Authorization: $PASSWD" | sed -E 's/\{"token":"(.*)",.*/\1/'`
# curl -X PUT $URL/login/$USER -H "Authorization: $PASSWD"


HA="Authorization: $TOKEN"

echo
echo "Verify valid token"
curl -i -X POST $URL/check_login/$USER -H "$HA"

# periods=('morning' 'evening' 'night')
# for day in `seq 120`; do
# 	DATE=`date +%Y-%m-%d --date "$day day"`
# 	for period in $periods; do
# 		echo
# 		echo "Create reservation"
# 		curl -i -X PUT "$URL/reservations/$ORIGIN/$DESTINATION?date=$DATE&period=$period" -H "$HA"
# 	done
# done

for fff in `seq 10`; do
	DATE=2024-04-02
	PERIOD='evening'
	ORIGIN=`shuf -i 196-241 -n 1`
	DESTINATION=`shuf -i 196-241 -n 1`
	CHARGE=`shuf -i 200-5000 -n 1`
	echo
	echo "Create reservation"
	curl -i -X PUT "$URL/reservations/$ORIGIN/$DESTINATION?date=$DATE&period=$PERIOD" -H "$HA"
	
	echo
	echo $"\n Put train"
	curl -i -X PUT $URL/train/$ORIGIN/$DESTINATION -H "$HA" -d "{\"charge\":$CHARGE,\"puissance\":4500}"
	
	sleep 2
done
exit

#echo
#echo "Put train"
#curl -i -X PUT $URL/train/$ORIGIN/$DESTINATION -H "$HA" -d '{"charge":690,"puissance":4500}'
#

echo
echo "List trains"
curl -i -X GET "$URL/trains" -H "$HA"

echo
echo "details for train"
curl -X GET $URL/train/31/details -H "$HA" | jq

exit;
DATE=`date +%Y-%m-%d`
PERIOD='evening'
echo
echo "Create reservation"
curl -i -X PUT "$URL/reservations/$ORIGIN/$DESTINATION?date=$DATE&period=$PERIOD" -H "$HA"



exit;
echo
echo "Put train"
curl -i -X PUT $URL/train/$ORIGIN/$DESTINATION -H "$HA" -d '{"charge":690,"puissance":4500}'

exit;
echo
echo "List reservations"
curl -X GET $URL/list_reservations -H "$HA" | jq
exit;


echo
echo "List arrival at station"
curl -i -X GET $URL/stations/196/arrivals -H "$HA"


