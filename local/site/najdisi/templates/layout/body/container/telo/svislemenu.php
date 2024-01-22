<?php
use Site\ConfigurationCache;
?>
        <!-- #mySidenav s třídou .open se menu neskryje při kliknutí mimo oblast menu-->
        <!-- #mySidenav s třídou .editMenu vznikne nescrollovatelné svislé menu; k rodiči tohoto elementu - <div class="fix-bar"> se ještě musí přidat class .no-fix (než bude podpora :has())-->
        <div id="mySidenav" class="editMenu"> 
            <div class="close-item" onclick="hamburger_close()"><a href="javascript:void(0)"><i class="times circle outline large icon"></i>Zavřít</a></div>
            
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