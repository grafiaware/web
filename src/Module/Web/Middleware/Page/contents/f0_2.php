<?php
//echo "zavolan f0_2 z f0_1.";

if (isset($_POST['jmeno'])) {$jmeno = $_POST['jmeno'];}
   else {$jmeno = "";}
if (isset($_POST['prijmeni'])) {$prijmeni = $_POST['prijmeni'];}
   else {$prijmeni ="";}
if (isset($_POST['firma'])) {$firma = $_POST['firma'];}
   else {$firma ="";}   
if (isset($_POST['ulice'])) {$ulice = $_POST['ulice'];}
   else {$ulice = "";}
if (isset($_POST['obec'])) {$obec = $_POST['obec'];}
   else {$obec = "";}
if (isset($_POST['psc'])) {$psc = $_POST['psc'];}
   else {$psc = "";}      
if (isset($_POST['telefon'])) {$telefon = $_POST['telefon'];}
   else {$telefon = "";}
if (isset($_POST['mail'])) {$mail = $_POST['mail'];}
   else {$mail = "";}
if (isset($_POST['sdeleni'])) {$sdeleni = $_POST['sdeleni'];}
   else {$sdeleni = "";}

if (isset($_POST['ic'])) {$ic = $_POST['ic'];}
   else {$ic = '';}
if (isset($_POST['dic'])) {$dic = $_POST['dic'];}
   else {$dic = '';}
if (isset($_POST['cislo_uctu'])) {$cislo_uctu = $_POST['cislo_uctu'];}
   else {$cislo_uctu = '';} 
if (isset($_POST['zpusob_platby'])) {$zpusob_platby = $_POST['zpusob_platby'];}
   else {$zpusob_platby = '';}   
   
//if (isset($_POST['datum'])) {$datum = $_POST['datum'];}
//  else {$datum = "";}
//if (isset($_POST['misto'])) {$misto = $_POST['misto'];}
//   else {$misto = "";}
//if (isset($_POST['cena'])) {$cena = $_POST['cena'];}
//   else {$cena = "";}   

if (isset($_GET['idplan'])) {$id_planovane_kurzy = $_GET['idplan'];}  //pro hledani v t.planovane_kurzy
//if (isset($_GET['rlist'])) {$ret_list = $_GET['rlist'];}
//else { $ret_list="";}
if (isset($_GET['ci'])) {$id_kurz = $_GET['ci'];}
else { $id_kurz = "";}
//if (isset($_GET['kodk'])) {$kod_kurzu = $_GET['kodk'];}   //pro hledani kurzu v db t.kurz, t.popis_kurzu
//else { $kod_kurzu = "";}


include  'contents/f0.inc.php';     //naplni pole $zaznam udaji z db



$emailkont = $zaznam['email_kontakt'];
$idkurz = $zaznam['id_kurz'];
//echo '***emailkont***' . $emailkont ;
//echo "<br>  kod a nazev*" . $zaznam['kod_kurzu'] . "*" . $zaznam['kurz_Nazev'];

//****************************************************************
//include_once '__vsparf0_2.php';
//****************************************************************


 //  echo "mail***" . $mail  . "***";
 //  echo "sdeleni***" . $sdeleni  . "***";   


$recipient =  $emailkont;      //"support@grafia.cz";
$subject = "Kontakt uchazece o kurz " . 
            $zaznam['kod_kategorie']  . " ". $zaznam['kurz_poradove_cislo'] .
            " " . $zaznam['kurz_Nazev'] ;

  $headers = "From:" . $mail . "\n";
  $headers .= "MIME-Version: 1.0\n";
  $headers .= "Content-Type: text/plain; charset=utf-8\n";
  $headers .= "Content-Transfer-Encoding: 8bit\n";
  $headers .= "Return-Path: " . $emailkont ."\n";

$aktualTimeStamp = time();
$dat = date ("Y-m-d", $aktualTimeStamp);  
$cas = date ("H:i:s", $aktualTimeStamp);
$datcas = date ("Y-m-d H:i:s", $aktualTimeStamp); 
if ($zpusob_platby==1) {$zpusob_platby_slovy="převodem";}
if ($zpusob_platby==2) {$zpusob_platby_slovy="složenkou";}
if ($zpusob_platby==3) {$zpusob_platby_slovy="v hotovosti";}


