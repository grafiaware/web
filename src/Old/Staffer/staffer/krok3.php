<?php
use Middleware\Staffer\AppContext;
$handler = AppContext::getDb();

$id_pozice = $pozice;
$ok = 1;

//$nenitam = false;
//do {
//    $id_odpoved = uniqid(substr(rand(), 0, 5), 0); //vygenerujeme id_odpovedi
//    $statement = $handler->query("SELECT * FROM staffer_odpovedi WHERE id_odpoved = '$id_odpoved'"); //zeptáme se databáze, zda tam není
//    $statement->execute();
//    $jetam2 = $statement->rowCount();
//    if($jetam2 == 0 || $jetam2 == false) {
//        $nenitam = true; //pokud skutečně takové ID neexistuje, tak tam není ($nenitam je true)
//    } else {
//        $nenitam = false; //musím komentovat i to, co je jasné všem? :)
//    }
//} while ($nenitam == false); //aneb "dělej {kód}, dokud tam to samé ID existuje (a tím pádem je $nenitam false)

### kontrola zadaných údajů - vzniknou řetězce $sloupce a $values
    foreach ($pole as $klic=>$hodnoty) {
        $funkce='$vysl = '.$hodnoty[3].' ($klic,$hodnoty,$krok);';
        eval ($funkce);
        $sloupce.=','.$klic;
        $values.=",'".$vysl[1]."'";
        IF ($vysl[2] == 0) {
            $ok=0;
        }
    }

if (!$ok) {
    echo '<span class="chyba_staffer">Došlo k narušení ukládaných dat! Data nebyla uložena. Vyplňte formulář znovu.</span>';
} else {
//    $ok = $handler->exec("INSERT INTO staffer_odpovedi ($sloupce) VALUES ($values)");
    ### nové - generuji promární klíč a insert v transakci
    try {
        $handler->beginTransaction();
        ## primární klíč
        do {
            $id_odpoved = uniqid();
            $stmt = $handler->prepare(
                    "SELECT id_odpoved FROM staffer_odpovedi
                    WHERE id_odpoved = :id_odpoved LOCK IN SHARE MODE");   //nelze použít LOCK TABLES - to commitne aktuální transakci!
            $stmt->bindParam(':id_odpoved', $id_odpoved);
            $stmt->execute();
        } while ($stmt->rowCount());

        ## před řetězce $sloupce a $values se přidají položky id_pozice,id_odpoved
        $sloupce = 'id_pozice,id_odpoved'.$sloupce;
        $values = "'".$id_pozice."','".$id_odpoved."'".$values;
        $statement = $handler->query("INSERT INTO staffer_odpovedi ($sloupce) VALUES ($values)");
        $statement->execute();
        $handler->commit();
        $ok = TRUE;
    } catch(Exception $e) {
        $dbhTransact->rollBack();
        $ok = FALSE;
    }

    IF ($ok) {
        // +  čekání 4 vteřiny (viz content) před obnovením - Lukáš
        echo '<H2>Děkujeme, Vaše data byla uložena.<br> Budete kontaktováni naším pracovníkem.</H2>';
        //
        include 'user_reload_cleaner.php';
        //
        include 'mailer.php';
        ?>
        <html>
        <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <meta http-equiv="refresh" content="4; URL=index.php?list=p08&language=lan1">
        <title></title>
        </head>
        <body>
        </body>
        </html>
        <?PHP
    } ELSE {
        include 'zpet.php';
        echo 'Data neuložena! Chyba databáze!';
    }
}
?>
