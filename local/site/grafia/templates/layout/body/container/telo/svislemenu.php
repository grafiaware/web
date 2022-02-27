        <div id="mySidenav">
            <div class="close-item" onclick="hamburger_close()"><a href="javascript:void(0)"><i class="times circle outline large icon"></i>Zavřít</a></div>

            <nav class="svisle-menu bloky">
                <?= $bloky ?? ''?>
            </nav>
            <nav class="svisle-menu">
                <?= $menuSvisle ?>
            </nav>
            <nav class="svisle-menu kos">
                <?= $kos ?? '' ?>
            </nav>

            <?=
            $rychleOdkazy
            ?>
        </div>
        <div id="myOverlay" onclick="hamburger_close()"></div>
        <span class="nav-mobile active" onclick="hamburger_open()"><i class="bars large icon"></i>Menu</span>
