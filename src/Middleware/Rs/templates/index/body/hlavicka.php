<!--<div id="rs_top">
REDAKČNÍ SYSTÉM
</div>
<div id="rs_log">
< ?php include 'loginfo.php'; ?>
</div>
<DIV ID="rs_nastroje">
< ?php
if ( substr($txt_firma,0,6) == "Grafia" ) {
// include_once \Middleware\Rs\AppContext::getScriptsDirectory().'nastroje.php';
}
?>
</DIV>
<hr class="separator" />-->

<div class="ui stackable grid">
<!--    <div class="row">
        <div class="fifteen wide column center aligned">
            <p>REDAKČNÍ SYSTÉM</p>
        </div>
    </div>-->
    <div class="row"> 
        <?php include 'loginfo.php'; ?>
    </div> 
    <div class="row">
        <div class="fifteen wide column">
            <?php
                if ( substr($txt_firma,0,6) == "Grafia" ) {
                // include_once \Middleware\Rs\AppContext::getScriptsDirectory().'nastroje.php';
                }
            ?>
            <button class="ui small disabled button">Editace WWW stránek</button>
            <button class="ui small button">Volné pracovní pozice</button>
           
        </div>
    </div>
</div> 
