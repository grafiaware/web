<?php
use Site\ConfigurationCache;
?>
        <!-- #mySidenav s třídou .open se menu neskryje při kliknutí mimo oblast menu-->
        <!-- #mySidenav s třídou .editMenu vznikne nescrollovatelné svislé menu; k rodiči tohoto elementu - <div class="fix-bar"> se ještě musí přidat class .no-fix (než bude podpora :has())-->
        <div id="mySidenav"> 
            <div class="close-item">
                <div class="hamburger-icon"><i class="bars big icon"></i><p>Menu</p></div>
                <a href=""><img src="layout-images/klic_na_hlavicku_bily.png" width="" height="50" /></a>
                <a onclick="hamburger_close()" href="javascript:void(0)"><i class="close slim-icon"></i></a>

                <?php include "svislemenu/ovladaci-prvky-menu.php"; ?>

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
