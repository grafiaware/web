<?php
if ($sess_prava['dellist']) {?>
<h4>Průvodce odebráním stránky</h4>
<p>Vyberte menu, ze kterého chcete stránku odebrat.</p>
<form method="GET" action="index.php">
<FIELDSET><LEGEND>Menu</LEGEND>

<?php
if ($zobraz_prvek['hlavni_menu'] ) { ?>
<input name="delstranka" type="radio" value="<?php echo $menu_l;?>">Hlavní menu &nbsp;&nbsp;&nbsp;
<?php }

if ($zobraz_prvek['horni_menu'] ) { ?>
<input name="delstranka" type="radio" value="<?php echo $menu_h;?>">Horní menu &nbsp;&nbsp;&nbsp;
<?php } 

if ($zobraz_prvek['vodo_menu'] ) { ?>
<input name="delstranka" type="radio" value="<?php echo $menu_s;?>">Vodorovné menu &nbsp;&nbsp;&nbsp;
<?php } 

if ($zobraz_prvek['leve_menu'] ) { ?>
<input name="delstranka" type="radio" value="<?php  echo $menu_l;?>">Levé menu &nbsp;&nbsp;&nbsp; 
<?php }

if ($zobraz_prvek['prave_menu'] ) { ?>
<input name="delstranka" type="radio" value="<?php echo $menu_p;?>">Pravé menu
<?php } ?>


</FIELDSET>
<input type="hidden" name="list" value="dellist2">
<br><center><input value="Pokračovat" type="submit"></center>   
</form>

<?php ;} else {echo '<p class=chyba>Nemáte oprávnění k odstranění stránky!</p>';}?>
