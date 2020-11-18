        <nav id="mySidenav" class="svisle-menu">
            <div class="close-item">
                <span class="hamburger-icon"><i class="bars big icon"></i><p>Menu</p></span>
                <a href="index.php"><img src="images/LogoCtyrlistekInvert.png" width="65" height="65" /></a>
                <a onclick="hamburger_close()" href="javascript:void(0)"><i class="close slim-icon"></i></a>
            </div>
            <?=
                $menuSvisle ?? '';
            ?>
        </nav>
        <div id="myOverlay" onclick="hamburger_close()"></div>
        <div class="nav-mobile active" onclick="hamburger_open()"><span><i class="bars big icon"></i><p>Menu</p></span></div>
<!--        <nav class="svisle-menu kos">
            <?=
                $kos ?? '';
            ?>
        </nav>
        <nav class="svisle-menu bloky">
            <?=
                $bloky ?? '';
            ?>
        </nav>-->
