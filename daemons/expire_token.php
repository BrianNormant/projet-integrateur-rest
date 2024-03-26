<?php

include './api/connectDB.php';

$sth = $dbh->query(<<<SQL
DELETE FROM EQ06_Token WHERE dateExpiration < NOW();
SQL);

echo sprintf("Deleted %d rows\n", $sth->rowCount());

?>
