<?php
 /**
  * Vezme ze vstupního řetězce prvních 50 znaků a pomocí regul.výrazu
  * zkontroluje, zda se jedná o validní mail.adresu.
  *
  * @param string $text kontrolovaný řetězec
  * @return boolean true = je OK, false = není OK
  */
  function kontrola_email ($text) {
       $t = mb_substr($text,0,50,"UTF-8");

       if (!(mb_ereg('^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)+$', $t) )) {$OK=FALSE; }
       //if (!(ereg('@[^@]+[.][a-zA-Z]+$', $t) ) )  {$OK=FALSE; }
       else  {$OK=TRUE;}

       return $OK ;
  } // function kontrola_email

  /**
  * Vezme ze vstupního řetězce prvních 5 znaků a pomocí regul.výrazu
  * zkontroluje, zda se jedná o validní pošt.směrovací číslo.
  *
  * @param string $text kontrolovaný řetězec
  * @return boolean true = je OK, false = není OK
  */
  function kontrola_psc ($text) {
       $t = mb_substr($text,0,5, "UTF-8");

       if (!(mb_ereg('[0-9]{5}' , $t) )) {$OK=FALSE; }
       else  {$OK=TRUE;}

       return $OK ;
  } // function kontrola_psc
//*****************************************************************************************************
//echo "jsme v f0_1.";

if (isset($_GET['idplan'])) {$id_planovane_kurzy = $_GET['idplan'];}  //pro hledani v t.planovane_kurzy
if (isset($_GET['ci'])) {$id_kurz = $_GET['ci'];}
//if (isset($_GET['kodk'])) {$kod_kurzu = $_GET['kodk'];}   //pro hledani kurzu v db t.kurz, t.popis_kurzu
//if (isset($_GET['rlist'])) {$ret_list = $_GET['rlist'];} //pro navrat na "vysilajici" stranku

//echo "ret_list" . $ret_list;


$kontrola_OK = true ;


//naplneni z $_POST , po Odeslat z f0
//if (isset($_POST['datum'])) {$datum = $_POST['datum'];}
//   else {$datum = '';}
//if (isset($_POST['misto'])) {$misto = $_POST['misto'];}
//  else {$misto = '';}
//if (isset($_POST['cena'])) {$cena = $_POST['cena'];}
//   else {$cena = '';}

if (isset($_POST['jmeno'])) {$jmeno = $_POST['jmeno'];}
   else {$jmeno = '';}
if (isset($_POST['prijmeni'])) {$prijmeni = $_POST['prijmeni'];}
   else {$prijmeni = '';}
if (isset($_POST['firma'])) {$firma = $_POST['firma'];}
   else {$firma = '';}
if (isset($_POST['ulice'])) {$ulice = $_POST['ulice'];}
   else {$ulice = '';}
if (isset($_POST['obec'])) {$obec = $_POST['obec'];}
   else {$obec = '';}
if (isset($_POST['psc'])) {$psc = $_POST['psc'];}
   else {$psc = '';}
if (isset($_POST['telefon'])) {$telefon = $_POST['telefon'];}
   else {$telefon = '';}
if (isset($_POST['mail'])) {$mail = $_POST['mail'];}
   else {$mail = '';}
if (isset($_POST['sdeleni'])) {$sdeleni = $_POST['sdeleni'];}
   else {$sdeleni = '';}

if (isset($_POST['ic'])) {$ic = $_POST['ic'];}
   else {$ic = '';}
if (isset($_POST['dic'])) {$dic = $_POST['dic'];}
   else {$dic = '';}
if (isset($_POST['cislo_uctu'])) {$cislo_uctu = $_POST['cislo_uctu'];}
   else {$cislo_uctu = '';}
if (isset($_POST['zpusob_platby'])) {$zpusob_platby = $_POST['zpusob_platby'];}
   else {$zpusob_platby = '';}


