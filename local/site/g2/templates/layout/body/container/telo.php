<div class="ui three column stackable grid equal width">
    <div class="one wide tablet three wide computer column">
        <?php include "telo/svislemenu.php"; ?>
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

