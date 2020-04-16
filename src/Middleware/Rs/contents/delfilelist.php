<?php
use Middleware\Rs\AppContext;
$handler = AppContext::getDb();
$statement = $handler->query("SELECT IDfile,pripona FROM `soubory` WHERE CONVERT(`list` USING utf8)='$dellist'");
$statement->execute();

//while ($zaznam = MySQL_Fetch_Array($vysledek)) {
$zaznamy = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($zaznamy as $zaznan) {
                                                $IDfile = $zaznam['IDfile'];
                                                $pripona = $zaznam['pripona'];
                                                //$cesta = '../files/'.$IDfile.'.'.$pripona;
                                                //$cesta_rs = 'files/'.$IDfile.'.'.$pripona;
                                                $successDelete = $handler->exec("DELETE FROM `soubory` WHERE IDfile = '$IDfile'");
                                                //  unlink ($cesta);
                                                //  unlink ($cesta_rs);                                                                                                                                                                 

                                                $cesta_bezsouboru = \Middleware\Web\AppContext::getAppPublicDirectory().'files/';
                                                $cesta_rs_bezsouboru = \Middleware\Rs\AppContext::getAppPublicDirectory().'files/';
                                                $s = 'deleted_' . date("Ymd_His") .  '_' . $IDfile . '.' .$pripona;
                                                rename( $cesta_bezsouboru . $IDfile.'.'.$pripona , $cesta_bezsouboru . $s  );
                                                rename( $cesta_rs_bezsouboru . $IDfile.'.'.$pripona , $cesta_rs_bezsouboru . $s  );

                                                echo $IDfile.' - Smazáno<br>';
                                                }
?>
