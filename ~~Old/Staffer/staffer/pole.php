<?php
use Middleware\Staffer\AppContext;
$handler = AppContext::getDb();

$pole = array();
$statement = $handler->query("select pole,nazev,fce from staffer_forms where $form=1");
$statement->execute();
foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $zaznam) {$pole[$zaznam['pole']]=array();
    $pole[$zaznam['pole']][0]=$zaznam['nazev'];
    if (isset($_POST[$zaznam['pole']])) {
        $pole[$zaznam['pole']][1]= $_POST[$zaznam['pole']];
    } else {
        $pole[$zaznam['pole']][1]='';
    }
    $pole[$zaznam['pole']][2]=0;
    $pole[$zaznam['pole']][3]=$zaznam['fce'];
}
?>
