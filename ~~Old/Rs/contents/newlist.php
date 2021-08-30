<?php
//echo "<br>*sess prava v new_list:* "  ; print_r($sess_prava);

if ($sess_prava['newlist']) {
?>
<h4>Přidání nové stránky</h4>
<p>Vyberte menu, do kterého chcete stránku přidat.</p>
<form method="GET" action="index.php">
<FIELDSET><LEGEND>Menu</LEGEND>

<?php if ($zobraz_prvek['hlavni_menu'] )    { ?>
  <input name="newstranka" type="radio" value="<?php echo $menu_l;?>">Hlavní menu <?php } ?>
<?php if ($zobraz_prvek['horni_menu'] )     { ?>  
  <input name="newstranka" type="radio" value="<?php echo $menu_h;?>">Horní menu  <?php } ?>
<?php if ($zobraz_prvek['vodo_menu'] )     { ?>  
  <input name="newstranka" type="radio" value="<?php echo $menu_s;?>">Vodorovné menu <?php } ?>
  
<?php if ($zobraz_prvek['leve_menu'] )     { ?>
  <input name="newstranka" type="radio" value="<?php echo $menu_l; ?>">Levé menu <?php } ?>
<?php if ($zobraz_prvek['prave_menu'] )     { ?>  
  <input name="newstranka" type="radio" value="<?php echo $menu_p; ?>">Pravé menu  <?php } ?>

</FIELDSET>


<input type="hidden" name="list" value="newlist2">
<br><center><input value="Pokračovat" type="submit"></center>
</form>

<?php ;}
else {echo '<p class=chyba><br>Nemáte oprávnění k tomuto úkonu.</p>';}?>
