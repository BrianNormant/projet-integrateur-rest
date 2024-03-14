<?php

require_once __DIR__.'/router.php';

define("LOCAL", "/");
define("WEB", "/projet6/");

get('/', 'index.php');
get('/index.php', 'index.php');
get('/api/help', '/api/help.php');
get('/api/users', '/api/users.php');

put('/api/login/$user', '/api/login.php');

post('/api/check_login', '/api/check_login.php');


# any('/404','views/404.php');
