<?php
use Site\Configuration;
?>
        <div id="mySidenav">
            <div class="close-item">
                <div class="hamburger-icon"><i class="bars big icon"></i><p>Menu</p></div>
                <a href=""><img src="layout-images/klic_na_hlavicku_bily.png" width="" height="50" /></a>
                <a onclick="hamburger_close()" href="javascript:void(0)"><i class="close slim-icon"></i></a>
                <?= $controlEditMenu ?>

                <!-- vložit při editování menu "ovladaci-prvky-menu.php"-->

            </div>
            <nav class="svisle-menu">
                <?= $menuSvisle ?? ''; ?>
            </nav>
            <nav class="svisle-menu kos">
                <?= $kos ?? ''; ?>
            </nav>
            <nav class="svisle-menu bloky">
                <?= $bloky ?? ''; ?>
            </nav>
        </div>
        <div id="myOverlay" onclick="hamburger_close()"></div>
        <div class="nav-mobile active" onclick="hamburger_open()"><div><i class="bars big icon"></i><p>Menu</p></div></div>
