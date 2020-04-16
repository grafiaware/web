<?php
echo '<fieldset><legend>Období uveřejnění</legend><br>';
?><!-- ZACATEK STAVU Pozice -->
<input type="radio" name="aktiv" value="0" <?PHP IF (isset($zaznam['aktiv']) AND $zaznam['aktiv'] == 0) {echo 'checked';}?>> <span class="rs_obdobi_neuverejnene">Neuveřejněná</span><br>
<input type="radio" name="aktiv" value="1" <?PHP IF (isset($zaznam['aktiv']) AND $zaznam['aktiv'] == 1) {echo 'checked';}?>> <span class="rs_obdobi_uverejnene">Uveřejněná</span><br>
<input type="radio" name="aktiv" value="2" <?PHP IF (isset($zaznam['aktiv']) AND $zaznam['aktiv'] == 2) {echo 'checked';}?>> <span class="rs_obdobi_oddo">Uveřejnit v období <i>(rok-měsíc-den)</i></span>
<?PHP include \Middleware\Staffer\AppContext::getScriptsDirectory().'contents/kalendar.php';?>

<DIV class="rs_float_r" ><input type="submit" value="ULOŽIT STAV"></DIV>
<!-- KONEC STAVU Pozice -->
<?PHP
echo '</fieldset><br>';

echo '<fieldset><legend>Údaje o pozici</legend><br>';
?>
<!-- tinyMCE  jscripts/tiny_mce/tiny_mce.js -->
<script language="javascript" type="text/javascript" src="<?php echo \Middleware\Staffer\AppContext::getPublicDirectory() ?>tinymce/tinymce.min.js"></script>
<script language="javascript" type="text/javascript">


	tinyMCE.init ({
            selector:  'textarea',
            language : 'cs',
            plugins : [
                       "advlist lists  charmap code insertdatetime contextmenu textcolor",
                       "searchreplace wordcount visualblocks visualchars nonbreaking",
                       "paste  save  autosave link ",
                       ],

            toolbar1 : "undo redo | bold italic | forecolor backcolor | alignleft alignright aligncenter alignfull | bullist numlist |" +
                       " searchreplace visualchars nonbreaking charmap insertdatetime",
            textcolor_map: [
    "000000","Black",
    "808080","Gray",
    "999999","Medium gray",
    "FFFFFF","White",
    "EB1B4C",'Grafia-červená',
    "000080",'Grafia-modrá' ],

            content_css : "<?php echo \Middleware\Staffer\AppContext::getAppPublicDirectory() ?>grafia/css/editor.css",
            nonbreaking_force_tab : true,
	});

//alert(' jdu pres tinymce ve staffer pozice.php ...!')
</script>



<!-- /tinyMCE -->




    <?PHP

echo 'Název pozice: <input type="text" name="nazev" value="'.@$nazev.'" size=70>';
echo '<br><br>';
echo 'Typ dotazníku pro uchazeče: <select name="typ" size="1">';

//$vysledek2 = MySQL_Query("SHOW COLUMNS FROM staffer_forms");
//WHILE ($zaznam2 = MySQL_Fetch_Array($vysledek2)) {
/* @var $handler \PDO */
$statement = $handler->query("SHOW COLUMNS FROM staffer_forms");
$statement->execute();
foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $zaznam2) {
    if (substr ($zaznam2['Field'],0,3) == 'dot') {?>
<option value="<?PHP echo $zaznam2['Field'];?>" <?PHP if (@$typ == @$zaznam2['Field']) {echo'selected';} ?>>

    <?PHP
    if (substr ($zaznam2['Field'],3,1) == '1') { echo "dotazník bez povinného životopisu";}
    if (substr ($zaznam2['Field'],3,1) == '2') { echo "dotazník s povinným životopisem";}
    // echo $zaznam2['Field'];
    ?>
 </option>
<?PHP
}
}
echo '</select> &nbsp;&nbsp;';
    //<b>dot1</b> - nižší pozice &nbsp;&nbsp; <b>dot2</b> - vyšší pozice s životopisem';
?>

<br><br>
 <!-- <img src="app/staffer/img/ikoclo16.gif" title="pozice_s_odmenou">
 <input type="checkbox" name="pozice_s_odmenou" value=1  -->
    <?PHP
    //if ( $pozice_s_odmenou == 1 ) {echo 'checked >&nbsp;&nbsp;';}
    //else {echo '>&nbsp;&nbsp;';}
    ?>

 <!--Pozice s odměnou za zprostředkování        -->


<!-- tivs -->
<!-- <script language="javascript" type="text/javascript">
                 html_area_id="pozadavky";
</script> -->

<?PHP
echo '<br><br>Nabízíme:<br><textarea id="elm1" name="nabizime" cols="89" rows="13">';
echo @$nabizime.'</textarea>';
echo '<br>';

echo 'Požadavky:<br><textarea id="elm2" name="pozadavky" cols="89" rows="13">';
echo @$pozadavky.'</textarea>';
echo '<br>';

echo 'Popis práce:<br><textarea id="elm3" name="popis" cols="89" rows="13">';
echo @$popis.'</textarea>';
echo '<br>';

echo 'Nástup: <input type="text" name="nastup" value="'.@$nastup.'">';
echo '<br><br>';

echo 'Směnný rozpis: <input type="text" name="model" value="'.@$model.'" size=60> <i>Vložte odkaz na stránku se směnným rozpisem.<br>např.: http://www.grafia.cz?list=s07_01&lang=lan1</i>';
echo '<br>';
echo '</fieldset><br>';
echo '<fieldset><legend>Kontaktní osoba pro tuto pozici</legend><br>';
echo 'Kontaktní osoby: <select name="kontakt_id" size="1">';
//$vysledek2 = MySQL_Query("SELECT id,jmeno,prijmeni FROM staffer_kontos ORDER BY prijmeni");
//WHILE ($zaznam2 = MySQL_Fetch_Array($vysledek2)) {
$statement2 = $handler->query("SELECT id,jmeno,prijmeni FROM staffer_kontos ORDER BY prijmeni");
$statement2->execute;
foreach ($statement2->fetchAll(PDO::FETCH_ASSOC) as $zaznam2) {
?>
<option value="<?PHP echo $zaznam2['id'];?>" <?PHP if (@$zaznam2['id'] == @$kontakt_id) {echo 'selected';}?>><?PHP echo $zaznam2['jmeno'].' '.$zaznam2['prijmeni'];?></option>
<?PHP }
echo '</select><br><br></fieldset><br>';
?>
