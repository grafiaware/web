<?php
if ($sess_prava['chpass']) {
?>
<h4>Změna hesla</h4>
<form method="POST" action="index.php?list=chpass2">
<FIELDSET><LEGEND>Nové heslo</LEGEND>
<input name="newpass1" type="password" value="" maxlength="50">
</FIELDSET>
<FIELDSET><LEGEND>Potvrzení nového hesla</LEGEND>
<input name="newpass2" type="password" value="" maxlength="50">
</FIELDSET>
<br><center><input value="Změnit heslo" type="submit"></center>
</form>

<?php ;}  else {echo '<p class=chyba><br>Nemáte oprávnění k tomuto úkonu. </p>';}?>
