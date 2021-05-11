<?php
use Web\Middleware\Web\AppContext;
$handler = AppContext::getDb();

$vysledek = MySQL_Query("SELECT list FROM stranky WHERE MATCH (nazev_lan1,obsah_lan1,nazev_lan2,obsah_lan2,nazev_lan3,obsah_lan3) AGAINST('spousta lelku + řepa + konference') ORDER BY `poradi` ASC");
$statement = $handler->query("SELECT list FROM stranky WHERE MATCH (nazev_lan1,obsah_lan1,nazev_lan2,obsah_lan2,nazev_lan3,obsah_lan3) AGAINST('spousta lelku + řepa + konference') ORDER BY `poradi` ASC");
$statement->execute();

$num_rows = $statement->rowCount();

echo 'Počet nalezených záznamů '.$p.'-';
//WHILE ($zaznam = MySQL_Fetch_Array($vysledek)) {
$zaznamy = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach($zaznamy as $zaznam) {
        $transformator = new Web\Middleware\Web\Transformator();
        $transformedContent = $transformator->transform($zaznam['list']);
        echo $transformedContent;    
}

