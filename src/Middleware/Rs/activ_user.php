<?php
$user=$sess_prava['user'];
use Middleware\Rs\AppContext;
$handler = AppContext::getDb();
$successUpdate = $handler->exec("UPDATE activ_user SET stranka = 'null' WHERE akce < DATE_SUB(NOW(),INTERVAL 1 HOUR)");

if ($list == 'rl' OR $list == 'stranky' or $list == 'stranky_publ' OR $list == 'dellist3') {
    if ($list == 'dellist3') {
        $stranka = $_GET['delstranka'];
    }
    $statement = $handler->query("SELECT user FROM activ_user WHERE (stranka='$stranka' AND user<>'$user')");
    $statement->execute();
    $n = $statement->rowCount();
    IF ($n == 0) {
        $successUpdate = $handler->exec("UPDATE activ_user SET stranka = '$stranka',akce = NOW() WHERE user = '$user'");
        $use=0;
    } else {
        $zaznam = $statement->fetch(PDO::FETCH_ASSOC);
        $use=$zaznam['user'];
        $successUpdate = $handler->exec("UPDATE activ_user SET stranka = 'null' WHERE user = '$user'");
    }
} else {
    $successUpdate = $handler->exec("UPDATE activ_user SET stranka = 'null' WHERE user = '$user'");
}

