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
            <!---->
            <div id="vyberove_menu">
                <div class="close_vyberoveMenu" onclick="vyberoveMenu_close()"><a href="javascript:void(0)"><i class="times circle outline large icon" title="zavřít"></i></a></div>
                <nav class="vodorovne-menu">
                    <?=
                        $menuVodorovne
                    ?>
                </nav>
                <nav class="svisle-menu">
                    <?=
                        $menuSvisle
                    ?>
                </nav>
                <nav class="svisle-menu bloky">
                    <form action="">
                    <?=
                        $bloky ?? ''
                    ?>
                    </form>
                </nav>
            </div> 
            
            <form method="POST" action="">
                <div class="vyber_komponenty">
                    <p>Výběr komponenty</p>
                </div>
            </form>
            
            <div id="prekryti_pro_vyber" onclick="vyberoveMenu_close()"></div>
            <span class="nav_vyberove active" onclick="vyberoveMenu_open()"><i class="bars large icon"></i>Menu</span>
            <!---->
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

