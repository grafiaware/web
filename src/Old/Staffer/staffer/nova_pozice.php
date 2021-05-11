<?PHP
use Middleware\Staffer\AppContext;
$handler = AppContext::getDb();

$statement = $handler->query("select jmeno from staffer_kontos");
if ($statement->rowCount()) {
    echo '<h3>Nová pozice</h3>';
echo '<form method="POST" enctype="multipart/form-data" action="?list=nova_pozice2&app=staffer">';
include 'pozice.php';
echo '<input type="submit" value="Založit" name="save"><br><br>';
echo '</form>';
} else {
echo '<p>Nelze založit novou pozici, dokud nebude existovat kontaktní osoba. Založte ji!</p>';
include 'nova_kontos.php';
}
?>
