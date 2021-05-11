<h3>Kontaktní osoby</H3>
<?php
use Middleware\Staffer\AppContext;
$handler = AppContext::getDb();

$statement = $handler->query("select * from staffer_kontos");
$statement->execute();
if (!$statement->rowCount()) {
    echo '<p>Není založena žádná kontaktní osoba.</p>';
}
foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $zaznam) {
    echo '<fieldset><legend>'.$zaznam['jmeno'].' '.$zaznam['prijmeni'].'</legend>';
        echo '<DIV id="rs_staf_prehled_kontos">E-mail: <a href="mailto:'.$zaznam['mail'].'">'.$zaznam['mail'].'</a></DIV>';
        echo '<DIV id="rs_staf_prehled_kontos">Tel: '.$zaznam['tel'].'</DIV>';
        echo '<DIV id="rs_staf_prehled_kontos">Fax: '.$zaznam['fax'].'</DIV>';
        // Width na auto upravil Lukáš
        echo '<DIV class="rs_float_r">
        <a href="?list=uprava_kontos&osoba='.$zaznam['id'].'&app=staffer">
        <img src="', AppContext::getAppPublicDirectory().'img/upravit.png" alt="Upravit" title="Upravit">&nbsp;Upravit</a>&nbsp;&nbsp;
        <a href="?list=smazat_kontos&osoba='.$zaznam['id'].'&app=staffer">
        <img src="', AppContext::getAppPublicDirectory().'img/smazat.png" alt="Smazat" title="Smazat">&nbsp;Smazat</a>
        </DIV>';
    echo '</fieldset>';
}
echo '<br>';
?>
