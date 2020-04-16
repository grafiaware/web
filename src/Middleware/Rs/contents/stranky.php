<?php
use Middleware\Rs\AppContext;
$handler = AppContext::getDb();

if (isset($_POST ['stranka'])) {$stranka = $_POST ['stranka'];} else {$stranka = 'x';};
if (isset($sess_prava[$stranka]) AND $sess_prava[$stranka]) {
    if (isset($_POST ['nazev'])) {$nazev = $_POST['nazev'];} else {$nazev = 'Bez názvu';};
    if (isset($_POST ['obsah'])) {$obsah = $_POST ['obsah'];} else {$obsah = '';};
    if ($nazev == '') {$nazev = 'Bez názvu';}
    if (isset($_POST['aktiv'])) {$aktiv = $_POST['aktiv'];} else {$aktiv = 0;}
    if (isset ($_POST['aktiv_lanstart'])) {
                                           $aktiv_lanstart = $_POST['aktiv_lanstart'];
                                           if ($aktiv_lanstart == '') {$aktiv_lanstart = date("Y-m-d");}
                                          }
    if (isset ($_POST['aktiv_lanstop'])) {
                                          $aktiv_lanstop = $_POST['aktiv_lanstop'];
                                          if ($aktiv_lanstop == '') {$aktiv_lanstop = date("Y-m-d");}
                                         }
    if (isset ($_POST['keywords'])) {
                                     $keywords = $_POST['keywords'];
                                     $keywords = trim($keywords);
                                     $keywords = substr ($keywords,0,500);
                                     $keywords = strip_tags ($keywords);
                                    }

    $obsah = html_entity_decode ($obsah,ENT_QUOTES,"UTF-8");

    $db_nazev = 'nazev_'.$lang;
    $db_obsah = 'obsah_'.$lang;
    $db_aktiv = 'aktiv_'.$lang;
    $db_aktivstart = 'aktiv_'.$lang.'start';
    $db_aktivstop = 'aktiv_'.$lang.'stop';
    $db_keywords = 'keywords_'.$lang;


    $editor=$_SESSION['login']['user'];

    if (isset($_POST ['newfile']) OR isset($_POST ['newfile2'])) {
        if (isset($_POST ['newfile'])) {
            include AppContext::getScriptsDirectory().'contents/newfile.php';
            if ($nazev != 'Bez názvu') {
                $successUpdate = $handler->exec("update stranky set editor='$editor',$db_obsah ='$obsah',$db_nazev ='$nazev',$db_aktiv='$aktiv',$db_aktivstart='$aktiv_lanstart',$db_aktivstop='$aktiv_lanstop',$db_keywords='$keywords' where list = '$stranka'");
            }
            if ($nazev == 'Bez názvu') {
                $successUpdate = $handler->exec("update stranky set editor='$editor',$db_obsah ='$obsah',$db_aktiv='$aktiv',$db_aktivstart='$aktiv_lanstart',$db_aktivstop='$aktiv_lanstop',$db_keywords='$keywords' where list = '$stranka'");
            }
        }
        if (isset($_POST ['newfile2'])) {
            include AppContext::getScriptsDirectory().'contents/newfile2.php';
        }
    } else {

        include AppContext::getScriptsDirectory().'contents/delfile.php'; //automaticke odstranovani souboru, ktery je v tab soubory a nema existujici odkaz na strance
        //pouze pokus
        if ($nazev != 'Bez názvu') {
            $ss="update stranky set editor=" . $editor .
            "," . $db_obsah . "=" . $obsah .
            "," . $db_nazev . "=" . $nazev .
            "," . $db_aktiv . "=" . $aktiv .
            "," . $db_aktivstart . "=" . $aktiv_lanstart .
            "," . $db_aktivstop .  "="  . $aktiv_lanstop .
            "," . $db_keywords .  "=" . $keywords .
            "where list = " . $stranka;

            $successUpdate = $handler->exec("update stranky set editor='$editor',$db_obsah ='$obsah',$db_nazev ='$nazev',$db_aktiv='$aktiv',$db_aktivstart='$aktiv_lanstart',$db_aktivstop='$aktiv_lanstop',$db_keywords='$keywords' where list = '$stranka'");
        }
        if ($nazev == 'Bez názvu') {
            $successUpdate = $handler->exec("update stranky set editor='$editor',$db_obsah ='$obsah',$db_aktiv='$aktiv',$db_aktivstart='$aktiv_lanstart',$db_aktivstop='$aktiv_lanstop',$db_keywords='$keywords' where list = '$stranka'");
        }
        include_once AppContext::getScriptsDirectory().'contents/rl.php';
    }
} else {
    echo 'Došlo ke ztrátě oprávnění k editaci!<br>';
    echo '<p class=chyba><br>Data nebyla uložena!</p>';
}




