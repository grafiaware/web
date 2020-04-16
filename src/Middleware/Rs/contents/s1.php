<?php
use Middleware\Rs\AppContext;
$handler = AppContext::getDb();

//pouze pokus
$handler->exec("update pokusna set prijmeni ='Horváth' where jmeno = 'Emil'"); 
$statement = $handler->query("select * from pokusna");
$statement->execute();
echo $statement->rowCount();
//while ($zaznam = MySQL_Fetch_Array($vysledek))
$zaznamy = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($zaznamy as $zaznam) {
    echo $zaznam["jmeno"].$zaznam["prijmeni"]."<br>\n";
}
$area='xxxxxxx';
include './editor.php';
