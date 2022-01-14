<div class="ui three column stackable grid equal width">
    <div class="one wide tablet three wide computer column">
        <?= $controlEditMenu ?>
        <?php include "telo/bloky.php"; ?>
        <?php include "telo/svislemenu.php"; ?>
        <?php include "telo/kos.php"; ?>
    </div>
    <div class="column">
        <?=
        $content;
        ?>
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

