<?PHP
use Middleware\Staffer\AppContext;
$handler = AppContext::getDb();

if (isset($_GET['osoba'])) {$osoba=$_GET['osoba'];}
$statement = $handler->query("SELECT * FROM staffer_kontos WHERE id='$osoba'");
$statement->execute();
$zaznam = $statement->fetch(PDO::FETCH_ASSOC);
echo '<h3>Úprava kontaktní osoby: '.$zaznam['jmeno'].' '.$zaznam['prijmeni'].'</h3>';

echo '<fieldset><br>';
echo '<form method="POST" enctype="multipart/form-data" action="?list=uprava_kontos2&app=staffer">';
echo 'Jméno: <input type="text" name="jmeno" value="'.@$zaznam['jmeno'].'">';
echo '&nbsp;&nbsp;';
echo 'Příjmení: <input type="text" name="prijmeni" value="'.@$zaznam['prijmeni'].'">';
echo '<br><br>';
echo 'E-mail: <input type="text" name="mail" value="'.@$zaznam['mail'].'">';
echo '&nbsp;&nbsp;';
echo 'Telefon: <input type="text" name="tel" value="'.@$zaznam['tel'].'">';
echo '&nbsp;&nbsp;';
echo 'Fax: <input type="text" name="fax" value="'.@$zaznam['fax'].'">';
echo '<br><br>';
echo '<input type="hidden" name="osoba" value="'.$osoba.'">';
echo '<input type="submit" value="Uložit" name="save">';
echo '</fieldset><br>';
?>
