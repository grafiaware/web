<?php
use Middleware\Rs\AppContext;
$handler = AppContext::getDb();

if ($sess_prava[$stranka]) {
if ($use) {echo '<p class=chyba>Tuto stránku právě upravuje uživatel <b>'.$use.'</b></p><p>Stránku budete moci editovat, až uživatel dokončí úpravy.</p><p><i>Pokud je stránka nedostupná i zjevně po ukončení práce ostatních uživatelů, pak se výše zmíněný uživatel neodhlásil ze systému. Stránka se odblokuje po 60-ti minutách.</i></p>';}
else {
include './data.inc.php';
$db_nazev = 'nazev_'.$lang;
$db_obsah = 'obsah_'.$lang;
$db_aktiv = 'aktiv_'.$lang;
$db_aktivstart = 'aktiv_'.$lang.'start';
$db_aktivstop = 'aktiv_'.$lang.'stop';
$db_keywords = 'keywords_'.$lang;
$statement = $handler->query("SELECT $db_nazev,$db_obsah,$db_aktiv,$db_aktivstart,$db_aktivstop,$db_keywords FROM stranky WHERE list='$stranka'");
$statement->execute();
$zaznam = $statement->fetch(PDO::FETCH_ASSOC);

?>
<H3><?php echo $zaznam[$db_nazev];?></h3> 
<?php 

IF ($lang == 'lan1') {echo '<H4>'.$lan1[1].'</H4>';}
IF ($lang == 'lan2') {echo '<H4>'.$lan2[1].'</H4>';}
IF ($lang == 'lan3') {echo '<H4>'.$lan3[1].'</H4>';}
?>
<form method="POST" action="index.php?list=stranky&language=<?php echo $lang;?>&stranka=<?php echo $stranka;?>" style="width:500px;">
<!-- ZACATEK STAVU STRANKY -->
<fieldset>
<legend>Stav stránky</legend>
<input type="radio" name="aktiv" value="0" <?php IF ($zaznam[$db_aktiv] == 0) {echo 'checked';}?>> <span style="background-color:red; color:white">Nuveřejněná</span><br>
<input type="radio" name="aktiv" value="1" <?php IF ($zaznam[$db_aktiv] == 1) {echo 'checked';}?>> <span style="background-color:green; color:white">Uveřejněná</span><br>
<input type="radio" name="aktiv" value="2" <?php IF ($zaznam[$db_aktiv] == 2) {echo 'checked';}?>> <span style="border: dotted lime 1px">Uveřejnit v období <i>(rok-měsíc-den)</i></span>
<?php include 'contents/kalendar.php';?>
<DIV style="float:right"><input type="submit" value="ULOŽIT STAV"></DIV>
</fieldset>
<!-- KONEC STAVU STRANKY -->
<!-- ZACATEK NAZVU STRANKY -->
<fieldset>
<legend>Název stránky</legend>
<input type="text" name="nazev" size="76" value="<?php echo $zaznam[$db_nazev];?>" maxlength="100">
</fieldset> 
<!-- KONEC NAZVU STRANKY -->
<!-- ZACATEK KEYWORDS STRANKY --> 
<fieldset>
<legend>Klíčová slova - <i>jednotlivá slova oddělujte čárkou</i></legend>
<input type="text" name="keywords" size="76" value="<?php echo $zaznam[$db_keywords];?>" maxlength="500">
</fieldset>
<!-- KONEC ZACATEK KEYWORDS STRANKY -->         
<!-- ZACATEK EDITORU -->
<fieldset>
<legend>Editor obsahu</legend>
<script type="text/javascript">
  _editor_lang = "cz";
  _editor_url = "htmlarea/";
</script>
<!-- load the main HTMLArea files -->
<script type="text/javascript" src="htmlarea/htmlarea.js"></script>

<script type="text/javascript">
// load the plugin files
HTMLArea.loadPlugin("TableOperations");

var editor = null;
function initEditor() {
  // create an editor for the "ed_pole_profi" textbox
  editor = new HTMLArea("ed_pole_profi");

  // register the TableOperations plugin with our editor
  editor.registerPlugin(TableOperations);

  editor.generate();
  return false;
}

function insertHTML() {
  var html = prompt("Enter some HTML code here");
  if (html) {
    editor.insertHTML(html);
  }
}
function highlight() {
  editor.surroundHTML('<span style="background-color: yellow">', '</span>');
}

</script>
<!-- KONEC EDITORU -->
<textarea id="ed_pole1" name="obsah" style="width:450px;" rows="30" cols="60"> 
<?php echo $zaznam[$db_obsah];?>
</textarea>
<script type="text/javascript" src="htmlarea/setting1.js"></script> 

</fieldset>
<p><input type="submit" value="ULOŽIT ZMĚNY"> <input type="submit" value="Importovat soubor" name="newfile"></p>
<input type="hidden" name="stranka" value="<?php echo $stranka;?>">
<input type="hidden" name="edit" value="<?php echo $zaznam['edit'];?>">

<br>
</form>
<?php ;};} else {echo 'Nemáte oprávnění k editaci!';}?>