//   echo "mail***" . $mail  . "***";
//   echo "sdeleni***" . $sdeleni  . "***";
   
//???
//if (isset($_POST['lang'])) {$lang = $_POST['lang'];} else {$lang = 'lan1';}


include  'contents/f0.inc.php';


?>

<H2>Přihláška pro jednotlivce</H2>
<h3>Zkontrolujte si Vaše údaje</h3>
<p>


<form method="post" action="index.php?list=f0_2&ci=<?php echo $id_kurz; ?>&idplan=<?php echo $id_planovane_kurzy; ?>">


<FIELDSET> <!--style="width:355px;border-color: rgb(41,81,148);font-size:12px" -->
<LEGEND><b>Kurz</b></LEGEND>
  <table border="0px">
  <tbody>
    <tr>
        <td class="prihlaska_kurz_popis"> &nbsp;
        </td>
        <td class="prihlaska_kurz_hodnota">  <?php echo $zaznam['kod_kategorie']." " .
                                              $zaznam['kurz_poradove_cislo'] ; ?>
          <input  type="hidden" name="kod" size="30" value="<?php echo $zaznam['kod_kategorie']." " .
                                              $zaznam['kurz_poradove_cislo']; ?>">
        </td>
    </tr>
    <tr>
        <td class="prihlaska_kurz_popis">Název kurzu
        </td>
        <td>  <?php echo $zaznam['kurz_Nazev'];  ?>
           <input  type="hidden" name="nazev" size="30" value="<?php echo $zaznam['kurz_Nazev'];?>">
        </td>
    </tr>


    <tr>
        <td class="prihlaska_kurz_popis">Datum
        </td>
        <td>   <?php echo datum_ymd_dmy2($zaznam_planovane['od_data_plan']) . ' - ' . datum_ymd_dmy2($zaznam_planovane['do_data_plan']) ;  ?>
        </td>
    </tr>
    <tr>
        <td class="prihlaska_kurz_popis"> Místo konání
        </td>
        <td>   <?php echo $zaznam_planovane['misto_obec'] . ' ' . $zaznam_planovane['misto_ulice'] . ' ' . $zaznam_planovane['misto_cislo'] ?>
        </td>
    </tr>
    <tr>
        <td class="prihlaska_kurz_popis">Cena za osobu (Kč)
        </td>
        <td> <?php echo $zaznam_planovane['cena_plan']; ?>
        </td>
    </tr>

  </tbody>
  </table>
</FIELDSET>

