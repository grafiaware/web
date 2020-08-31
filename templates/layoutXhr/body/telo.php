<div class="ui three column stackable grid equal width">
    <div class="one wide tablet three wide computer column">
        <?php include "telo/svislemenu.php"; ?>
        <?php include "telo/bloky.php"; ?>
        <?php include "telo/kos.php"; ?>
        <div id="component_rychleOdkazy">
            <script>$("#component_rychleOdkazy").load("component/namedpaper/a3");</script>
        </div>
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
                    <?=
                        $bloky ?? ''
                    ?>
                </nav>
            </div>

            <div id="prekryti_pro_vyber" onclick="vyberoveMenu_close()"></div>
            <span class="nav_vyberove active" onclick="vyberoveMenu_open()"><i class="bars large icon"></i>Menu</span>
        <div id="component_presentedpaper">
            <script>$("#component_presentedpaper").load("component/presentedpaper");</script>
        </div>

        </div>
    </div>
    <div class="four wide tablet three wide computer column">
        <div id="component_razitko">
        <script>$("#component_razitko").load("component/namedpaper/a4");</script>
        </div>
        <div id="component_socialnisite">
        <script>$("#component_socialnisite").load("component/namedpaper/a5");</script>
        </div>
        <div id="component_aktuality">
        <script>$("#component_aktuality").load("component/namedpaper/a1");</script>
        </div>
        <div id="component_akce">
        <script>$("#component_akce").load("component/namedpaper/a2");</script>
        </div>
    </div>
</div>

