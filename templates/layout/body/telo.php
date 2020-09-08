<div class="ui three column stackable grid equal width">
    <div class="one wide tablet three wide computer column">
        <?php include "telo/svislemenu.php"; ?>
        <?php include "telo/bloky.php"; ?>
        <?php include "telo/kos.php"; ?>
        <?=
            $rychleOdkazy
        ?>
    </div>
    <div id="contents" class="column">
        <div class="articleHeadlined">
            
            <?=
            $content;
            ?>
            
        </div>
    </div>
    <div class="four wide tablet three wide computer column">
        <?=
            $razitko
            .
            $socialniSite
            .
            $aktuality
            .
            $nejblizsiAkce
        ?>
    </div>
</div>

