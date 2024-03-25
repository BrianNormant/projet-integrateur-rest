#!php 
<?php
echo "Hello world\n";
include '/var/www/equipe500/projet6/api/connectDB.php';
$sth = $dbh->query(<<<SQL
SELECT * FROM EQ06_Train;
SQL);

var_dump($sth->fetchAll());
?>
