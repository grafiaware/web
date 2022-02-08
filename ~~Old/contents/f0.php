<?php
//echo "jsme v f0.";
if (isset($_GET['idplan'])) {$id_planovane_kurzy = $_GET['idplan'];}  //pro hledani v t.planovane_kurzy
if (isset($_GET['ci'])) {$id_kurz = $_GET['ci'];}
//echo  "<br>ci*" . $ci;
//
//if (isset($_GET['kodk'])) {$kod_kurzu = $_GET['kodk'];}     //pro hledani kurzu v db t.kurz, t.popis_kurzu
//if (isset($_GET['rlist'])) {$ret_list = $_GET['rlist'];}     //pro navrat na "vysilajici" stranku
//echo "ret_list" . $ret_list;}

//naplnit z GET datum, misto
//if (isset($_GET['datum'])) {$datum = $_GET['datum'];}  else {$datum = '';}
//if (isset($_GET['misto'])) {$misto = $_GET['misto'];}  else {$misto = '';}
//if (isset($_GET['cena'])) {$cena = $_GET['cena'];}  else {$cena = '';}

//naplneni z $_POST , vracim-li se po neuspesne kontrole Zpet z f0_1
if (isset($_POST['datum'])) {$datum = $_POST['datum'];}
if (isset($_POST['misto'])) {$misto= $_POST['misto'];}
if (isset($_POST['cena'])) {$cena= $_POST['cena'];}

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
//echo "<br>*** " . $_POST['zpusob_platby'];   
   
//   echo "mail***" . $mail  . "***";
//   echo "sdeleni***" . $sdeleni  . "***";

include  'contents/f0.inc.php';
?>


<p> 
<p>Pokud chcete přihlásit skupinu osob, kontaktujte nás, prosím, na
<a href="mailto:kurzy@grafia.cz?subject=Zadost o skupinovou prihlasku na kurz <?php echo $zaznam['kod_kategorie']. 
              " " .$zaznam['kurz_poradove_cislo'] . " ".$zaznam['kurz_Nazev']; ?>">kurzy@grafia.cz</a>,
kde Vám bude obratem zaslána skupinová přihláška.<br><br>
</p>
    