$message = "Údaje zájemce o kurz - odesláno z webu dne: " . $dat . " " . $cas    ."\n\n" .
          "Kurz: " . $zaznam['kod_kategorie']  . " ". $zaznam['kurz_poradove_cislo']  .  " " . $zaznam['kurz_Nazev'] . "\n" .
         
          "Datum konání: " .   datum_ymd_dmy2($zaznam_planovane['od_data_plan']) . ' - ' . datum_ymd_dmy2($zaznam_planovane['do_data_plan']). "\n"  .    
          "Místo konání: " . $zaznam_planovane['misto_obec'] . ' ' . $zaznam_planovane['misto_ulice'] . ' ' . $zaznam_planovane['misto_cislo'] . "\n"  .
          "Cena za osobu (Kč): " . $zaznam_planovane['cena_plan']  .
          "\n\n" .
           " Jméno: " . $jmeno . "\n" .
           " Příjmení: " . $prijmeni . "\n" .
           " Firma: " . $firma . "\n" .
           " Ulice: " . $ulice .  "\n" .
           " Obec: " . $obec .  "\n" .
           " PSČ: " . $psc .  "\n" .
           " Telefon: "  . $telefon . "\n" .
           " E-mail: " . $mail . "\n"  .
           " IČ: " . $ic . "\n"  .
           " DIČ: " . $dic . "\n"  .
           //" Způsob platby: " . $zpusob_platby_slovy . "\n"  .
           //" Číslo účtu: " . $cislo_uctu . "\n"  .
           " Sdělení: " . "\n"  .$sdeleni  . "\n" ;



//zápis do db
//strip_tags (); nevhodne na kontrolu formulare

if (get_magic_quotes_gpc() ){ /*tak neslashovat*/
}
else {
     $jmeno = addslashes(trim($jmeno));
     $prijmeni = addslashes(trim($prijmeni));
     $firma = addslashes(trim($firma));
     $ulice = addslashes(trim($ulice));
     $obec = addslashes(trim($obec));
     $psc = addslashes(trim($psc));
     $mail = addslashes(trim($mail));
     $telefon = addslashes(trim($telefon));
     $ic = addslashes(trim($ic));
     $dic = addslashes(trim($dic));
     $zpusob_platby = addslashes(trim($zpusob_platby));
     $cislo_uctu = addslashes(trim($cislo_uctu));
     $sdeleni = addslashes(trim($sdeleni));
     
     //$jmeno = htmlentities($jmeno, ENT_QUOTES,"UTF-8"); //pak by to bylo v db zaentitovane
}

    
 //cvicne    
 //$jmenoent=htmlentities ( $jmeno, ENT_QUOTES,"UTF-8");     
 //$mailent=htmlentities ( $mail, ENT_QUOTES,"UTF-8");
 //$zpetnejmeno = html_entity_decode ($jmenoent,ENT_QUOTES,"UTF-8");
 //$zpetnemail = html_entity_decode ($mailent, ENT_QUOTES,"UTF-8");


use Web\Middleware\Page\AppContext;
$handler = AppContext::getDb();


function vloz_par($s){
     if ($s === "") {
          $ss= "null";
     }
     else{
          $ss= "'" . $s . "'";
     }     
return $ss;
}
     $prikaz = "INSERT INTO prihlasky_mail " .
     "(jmeno, prijmeni,firma, ulice, obec, psc, telefon, mail, sdeleni, ic, dic, cislo_uctu, zpusob_platby, datum_prihl, id_kurz_FK, id_planovane_kurzy_FK) ". 
     "VALUES (" . vloz_par($jmeno) . "," . vloz_par($prijmeni) . "," .
          vloz_par($firma). "," .
          vloz_par($ulice). "," .
          vloz_par($obec) . "," . vloz_par($psc) . "," . vloz_par($telefon) . "," .
          vloz_par($mail) . "," . vloz_par($sdeleni) . "," .
          vloz_par($ic) . "," . vloz_par($dic) . "," . vloz_par($cislo_uctu) . "," .  vloz_par($zpusob_platby_slovy) . "," . 
          vloz_par($datcas) .      
     $ok = $handler->exec($prikaz);
     //fce v db - now()

      if ($ok) { //echo "<br>insert OK";
               $message .= "\n\n Data byla uložena do databáze.";
      }
      else { //echo "<br>insert neni OK";
               $message .= "\n\n Data se nepodařilo uložit do databáze.";
      }


mail($recipient, $subject, $message, $headers);
?>

<center>
<h4>Děkujeme, Vaše údaje byly úspěšně odeslány!<br>Budete kontaktováni naším pracovníkem.</h4>
</center>

<br><br><br>
<!-- <a href="index.php?list=<?php /*echo $ret_list;*/ ?>">Zpět (na harmionogram otevřených kurzů)</a> -->