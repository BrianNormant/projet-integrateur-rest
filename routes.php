<?php

require_once __DIR__.'/router.php';


get('/', 'index.php');
get('/index.php', 'index.php');

# any('/404','views/404.php');