<H2>Přihláška pro jednotlivce</H2>    
    
    
<form method="post" action="index.php?list=f0_1&ci=<?php echo $id_kurz; ?>&idplan=<?php echo $id_planovane_kurzy; ?> ">

        
<FIELDSET> 
<LEGEND><b>Kurz</b></LEGEND>
  <table border="0px">
  <tbody>
    <tr>
        <td class="prihlaska_kurz_popis"> &nbsp;
        </td>    
        <td class="prihlaska_kurz_hodnota" >  <?php echo  $zaznam['kod_kategorie']." " .
                                              $zaznam['kurz_poradove_cislo'] ; ?> 
        </td>
    </tr>
    <tr>
        <td class="prihlaska_kurz_popis">Název kurzu </td>    
        <td>  <?php echo $zaznam['kurz_Nazev'];  ?>  </td>
    </tr>    
    
    <tr>
        <td class="prihlaska_kurz_popis">Datum 
        </td>    
        <td>  <?php echo datum_ymd_dmy2($zaznam_planovane['od_data_plan']) . ' - ' . datum_ymd_dmy2($zaznam_planovane['do_data_plan']) ;  ?>
        </td>
    </tr>    
    <tr>
        <td class="prihlaska_kurz_popis">Místo konání 
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
<FIELDSET> 
<LEGEND><b>Vaše údaje</b></LEGEND>
  
  <table border="0px" >
        <!--style="margin-left: auto; margin-right: auto; text-align: left; width: 350px;font-size:12px"
         border="0" cellpadding="2" cellspacing="2"> -->
    <tbody>
      <tr>
        <td class="prihlaska_kurz_popis">Jméno:</td>
        <td>
            <input maxlength="50" name="jmeno" size="30" value="<?php  echo $jmeno; ?>">
        </td>
        <td>
        </td>
      </tr>
      <tr>
        <td class="prihlaska_kurz_popis">Příjmení: )*</td>
        <td>
            <input maxlength="50" name="prijmeni" size="30" value="<?php  echo $prijmeni; ?>">
        </td>
      </tr>
      <tr>
        <td class="prihlaska_kurz_popis">Firma: </td>
        <td>
            <input maxlength="50" name="firma" size="30" value="<?php  echo $firma; ?>">
        </td>
      </tr>
    
    
      <tr>
        <td class="prihlaska_kurz_popis">Ulice:</td>
        <td>
            <input maxlength="50" name="ulice" size="30" value="<?php  echo $ulice; ?>">
        </td>
        <td>&nbsp;
        </td>
      </tr>
      <tr>
        <td class="prihlaska_kurz_popis">Obec:</td>
        <td>
            <input maxlength="50" name="obec" size="30" value="<?php  echo $obec; ?>">
        </td>
        <td>&nbsp;
        </td>
      </tr>
      <tr>
        <td class="prihlaska_kurz_popis">PSČ:</td>
        <td>
            <input maxlength="5" name="psc" size="5" value="<?php  echo $psc; ?>">
        </td>
        <td>&nbsp;
        </td>
      </tr>  
      <tr>
        <td class="prihlaska_kurz_popis">Telefon (mobil):</td>
        <td>
            <input maxlength="40" name="telefon" size="20"  value="<?php  echo $telefon; ?>" >
        </td>
        <td>&nbsp;
        </td>
      </tr>
      <tr>
        <td class="prihlaska_kurz_popis">E-mail: )*</td>
        <td>
            <input maxlength="50" name="mail" size="30" value="<?php  echo $mail; ?>" >
        </td>
      </tr>
        

      <tr>
        <td class="prihlaska_kurz_popis">IČ:</td>
        <td>
            <input maxlength="20" name="ic" size="30" value="<?php  echo $ic; ?>" >
        </td>
      </tr>
      <tr>
        <td class="prihlaska_kurz_popis">DIČ:</td>
        <td>
            <input maxlength="20" name="dic" size="30" value="<?php  echo $dic; ?>" >
        </td>
      </tr>
      
      <!--  docasne neni  -->
      <tr class="docasne_neni">
        <td colspan="3"><br>
        Abychom Vám mohli správně vystavit fakturu, prosíme, uveďte způsob platby. Budu platit
        </td>
      </tr>
      <tr class="docasne_neni">
        <td colspan="3">
            <input  type="radio" name="zpusob_platby" value="1" <?php if ($zpusob_platby == 1) {echo 'checked';} ?>> 
            převodem na účet z účtu  <input maxlength="40" name="cislo_uctu" size="30" value="<?php  echo $cislo_uctu; ?>" >
        </td>
      </tr>
      <tr class="docasne_neni">
        <td>
            <input  type="radio" name="zpusob_platby" value="2" <?php if ($zpusob_platby == 2) {echo 'checked';} ?>>
            složenkou
        </td>
      </tr>
      <tr class="docasne_neni">
        <td colspan="3">
            <input  type="radio" name="zpusob_platby" value="3" <?php if ($zpusob_platby == 3) {echo 'checked';} ?>>
        v hotovosti (nejpozději 3 dny  před zahájením kurzu v centrále Grafia,s.r.o., Budilova 4, Plzeň)</td>
      </tr>
        
        
        
      <tr>
        <td colspan="3"><br><b>
        Chcete-li nám sdělit nějaké další požadavky, či máte-li otázky, využijte prosím  následující pole...</b><br><br>
        Sem také vypište požadavek na uplatnění <b>slevy</b> (např. druhá a další osoba přihlšená do kurzu, sleva pro stávající zákazníky).
        O poskytovaných slevách se informujte
        <a href="mailto:kurzy@grafia.cz?subject=Chci informace o aktualnich slevach (<?php echo $zaznam['kod_kategorie'].
                      " " . $zaznam['kurz_poradove_cislo'].") ". $zaznam['kurz_Nazev']; ?>">tímto e-mailem</a>.<br>&nbsp;
        
        </td>
      </tr>
        
      <tr>
        <td class="prihlaska_kurz_popis" >Sdělení:</td>
        <td colspan="2">
            <textarea name="sdeleni" cols="45" rows="10"><?php  echo $sdeleni; ?></textarea>
            &nbsp;
        </td>
       
      </tr>  
        
      <tr>
       <td colspan="2">)*&nbsp;&nbsp;&nbsp;Povinný údaj<br>
       <!-- **&nbsp;Nejste-li soukromá osoba, ale firma, vyplňte, prosím, jméno firmy -->
       </td>
      </tr>   
      
      <tr>
        <td colspan="3"><br>
        Po vyplnění a zkontrolování údajů  klikněte na tlačítko Pokračovat.V dalším kroku proběhne kontrola Vašich zadaných údajů.<br> 
        <!-- <b>Po následném odeslání vyplněné přihlášky Vám bude vystavena a zaslána faktura na Vaši e-mailovou adresu.</b><br>--><br> 
        Kapacita je omezena, přihlášky jsou řazeny dle termínu doručení.<br>&nbsp;
        
        </td>
      </tr>
      
      
      
      
      <tr>  
        <td>&nbsp; </td>
        <td>
            <input value="Pokračovat" name="B1" class="prihlaska_kurz_tlacitko" type="submit">
        </td>
  
      </tr>
    </tbody>
  </table>

</FIELDSET>

</form>


</p>


<br><br><br>
<!-- a href='index.php?list=<?php /*echo $ret_list;*/ ?>'>Zpět (na harmionogram otevřených kurzů)</a> -->

