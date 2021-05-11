<?php
//vyhleda kurz podle id   kurzu
use Web\Middleware\Web\AppContext;
$handler = AppContext::getDb();

$statement = $handler->query("select * , c_kategorie.kod as kod_kategorie
                        from kurz
                        join popis_kurzu
                            on (kurz.id_popis_kurzu_FK = popis_kurzu.id_popis_kurzu)
                        left join c_kontakt
                            on (kurz.id_c_kontakt_FK = c_kontakt.id_c_kontakt)
                        left join c_kategorie   on  c_kategorie.id_c_kategorie= kurz.id_c_kategorie_FK     
                        where
                           id_kurz="  . $id_kurz 
                        );
//(kod_kurzu='$kod_kurzu')    
$num_rows = $statement->rowCount();

IF ($num_rows == 0)  {
    echo '<span class="chyba">Litujeme, ale požadovaná informace o kurzu není dostupná.</span>';
    }
else {
    //  prectene informace
//        $zaznam = MySQL_Fetch_Array($vysledek);
        $zaznam = $statement->fetch(PDO::FETCH_ASSOC);
}    



//vyhleda naplan.kurz podle id_planovane_kurzy

//$vysledek = MySQL_Query("select * from planovane_kurzy
//                        left join c_kontakt  on (planovane_kurzy.id_c_kontakt_FK = c_kontakt.id_c_kontakt)
//                        left join s_misto_kurzu on (planovane_kurzy.id_s_misto_kurzu_FK = s_misto_kurzu.id_s_misto_kurzu)
//                        where
//                        (planovane_kurzy.id_planovane_kurzy = '$id_planovane_kurzy')    
//                        ");
$statement = $handler->query("select * from planovane_kurzy
                        left join c_kontakt  on (planovane_kurzy.id_c_kontakt_FK = c_kontakt.id_c_kontakt)
                        left join s_misto_kurzu on (planovane_kurzy.id_s_misto_kurzu_FK = s_misto_kurzu.id_s_misto_kurzu)
                        where
                        (planovane_kurzy.id_planovane_kurzy = '$id_planovane_kurzy')    
                        ");
//MySQL_CLOSE ($connect);
//$num_rows = mysql_num_rows($vysledek);
$num_rows = $statement->rowCount();

IF ($num_rows == 0)  {
    echo '<span class="chyba">Litujeme, ale požadovaná informace o plánovaném kurzu není dostupná.</span>';
    }
else {
    //  prectene informace
//        $zaznam = MySQL_Fetch_Array($vysledek);
        $zaznam = $statement->fetch(PDO::FETCH_ASSOC);
}    




function datum_ymd_dmy2 ($sdatum=false) {
     if ($sdatum) {
              $dpom = substr($sdatum,8,2) .".".  substr($sdatum,5,2) ."." .substr($sdatum,2,2) ;
     }
     else {$dpom = "";}
  return $dpom ;
}
?>