<br>
<FIELDSET> <!--style="width:355px;border-color: rgb(41,81,148);font-size:12px" -->
<LEGEND><b>Vaše údaje</b></LEGEND>

  <table border="0px" >
        <!--style="margin-left: auto; margin-right: auto; text-align: left; width: 350px;font-size:12px"
         border="0" cellpadding="2" cellspacing="2"> -->
    <tbody>
      <tr>
        <td class="prihlaska_kurz_popis">Jméno:</td>
        <td class="prihlaska_kurz_hodnota_pasivni">
             <?php  echo htmlentities(trim($jmeno),ENT_QUOTES,"UTF-8"); ?>
             <input type="hidden" maxlength="50" name="jmeno" size="30" value="<?php  echo htmlentities(trim($jmeno),ENT_QUOTES,"UTF-8"); ?>">
            &nbsp;
        </td>
        <td>
            &nbsp;
        </td>
      </tr>
      <tr>
        <td class="prihlaska_kurz_popis">Příjmení: )*</td>
        <td class="prihlaska_kurz_hodnota_pasivni">
            <?php  echo htmlentities(trim($prijmeni),ENT_QUOTES,"UTF-8"); ?>
            <input type="hidden" maxlength="50" name="prijmeni" size="30" value="<?php  echo htmlentities(trim($prijmeni),ENT_QUOTES,"UTF-8"); ?>">
            &nbsp;

        </td>
        <td>
            <?php
                if (!($prijmeni)) {
                     echo '<span class="prihlaska_kurz_chyba">Povinný údaj.</span>';
                        $kontrola_OK=false;
                }
            ?>
            &nbsp;
        </td>
      </tr>

     <tr>
        <td class="prihlaska_kurz_popis">Firma: </td>
        <td class="prihlaska_kurz_hodnota_pasivni">
            <?php  echo htmlentities(trim($firma),ENT_QUOTES,"UTF-8"); ?>
            <input type="hidden" maxlength="50" name="firma" size="30" value="<?php  echo htmlentities(trim($firma),ENT_QUOTES,"UTF-8"); ?>">
            &nbsp;

        </td>
        <td>
            &nbsp;
        </td>
      </tr>




      <tr>
        <td class="prihlaska_kurz_popis">Ulice:</td>
        <td class="prihlaska_kurz_hodnota_pasivni">
            <?php  echo htmlentities(trim($ulice),ENT_QUOTES,"UTF-8"); ?>
            <input type="hidden" maxlength="50" name="ulice" size="30" value="<?php  echo htmlentities(trim($ulice),ENT_QUOTES,"UTF-8"); ?>">
            &nbsp;
        </td>
        <td>&nbsp;
        </td>
      </tr>
      <tr>
        <td class="prihlaska_kurz_popis">Obec:</td>
        <td class="prihlaska_kurz_hodnota_pasivni">
            <?php  echo htmlentities(trim($obec),ENT_QUOTES,"UTF-8"); ?>
            <input type="hidden" maxlength="50" name="obec" size="30" value="<?php  echo htmlentities(trim($obec),ENT_QUOTES,"UTF-8"); ?>">
            &nbsp;
        </td>
        <td>&nbsp;
        </td>
      </tr>
      <tr>
        <td class="prihlaska_kurz_popis">PSČ:</td>
        <td class="prihlaska_kurz_hodnota_pasivni">
            <?php  echo htmlentities(trim($psc),ENT_QUOTES,"UTF-8"); ?>
            <input type="hidden" maxlength="5" name="psc" size="5" value="<?php  echo htmlentities(trim($psc),ENT_QUOTES,"UTF-8"); ?>">
            &nbsp;
        </td>
        <td>
            <?php
                if ($psc) {
                    if (kontrola_psc($psc)) {
                        //echo '<span class="prihlaska_kurz_chyba">Kontrola mailu OK.</span>';
                    }
                    else  { echo '<span class="prihlaska_kurz_chyba">Nesprávný údaj.</span>';
                        $kontrola_OK=false;
                    }
                }
            ?>
            &nbsp;
        </td>
      </tr>
      <tr>
        <td class="prihlaska_kurz_popis">Telefon (mobil):</td>
        <td class="prihlaska_kurz_hodnota_pasivni">
            <?php  echo htmlentities(trim($telefon),ENT_QUOTES,"UTF-8"); ?>
            <input type="hidden" maxlength="40" name="telefon" size="20" value="<?php  echo htmlentities(trim($telefon),ENT_QUOTES,"UTF-8"); ?>  ">
             &nbsp;
        </td>
        </td>
        <td>&nbsp;
        </td>
      </tr>
      <tr>
        <td class="prihlaska_kurz_popis">E-mail: )*</td>
        <td class="prihlaska_kurz_hodnota_pasivni">
             <?php  echo htmlentities(trim($mail),ENT_QUOTES,"UTF-8"); ?>
             <input type="hidden" maxlength="50" name="mail" size="30" value=<?php  echo htmlentities(trim($mail),ENT_QUOTES,"UTF-8"); ?> >
             &nbsp;
        </td>
        <td>
            <?php
                if (!($mail)) {
                     echo '<span class="prihlaska_kurz_chyba">Povinný údaj.</span>';
                        $kontrola_OK=false;

                }
                else {
                    if (kontrola_email($mail)) {
                        //echo '<span class="prihlaska_kurz_chyba">Kontrola mailu OK.</span>';
                    }
                    else  { echo '<span class="prihlaska_kurz_chyba">Nesprávný údaj.</span>';
                        $kontrola_OK=false;
                    }
                }
             ?>
            &nbsp;
        </td>
      </tr>



      <tr>
        <td class="prihlaska_kurz_popis">IČ:</td>
        <td class="prihlaska_kurz_hodnota_pasivni">
            <?php  echo htmlentities(trim($ic),ENT_QUOTES,"UTF-8"); ?>
            <input type="hidden" maxlength="20" name="ic" size="20" value="<?php  echo htmlentities(trim($ic),ENT_QUOTES,"UTF-8"); ?>  ">
             &nbsp;
        </td>
        </td>
        <td>&nbsp;
        </td>
      </tr>
      <tr>
        <td class="prihlaska_kurz_popis">DIČ:</td>
        <td class="prihlaska_kurz_hodnota_pasivni">
            <?php  echo htmlentities(trim($dic),ENT_QUOTES,"UTF-8"); ?>
            <input type="hidden" maxlength="20" name="dic" size="20" value="<?php  echo htmlentities(trim($dic),ENT_QUOTES,"UTF-8"); ?>  ">
             &nbsp;
        </td>
        </td>
        <td>&nbsp;
        </td>
      </tr>

      <!--  docasne neni  -->
      <tr class="docasne_neni">
       <td class="prihlaska_kurz_popis">Způsob platby:</td>
       <td class="prihlaska_kurz_hodnota_pasivni">
         <input  type="hidden" maxlength="20" name="zpusob_platby" size="20" value="<?php  echo htmlentities(trim($zpusob_platby),ENT_QUOTES,"UTF-8"); ?> ">
         <?php
           if ($zpusob_platby == 1) {echo 'převodem';}
           if ($zpusob_platby == 2) {echo 'složenkou';}
           if ($zpusob_platby == 3) {echo 'v hotovosti';}
         ?>
       </td>
      </tr>

      <tr class="docasne_neni">
        <td class="prihlaska_kurz_popis">Číslo účtu:</td>
        <td class="prihlaska_kurz_hodnota_pasivni">
            <?php  echo htmlentities(trim($cislo_uctu),ENT_QUOTES,"UTF-8"); ?>
            <input type="hidden" maxlength="40" name="cislo_uctu" size="20" value="<?php  echo htmlentities(trim($cislo_uctu),ENT_QUOTES,"UTF-8"); ?>  ">
             &nbsp;
        </td>
        </td>
        <td>&nbsp;
        </td>
      </tr>




      <tr>
        <td class="prihlaska_kurz_popis">Sdělení:</td>
        <td class="prihlaska_kurz_hodnota_pasivni"> <br>
            <FIELDSET>
            <?php  echo htmlentities(trim($sdeleni),ENT_QUOTES,"UTF-8"); ?>
            <!-- <textarea name="sdeleni" cols="45" rows="10" ></textarea> -->
             &nbsp;
            <input type="hidden"  name="sdeleni" size="30" value="<?php  echo htmlentities(trim($sdeleni),ENT_QUOTES,"UTF-8"); ?>" >
            </FIELDSET>
        </td>

      </tr>

      <tr>
       <td>&nbsp;</td>
      </tr>
    </tbody>
  </table>
