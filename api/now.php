<?php

$x = intval(date("H"));
switch (true) {
case 6  < $x && $x <= 14 : 
	$period = 'morning'; 
	break;
case 14 < $x && $x <= 22 : 
	$period = 'evening'; 
	break;
default: 
	$period = 'night';
}
$day = date("y-m-d");

printf("We are %s, period is %s", $period, $day);
