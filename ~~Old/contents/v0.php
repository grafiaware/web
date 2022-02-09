<?php

//echo "jsme v0.";
//if (isset($_GET['kodk'])) {$kod_kurzu = $_GET['kodk'];}

// UPG:
// generuje stránku s popisem katalogového kurzu
// GET parametry: list (sem se dostanu pokud list=v0), ci, rlist
// ci je id_kurz v tabulce kurz
// rlist  - zdá se, že to je hodnota položky menu list odpovídající stránce, na které se zobrazuje seznam kurzů (rozcestník)
// tedy asi referrer list - ve v0 ale asi nepoužito - asi chyba není rozbaleno menu na položce rlist

if (isset($_GET['ci'])) {$id_kurz = $_GET['ci'];}

//if (isset($_GET['rlist'])) {$ret_list = $_GET['rlist'];}
//echo $kod_kurzu;

$handler = $mwContainer->get(Pes\Database\Handler\Handler::class);

$statement = $handler->query("select * , c_kategorie.kod as kodkat
                         from kurz
                         inner join popis_kurzu on (kurz.id_popis_kurzu_FK = popis_kurzu.id_popis_kurzu)
                         left join c_kategorie on c_kategorie.id_c_kategorie=kurz.id_c_kategorie_FK
                        where
                        id_kurz=" . $id_kurz  );

$num_rows = $statement->rowCount();
$zaznam = $statement->fetch(PDO::FETCH_ASSOC);

IF ($num_rows == 0)  {
    echo '<span class="chyba">Litujeme, ale požadovaná informace o kurzu není dostupná.</span>';
} else {
    $sql_query = 'SELECT
                    vzb_kurz_cast_kurzu.id_kurz_FK, vzb_kurz_cast_kurzu.id_cast_kurzu_FK , cast_kurzu.cast_nazev, cast_kurzu.deleted
                    FROM vzb_kurz_cast_kurzu
                    left join cast_kurzu on  vzb_kurz_cast_kurzu.id_cast_kurzu_FK = cast_kurzu.id_cast_kurzu
                    WHERE (vzb_kurz_cast_kurzu.id_kurz_FK = '.$zaznam['id_kurz']  . ')' .
                    ' and (cast_kurzu.deleted=0) ' .
                    ' order by razeni'. ';';
    $statement = $handler->query($sql_query);
    $statement->execute();

    $casti_kurzu = array();
    $casti_kurzu_nazvy = array();

    $zaznamy = $statement->fetchAll(PDO::FETCH_ASSOC);
    foreach($zaznamy as $casti_kurzu_polozka) {
        array_push($casti_kurzu,$casti_kurzu_polozka['id_cast_kurzu_FK']); //zjisti id vsech casti
        array_push($casti_kurzu_nazvy,$casti_kurzu_polozka['cast_nazev']);
    }

    $casti_kurzu_obsahy = '';
    foreach  ($casti_kurzu as $klic=>$hodn) {    //pro kazdou cast(nevymazanou)  hledam jeji moduly (nevymazane)
          $sql_query_1 = 'select * from  vzb_cast_modul_kurzu
                          left join cast_kurzu on  vzb_cast_modul_kurzu.id_cast_kurzu_FK = cast_kurzu.id_cast_kurzu
                          left join  modul_kurzu on  vzb_cast_modul_kurzu.id_modul_kurzu_fk =  modul_kurzu.id_modul_kurzu
                          where
                          (vzb_cast_modul_kurzu.id_cast_kurzu_FK = ' . $hodn . ')' .
                          ' and (cast_kurzu.deleted=0) and (modul_kurzu.deleted=0) ' .
                          ' order by vzb_cast_modul_kurzu.razeni';

          $statement1 = $handler->query($sql_query_1);
          $statement1->execute();

          $casti_kurzu_obsahy .= "<p>";
          $casti_kurzu_obsahy .= "<ul>";
          $casti_kurzu_obsahy .= '<li><b>' . $casti_kurzu_nazvy[$klic] . '</b></li>';


          $cast_kurzu_obsah='<ul>';
            $zaznamy1 = $statement1->fetchAll(PDO::FETCH_ASSOC);
            foreach($zaznamy1 as $modul_kurzu_polozka) {
                   $cast_kurzu_obsah .=
                     "<li><DIV id=div_modul_nazev_jeden" .  "> " .  $modul_kurzu_polozka['modul_nazev'] . "</DIV>" .

                      "</li>" ;
          }

          $cast_kurzu_obsah .= '</ul>';
          $casti_kurzu_obsahy .=$cast_kurzu_obsah;
          $casti_kurzu_obsahy .= "</ul></p>";

    }


    //echo "*************************";
    //echo  "moduly obsahy" . $moduly_kurzu_obsahy . "<br>";   // *SEL*
    //echo "*************************<br>";

//---------------  prectene informace

    echo "<br>Název kurzu: <h3>". $zaznam['kurz_Nazev'] . "</h3>";
    //echo "Kód kurzu:  "   ;
    echo '<p>'. $zaznam['kodkat'] .  " " . $zaznam['kurz_poradove_cislo']. '</p>';
    echo "<br>Anotace kurzu: ". $zaznam['kurz_Anotace'];
    echo "<br>Cíle kurzu: ". $zaznam['kurz_Cile'];

    if ($zaznam['zobrazeni_obsahu'] ==1) {                          //1 z obsahu kurzu
                echo "<br>Obsah kurzu: ". $zaznam['kurz_Obsah'];}
    if ($zaznam['zobrazeni_obsahu'] ==2) {                          //2 z obsahu modulu
                echo "<br>Obsah částí a modulů kurzu: ". $casti_kurzu_obsahy;}
    if ($zaznam['zobrazeni_obsahu'] ==3) {                          //3 z obsahu kurzu i z obsahu modulu
                echo "<br>Obsah kurzu: ". $zaznam['kurz_Obsah'];
                echo "<br>Obsah částí a modulů kurzu: ". $casti_kurzu_obsahy;
                }

    echo "<br>Vstupní znalosti: ". $zaznam['kurz_Vstupni_znalosti'];
    echo "<br>Doporučení: ". $zaznam['kurz_Doporuceni'];


}
//echo "<br><br><br><a href='index.php?list=". $ret_list . "'>Zpět</a>";
