<?php
use Middleware\Staffer\AppContext;
$handler = AppContext::getDb();

$spolecnost = 'GRAFIA, s.r.o.';
$domena = 'grafia.cz'; //změna
//ziskani zasilanych udaju////////////
$jmeno = $pole['jmeno'][1];
$prijmeni = $pole['prijmeni'][1];
$titul = $pole['titul'][1];
$email = $pole['mail'][1];
$telefon = $pole['telefon'][1];
$ulice = $pole['ulice'][1];
$cislo = $pole['cislo_pop'][1];
$mesto = $pole['mesto'][1];
$psc = $pole['psc'][1];
//$id_pozice;

$statement = $handler->query("SELECT nazev,kontakt_id FROM staffer_pozice WHERE id='$id_pozice' LIMIT 1");
$statement->execute();
$zaznam = $statement->fetch(PDO::FETCH_ASSOC);
$kontakt_id = $zaznam['kontakt_id'];
$nazev_pozice = $zaznam['nazev'];

$statement = $handler->query("SELECT jmeno,prijmeni,mail,tel,fax FROM staffer_kontos WHERE id='$kontakt_id' LIMIT 1");
$statement->execute();
$zaznam = $statement->fetch(PDO::FETCH_ASSOC);
$kontos_jmeno = $zaznam['jmeno'];
$kontos_prijmeni = $zaznam['prijmeni'];
$kontos_mail = $zaznam['mail'];
$kontos_tel = $zaznam['tel'];
$kontos_fax = $zaznam['fax'];

//-------------------------------------------------------------------------------------


  $to = $email;
  if ($form== konstStaffer_webdotaz) {
     $subject = '=?utf-8?B?'.  base64_encode("Žádost o informace o pozici $nazev_pozice"). '?=';
  }
  else {
     $subject = '=?utf-8?B?'.  base64_encode("Registrace přihlášky na pozici $nazev_pozice"). '?=';
  }
        //'From: "Odpoved z www.grafia.cz" <info@grafia.cz>' . PHP_EOL .
  $headers = 'From: '.'=?utf-8?B?'.  base64_encode("Odpoved z www.grafia.cz "). '?=' . "<$kontos_mail>" . PHP_EOL .
           'MIME-Version: 1.0' . PHP_EOL .
           'Content-type: text/plain; charset=UTF-8' . PHP_EOL .
           'X-Mailer: PHP-' . phpversion() . PHP_EOL;

  $message = "Tento e-mail je generován automaticky, neodpovídejte na něj!\r\nGrafia, s.r.o. Vám děkuje za zájem!\r\n\r\n";
  if ($form== konstStaffer_webdotaz) {
    $message .=  "Vaše kontaktní údaje byly uloženy a Vaše žádost o informace na pozici ".$nazev_pozice." byla přijata.\r\n";
  }
  else {
    $message .=  "Vaše kontaktní údaje a životopis byly uloženy a Vaše přihláška na pozici ".$nazev_pozice." byla přijata.\r\n";
  }
  $message .= "\r\nV blízké době Vás budeme kontaktovat!\r\n\r\nPěkný den!";
//-----------------------------------------------------------------------------------------------


  $mail1_ok = mail($to, $subject, $message, $headers); // odešleme e-mail
  if (!$mail1_ok) {
     echo '<h3>Došlo k chybě při odeslání e-mailu (1).</h3>';
  }
  else
  {
    // echo '<h3>E-mail 1 byl v pořádku odeslán.</h3>';
  }




//////////////////////////////////////
//mail kontaktni osobe////////////////
//////////////////////////////////////

  $to = $kontos_mail;
  if ($form== konstStaffer_webdotaz) {
             //     $subject = "Zadost o informace na pozici $nazev_pozice";
     $subject = '=?utf-8?B?'.  base64_encode("Žádost o informace o pozici $nazev_pozice"). '?=';
  }
  else {
            //     $subject = "Prihlaska na pozici $subj_nazev_pozice od $jmeno $prijmeni";
     $subject = '=?utf-8?B?'.  base64_encode("Přihláška na pozici $nazev_pozice od $jmeno $prijmeni"). '?=';
  }

//'From: "Uchazeč o práci z www.grafia.cz" <'.$email.'>' . PHP_EOL .
  $headers = 'From: '.'=?utf-8?B?'.  base64_encode("Uchazeč o práci z www.grafia.cz "). '?='   ."<$email>" . PHP_EOL .
           'MIME-Version: 1.0' . PHP_EOL .
           'Content-type: text/html; charset=UTF-8' . PHP_EOL .
           'X-Mailer: PHP-' . phpversion() . PHP_EOL;
//-----------------------------------------------------------------------------------------------

  $message = '<html><body>
  <DIV style="margin:20px;padding:15px;background-color:#EAECE9;border:solid silver 1px;font-family: Arial, Helvetica, sans-serif;">';

  if ($form==konstStaffer_webdotaz) {
      $message .= "<H3>Žádost o informace na pozici</H3>";
      $message .= '<H1 style="font-size:15px;font-family: Arial, Helvetica, sans-serif;color:#C1001F;">'. $nazev_pozice. ' z webu GRAFIA.CZ</H1>';
  }
  else {
     $message .= '<H3>Uchazeč o práci na pozici </H3>';
     $message .= '<H1 style="font-size:15px;font-family: Arial, Helvetica, sans-serif;color:#C1001F;">' . $nazev_pozice. ' z webu GRAFIA.CZ</H1>';
  }
  $message .=
  '<p style="font-size:11px;font-family: Arial, Helvetica, sans-serif;">'.$titul.' '.$jmeno.' '.$prijmeni.'<br>
  '.$ulice.' '.$cislo.'<br>'.$mesto.'<br>'.$psc.'<br><br>telefon: '.$telefon.'<br><br>e-mail: <a href="mailto:'.$email.'">'.$email.'</a>';

  if ($form== konstStaffer_webdotaz) {
      $message .= "<br><br>Dotaz : " . $pole['dotaz'][1];
  }

  $message .= '<br><br>Ostatní údaje z dotazníku a soubor s životopisem (<i>pokud byl požadován</i>) naleznete v redakčním systému Vašeho webu.<br>
  Pokud jste přihlášeni v redakčním systému Vašeho webu můžete<a href="http://www.grafia.cz/rs/index.php?list=cteni_odpovedi&odpoved='.$id_odpoved.'&pozice='.$pozice.'&app=staffer"> přejít </a>přímo k odpovědi.</p>
  </DIV>
  </body></html>';

  $mail2_ok = mail($to, $subject, $message, $headers);
  if(!$mail2_ok) {  // odešleme e-mail
     echo '<h3>Došlo k chybě při odeslání e-mailu (2).</h3>';
  } else {
     //echo '<h3>E-mail 2 byl v pořádku odeslán.</h3>';
  }


?>
