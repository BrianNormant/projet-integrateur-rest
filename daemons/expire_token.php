<?php
include './api/connectDB.php';

$sth = $dbh->query(<<<SQL
DELETE FROM EQ06_Token WHERE dateExpiration < NOW();
SQL);

echo sprintf("Deleted %d expired token\n", $sth->rowCount());

$sth = $dbh->query(<<<SQL
DELETE FROM EQ06_Reservation WHERE dateReserv < NOW() + INTERVAL 1 DAY;
SQL);
echo sprintf("Deleted %d expired reservation\n", $sth->rowCount());

?>