</FIELDSET>


  <table border="0px;">
    <tr>
       <td colspan="3">)*&nbsp;&nbsp;&nbsp;Povinný údaj<br>
       </td>
    </tr>
    <tr>
        <td colspan="3"><br>
        Po zkontrolování údajů  klikněte na tlačítko Odeslat. Chcete-li něco změnit, klikněte na tlačítko Opravit.<br>
        <!-- <b>Po odeslání vyplněné přihlášky Vám bude vystavena a zaslána faktura na Vaši e-mailovou adresu.</b><br> <br>
        Kapacita je omezena, přihlášky jsou řazeny dle termínu doručení.--><br>&nbsp;

        </td>
      </tr>

    <tr>
        <!-- <td style="width: 100px;font-weight: bold;">&nbsp;</td>  -->

        <td valign="top">
          <input value="Odeslat" name="B1"  type="submit" class="prihlaska_kurz_tlacitko" <?php if (!$kontrola_OK) {echo 'disabled';}?>>

</form>
        </td>


        <td valign="top">
          <form method="post" action="index.php?list=f0&ci=<?php echo $id_kurz; ?>&idplan=<?php echo $id_planovane_kurzy; ?>">
           <input type="hidden" maxlength="50" name="jmeno" size="30" value="<?php  echo htmlentities(trim($jmeno),ENT_QUOTES,"UTF-8"); ?>">
           <input type="hidden" maxlength="50" name="prijmeni" size="30" value="<?php  echo htmlentities(trim($prijmeni),ENT_QUOTES,"UTF-8"); ?>">
           <input type="hidden" maxlength="50" name="firma" size="30" value="<?php  echo htmlentities(trim($firma),ENT_QUOTES,"UTF-8"); ?>">

           <input type="hidden" maxlength="50" name="ulice" size="30" value="<?php  echo htmlentities(trim($ulice),ENT_QUOTES,"UTF-8"); ?>">
           <input type="hidden" maxlength="50" name="obec" size="30" value="<?php  echo htmlentities(trim($obec),ENT_QUOTES,"UTF-8"); ?>">
           <input type="hidden" maxlength="5" name="psc" size="5" value="<?php  echo htmlentities(trim($psc),ENT_QUOTES,"UTF-8"); ?>">

           <input type="hidden" maxlength="40" name="telefon" size="20"  value="<?php  echo htmlentities(trim($telefon),ENT_QUOTES,"UTF-8"); ?>" >
           <input type="hidden" maxlength="50" name="mail" size="30" value="<?php  echo htmlentities(trim($mail),ENT_QUOTES,"UTF-8"); ?>" >

           <input type="hidden" maxlength="20" name="ic" size="30" value="<?php  echo htmlentities(trim($ic),ENT_QUOTES,"UTF-8"); ?>" >
           <input type="hidden" maxlength="20" name="dic" size="30" value="<?php  echo htmlentities(trim($dic),ENT_QUOTES,"UTF-8"); ?>" >
           <input type="hidden" maxlength="40" name="cislo_uctu" size="30" value="<?php  echo htmlentities(trim($cislo_uctu),ENT_QUOTES,"UTF-8"); ?>" >

           <input type="hidden"  name="sdeleni" size="30" value="<?php  echo htmlentities(trim($sdeleni),ENT_QUOTES,"UTF-8"); ?>" >

           <!-- <input  type="hidden" name="datum" size="30" value="<?php /* echo htmlentities(trim($datum),ENT_QUOTES,"UTF-8");*/ ?>">-->
           <!-- <input  type="hidden" name="misto" size="30" value="<?php /* echo htmlentities(trim($misto),ENT_QUOTES,"UTF-8");*/ ?>">-->
           <!-- <input  type="hidden" name="cena" size="30" value="<?php /* echo htmlentities(trim($cena),ENT_QUOTES,"UTF-8");*/ ?>">-->

           <input type="hidden"  name="zpusob_platby" size="30" value="<?php  echo htmlentities(trim($zpusob_platby),ENT_QUOTES,"UTF-8"); ?>" >

           <!-- <input type="submit" value="Opravit" name="B2" style="font-family: Arial; font-weight: bold;font-size:13px;"> -->
           <input type="submit" value="Opravit" name="B2" class="prihlaska_kurz_tlacitko">

          </form>

    </tr>
  </table>



</p>

<br><br><br>
<!-- <a href='index.php?list=<?php /*echo $ret_list; */?>'>Zpět (na harmionogram otevřených kurzů)</a>  -->

