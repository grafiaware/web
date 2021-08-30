<?php
use Middleware\Staffer\AppContext;
$handler = AppContext::getDb();

if (isset($_GET['osoba'])) {
    $osoba=$_GET['osoba'];
    $succ = $handler->exec("DELETE FROM staffer_kontos WHERE id='$osoba'");
    include 'prehled_kontos.php';
} else {
    echo'<p class="chyba">Došlo ke ztrátě kontextu osoby!</p>';
    include 'prehled_kontos.php';
}
?>
