<?php
//$obsah - vstupni promenna ve ktere se vzhledava retezec-nazev souboru,
//pokud neni nalezen, soubor je automaticky odstranen.
// soubor funguje jen pokud je pripojena databaze
use Middleware\Rs\AppContext;
$handler = AppContext::getDb();
$statement = $handler->query("SELECT lan,IDfile,pripona FROM soubory WHERE list='$stranka'");
$statement->execute();
//while ($zaznam = MySQL_Fetch_Array($vysledek)) {
$zaznamy = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($zaznamy as $zaznam) {
                $IDfile = $zaznam['IDfile'];
                IF ($lang == $zaznam['lan']) {
                $cetnost = substr_count ($obsah, $IDfile);

                IF ($cetnost == 0) {$pripona = $zaznam['pripona'];
                           // $cesta = '../files/'.$IDfile.'.'.$pripona;
                           // $cestars = 'files/'.$IDfile.'.'.$pripona;
                            $successDelete = $handler->exec("DELETE FROM soubory WHERE IDfile = '$IDfile'");
                            //unlink ($cesta);
                            //unlink ($cestars);

                            $cesta_bezsouboru = AppContext::getAppPublicDirectory().'files/';
                            $cesta_rs_bezsouboru = AppContext::getAppPublicDirectory().'files/';
                            $s = 'deleted_' . date("Ymd_His") .  '_' . $IDfile . '.' .$pripona;
                            rename( $cesta_bezsouboru . $IDfile.'.'.$pripona , $cesta_bezsouboru . $s  );
                            rename( $cesta_rs_bezsouboru . $IDfile.'.'.$pripona , $cesta_rs_bezsouboru . $s  );

                }
                }
}

?>
