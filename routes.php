<?php

require_once __DIR__.'/router.php';

define("LOCAL", "/");
define("WEB", "/projet6/");

get( $LOCAL   .  '/',                 'index.php');
get( $LOCAL   .  '/index.php',        'index.php');
get( $LOCAL   .  '/api/help',         '/api/help.php');
get( $LOCAL   .  '/api/users',        '/api/users.php');
get( $LOCAL   .  '/api/stations',     '/api/stations.php');
put( $LOCAL   .  '/api/login/$user',  '/api/login.php');
post($LOCAL  .  '/api/check_login',  '/api/check_login.php');


get( $WEB   .  '/',                 'index.php');
get( $WEB   .  '/index.php',        'index.php');
get( $WEB   .  '/api/help',         '/api/help.php');
get( $WEB   .  '/api/users',        '/api/users.php');
get( $WEB   .  '/api/stations',     '/api/stations.php');
put( $WEB   .  '/api/login/$user',  '/api/login.php');
post($WEB  .  '/api/check_login',  '/api/check_login.php');

# any('/404','views/404.php');
