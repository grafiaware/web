<h3>Nová kontaktní osoba</h3>
<?PHP
echo '<fieldset><legend>Zadání třídy pro dotazování</legend>';
echo '<form method="POST" enctype="multipart/form-data" action="?list=nova_kontos2&app=staffer">';
echo 'Jméno: <input type="text" name="jmeno" value="'.@$jmeno.'">';
echo '&nbsp;&nbsp;';
echo 'Příjmení: <input type="text" name="prijmeni" value="'.@$prijmeni.'">';
echo '<br><br>';
echo 'E-mail: <input type="text" name="mail" value="'.@$mail.'">';
echo '&nbsp;&nbsp;';
echo 'Telefon: <input type="text" name="tel" value="'.@$tel.'">';
echo '&nbsp;&nbsp;';
echo 'Fax: <input type="text" name="fax" value="'.@$fax.'">';
echo '<br><br>';
echo '<input type="submit" value="Založit" name="save">';
echo '</fieldset><br>';
?>
