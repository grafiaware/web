<?php
use Middleware\Rs\AppContext;
$handler = AppContext::getDb();

IF ($sess_prava['pormenu']) {

    IF (isset ($_GET['pormenu'])) {
        $pormenu=$_GET['pormenu'];
        $db_stranka = $pormenu{0};
        $db_nazev = 'nazev_'.$lang;
        ////////////////////
        // Poradi polozek //
        ////////////////////
        echo '<H4>Pořadí položek v';

        if ($zobraz_prvek['hlavni_menu'] )     {
        IF ($db_stranka == $menu_l) {echo ' hlavním menu</h4>';}  }
        if ($zobraz_prvek['vodo_menu'] )     {
        IF ($db_stranka == $menu_s) {echo 'e vodorovném menu</h4>';}  }
        if ($zobraz_prvek['horni_menu'] )     {
        IF ($db_stranka == $menu_h) {echo ' horním menu</h4>';}    }

        if ($zobraz_prvek['prave_menu'] )     {
        IF ($db_stranka == $menu_p ) {echo ' pravém menu</h4>';}    }
        if ($zobraz_prvek['leve_menu'] )     {
        IF ($db_stranka == $menu_l ) {echo ' levém menu</h4>';}    }

        //Definice menu
        $rozsah_urovne=3;//rika kolik znaku pripada na jednu uroven menu
        $defurovne = array (
        0=>'class=polozka0',
        1=>'class=polozka1',
        2=>'class=polozka2',
        3=>'class=polozka3',
        4=>'class=polozka4',
        5=>'class=polozka5'
        ); //Definuje prislusne CSS k jednotlivym urovnim
        $uroven = ceil(strlen($pormenu)/$rozsah_urovne);
        $delka = $rozsah_urovne*$uroven;
        IF (strlen($pormenu) == 1) {$uroven = 0;}
        //Dotazy do DB na polozky jednotlivych urovni
        //Nacteni polozek prvni urovne (je zobrzena vzdy) do pole $data
        $i = 0;
        $data = array (0=> array());
        $statement = $handler->query("SELECT list,$db_nazev, poradi, aut_gen FROM stranky WHERE (left(list,1)='$db_stranka') AND (char_length(list)='$rozsah_urovne') ORDER BY `poradi` ASC");
        $statement->execute();
//        WHILE ($zaznam = MySQL_Fetch_Array($vysledek)) {
        $zaznamy = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($zaznamy as $zaznam) {
            $data[0][$i]= array($zaznam['list'],$zaznam[$db_nazev], $zaznam['poradi'],$zaznam['aut_gen']);
            $i++;
        }
        //Kontrola a opatreni, zda existuje vubec nejaka polozka menu
        IF (count ($data[0]))   {
            echo '<p>Vyberte kliknutím na položku v menu níže tu úroveň, ve které chcete pořadí položek měnit.<br/>
            Položky v menu jsou znázorněny v aktuálním (starém) pořadí. <br/>
            Chcete-li pořadí v úrovni změnit, musíte položky zvolené úrovně  nově  očíslovat.<br>
            Položkám, jejichž umístění chcete změnit, nastavte pořadí, ve kterém je chcete zobrazovat, a stiskněte tlačítko <b>Přerovnat</b>.</p>';
            //Nacteni polozek dalsich urovni do pole $data
            if ($uroven >= 1) {
                $i=1;
                while ($i <= $uroven) {
                    $delkalist=($i+1)*$rozsah_urovne;
                    $delkacastlist=$delkalist-$rozsah_urovne;
                    IF ($delkacastlist == 0) {
                        $delkacastlist=$rozsah_urovne;
                    }
                    $castlist=substr($pormenu, 0, $delkacastlist);
                    $data [$i]=array();
                    $j=0;
                    $statement = $handler->query("SELECT list,$db_nazev,poradi,aut_gen FROM stranky WHERE (left(list,$delkacastlist)='$castlist') AND (char_length(list)='$delkalist') ORDER BY `poradi` ASC");
                    $statement->execute();
//                    while ($zaznam = MySQL_Fetch_Array($vysledek)) {
                    $zaznamy = $statement->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($zaznamy as $zaznam) {
                        $data [$i][$j]= array($zaznam['list'],$zaznam[$db_nazev],$zaznam['poradi'], $zaznam['aut_gen']);
                        $j++;
                    }
                    $i++;
                }
            }

            //Definice funkce rekurze menu
            function vypis_urovne_menu_pormenu ($i,$data,$rozsah_urovne,$pormenu,$lang,$defurovne,$sess_prava) {
                foreach ($data[$i] as $polozka) {
                    IF ($sess_prava[$polozka[0]]) {
                        $dovest=''; /*if ($polozka[3] == 0) {$dovest='';}*/
                        if (mb_substr($polozka[3],0,1) == 1) {
                            $dovest='<span class=poznmenu><br>&nbsp;(publikace&nbsp;plán.kurzů)</span>';
                        }
                        if (mb_substr($polozka[3],0,1) == 2) {
                            $dovest='<span class=poznmenu><br>&nbsp;(publikace&nbsp;katalog.kurzů)</span>';
                        }
                        if (strlen($pormenu)==strlen($polozka[0])) {
                            echo '<tr><td id="table_poradi_td1a3">
                                '.$polozka[2].'.</td>
                                <td id="table_poradi_td2">
                                <a href="index.php?list=pormenu2&pormenu='.$polozka[0].'&language='.$lang.'" '.$defurovne[$i].'on >'.$polozka[1]. $dovest . '</a>
                                </td>
                                <td id="table_poradi_td1a3">
                                <input type="text" size="1" maxlength="2" name="'.$polozka[0].'" value="">&nbsp;
                                </td>';

                            /*IF  (!(is_numeric (@$_POST[$polozka[0]] ))   and  (trim(@$_POST[$polozka[0]] != ""))   )*/
                            if (!(ctype_digit(@$_POST[$polozka[0]] )) and  (trim(@$_POST[$polozka[0]] != ""))) {
                                echo '<td><p class=chyba>Zadali jste nečíselné údaje do sloupce nové pořadí!</p></td>';
                            } else {
                                echo '<td>&nbsp;</td>';
                            }
                            //IF  ((is_numeric (@$_POST[$polozka[0]] )) and ( @$_POST[$polozka[0]] == 0  ) )  {
                            //     { echo '<td><p class=chyba>Zadali jste 0.pořadí!</p></td>';}
                            echo '</tr>';
                        } else {
                            echo '<tr><td id="table_poradi_td1a3">&nbsp;</td>
                                <td id="table_poradi_td2">
                                <a href="index.php?list=pormenu2&pormenu='.$polozka[0].'&language='.$lang.'" '.$defurovne[$i].' >'.$polozka[1]. $dovest . '</a>
                                </td>
                                <td id="table_poradi_td1a3">&nbsp;</td>'.
                                '<td>&nbsp;</td>' .
                                '</tr>';
                        }
                    }
                    if ($polozka[0] == substr($pormenu,0,$i*$rozsah_urovne+$rozsah_urovne)) {
                        vypis_urovne_menu_pormenu ($i+1,$data,$rozsah_urovne,$pormenu,$lang,$defurovne,$sess_prava);
                    }
                }
            }

            if (count($_POST)!= 0) {
                /* post (existuje vzdy) ma prvky - > kontroluju zda vubec neco zadali*/
                $alfa=0;   $jenula=0;
                foreach ($_POST as $polozka){
                    if   ( !(ctype_digit($polozka )) and  (trim($polozka) != "")   )   {
                        $alfa=1;
                    }
                }

                if ($alfa) {
                    /*bylo chybne zadani - pismena, chyba se zobrazi u radky*/
                } else {
                    IF (array_sum($_POST)) {
                        /*bylo zadano nejake poradi nenulove*/
                    } else {
                        /*bylo zadano pouze poradi nulove nebo zadne*/
                        echo '<p class=chyba>Nezadali jste žádné údaje do sloupce nové pořadí!</p>' ;
                    }
                }
            }

            echo '
                <DIV ID="rs_pormenu_menu">
                <form method="POST" action="index.php?list=pormenu3&pormenu=<?php echo $pormenu; ?>">

                <table id="rs_table_poradi"> <!-- cellspacing="1" -->
                <tr>
                <td id="rs_table_poradi_td1a3">
                <em><span>staré pořadí</span><em>
                </td>
                <td id="rs_table_poradi_td2">
                <em><span>položka</span></em>
                </td>
                <td id="rs_table_poradi_td1a3">
                <em><span>nové pořadí</span></em>
                </td>
                <td>&nbsp;</td>
                </tr>
                ';
            $i=0;
            vypis_urovne_menu_pormenu ($i,$data,$rozsah_urovne,$pormenu,$lang,$defurovne,$sess_prava);
            echo '
                </table>
                </DIV>

                <DIV ID="rs_pormenu_form">
                <br><center><input value="Přerovnat" type="submit"></center>
                </form>
                </DIV>
                ';
        } else {
            echo '<p class=chyba>Menu neobsahuje žádnou položku.</p>';
        }
    } else {
        echo '<p class=chyba>Nevybrali jste žádné z menu!</p>';
        include 'contents/pormenu.php';
    }

} else {
    echo '<p class=chyba>Nemáte oprávnění k tomuto úkonu</p>';
}
