<?php
use Middleware\Rs\AppContext;
$handler = AppContext::getDb();

$rozsah_urovne=3;//rika kolik znaku pripada na jednu uroven menu

//echo "<br>*sess prava v new_list:* "  ; print_r($sess_prava);
//echo "<br>pole_GET: " ; print_r($_GET);



if ($sess_prava['newlist']) {
    //Zpracovani a kontrola vstupnich promennych
    $ok=1; $ermsg='';
    if (isset ($_GET['newnazev'])) {
        $newnazev = $_GET['newnazev'];
        $newnazev = strip_tags ($newnazev);
        $newnazev = trim ($newnazev);
        if (strlen($newnazev)==0) {
            $ok=0;
        }
    } else {
        $ok=0;
    }
    
    if (isset($_GET['newstranka'])) {
       $newstranka = $_GET['newstranka'];
       if (strlen($newstranka)<=0) {$ok=0;}
    } else {
       $ok=0;
    }

    if (isset($_GET['pozice'])) {
        $pozice=$_GET['pozice'];
    } else {
        $pozice=0;
    }

    if (isset($_GET['publikace'])) {
        $publikace=$_GET['publikace'];
    } else {
        $publikace=0;
    }
   

    if ($publikace!=0) {
       if ($publikace==1)  {
           $spub= "plánovaných kurzů";
       }
       if ($publikace==2)  {
           $spub= "katalogových kurzů";
       }

      $statement = $handler->query("SELECT * FROM stranky WHERE aut_gen ='" . $publikace. "'" );
    $statement->execute();
      $i = mysql_num_rows ($vysledek);
      //echo '<br>*i* je pocet stranek , kde publikace =' . $publikace . ' ' . $i;
      IF ($i!=0) {
          $ok  = 0; $ermsg ='Stránka pro publikaci ' . $spub .' již existuje.';
      }
    }  



    if ($ok == 1) {

        $editor=$_SESSION['login']['user'];
        if (!$pozice) {              //*** na urovni, nebude to submenu
            //dotaz na volne cislo stranky
            $delka_newstranka = strlen($newstranka);
            $delka_vyssi_urovne = $delka_newstranka-$rozsah_urovne;
            if ($delka_newstranka <= $rozsah_urovne) {
                $delka_vyssi_urovne=1; $newstranka=$newstranka{0};$delka_newstranka=$rozsah_urovne;
            }
            $jmeno_vyssi_urovne = substr ($newstranka,0,$delka_vyssi_urovne);
            $statement = $handler->query("SELECT list FROM stranky
                                 WHERE (left(list,$delka_vyssi_urovne)='$jmeno_vyssi_urovne') AND
                                       (char_length(list)='$delka_newstranka')");
            $statement->execute();
            $i = $statement->rowCount();
            if ($i == 0) {
                $newstranka=$newstranka.'01';
            } else {
                $data=array();
                $i=0;
                $delka_fragment = $rozsah_urovne - 1;
                //                                  WHILE ($zaznam = MySQL_Fetch_Array($vysledek)) {
                $zaznamy = $statement->fetchAll(PDO::FETCH_ASSOC);
                foreach ($zaznamy as $zaznan) {
                    $fragment = substr ($zaznam['list'],-$delka_fragment);
                    $data[$i] = $fragment;
                    $i++;
                }

                $fragment = 01;
                $volno = 0;
                while (!$volno) {
                    if (in_array($fragment, $data)) {
                        $fragment = $fragment+1;
                    } else {
                        $volno = 1;
                    }
                }

                $fragment = str_pad($fragment, $delka_fragment, "0", STR_PAD_LEFT);
                if (strlen($newstranka) == 1) {
                    $newstranka = $newstranka.$fragment;
                } else {
                    $newstranka = $jmeno_vyssi_urovne.'_'.$fragment;
                }                
            }
        } else {           // *** nova stranka bude v submenu
            $delka_fragment = $rozsah_urovne - 2;
            $newstranka=$newstranka.'_'.str_repeat ("0",$delka_fragment).'1';
        }

        /* kontrola - nedovoli dalsi nizsi uroven menu - */   
        //echo '<br>newstranka : ' . $newstranka;
        //echo '<br>rozsahurovne*maxpoceturovnimenu : ' . $rozsah_urovne * $max_pocet_urovni_menu;

        if ( strlen($newstranka) > ($rozsah_urovne * $max_pocet_urovni_menu)) {
            echo '<p class=chyba>Nelze založit další nižší úroveň menu!</p>';
            include 'contents/newlist2.php';
        } else { 
            $newlist=$newstranka;
            $handler->exec("INSERT INTO stranky (list,nazev_lan1,aktiv_lan1,nazev_lan2,aktiv_lan2,nazev_lan3,aktiv_lan3,aut_gen,editor)
                     VALUES ('$newlist','$newnazev','0','$newnazev','0','$newnazev','0','$publikace','$editor')");
            $handler->exec("ALTER TABLE opravneni ADD $newlist TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0'");
                        // *** nastaveni opravneni pro administratora na nove vytvorenou stranku
            //MySQL_Query("UPDATE opravneni SET $newlist = '1' WHERE user = 'administrator'");
            //MySQL_Query("UPDATE opravneni SET $newlist = '1' WHERE user = 'jab1408'");
            //MySQL_Query("UPDATE opravneni SET $newlist = '1' WHERE user = 'vlse2610'");
            //MySQL_Query("UPDATE opravneni SET $newlist = '1' WHERE user = '$editor'");

            $handler->exec("UPDATE opravneni SET $newlist = '1' WHERE (role = 'adm' or  role = 'sup')");
            $handler->exec("UPDATE opravneni SET $newlist = '1' WHERE user = '$editor'");


            $user = $_SESSION ["sess_user"];
            $statement = $handler->query("select * from opravneni where user='$user'");
            $statement->execute();
            $zaznam_opravneni = $statement->fetch(PDO::FETCH_ASSOC);
            $_SESSION ["sess_prava"] = $zaznam_opravneni;

            echo '
                <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
                <html>
                <head>
                <meta http-equiv="content-type" content="text/html; charset=utf-8">
                <meta http-equiv="refresh" content="0 ; URL=index.php?list=rl&stranka=<?php echo $newlist; ?>">
                <title></title>
                </head>
                <body>
                </body>
                </html>';
        }
    } else {
        if (!($ermsg===''))  {
            echo '<p class=chyba>' . $ermsg .'</p>';   
        } else {
            echo '<p class=chyba>Nezadali jste potřebné údaje pro založení nové stránky!</p>';
            include 'contents/newlist2.php';
        }  
    }

} else {
    echo '<p class=chyba><br>Nemáte oprávnění k tomuto úkonu.</p>';
}

