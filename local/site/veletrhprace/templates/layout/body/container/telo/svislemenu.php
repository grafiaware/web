<?php
use Site\Configuration;
?>
        <div id="mySidenav">
            <div class="close-item">
                <div class="hamburger-icon"><i class="bars big icon"></i><p>Menu</p></div>
                <a href=""><img src="layout-images/klic_na_hlavicku_bily.png" width="" height="50" /></a>
                <a onclick="hamburger_close()" href="javascript:void(0)"><i class="close slim-icon"></i></a>
                <form class="ui form" method="POST" action="">
                    <button class="fluid ui labeled icon large button" type="submit" name="edit_menu" value="<?= empty($editMenu) ? 1 : 0 ?>" formtarget="_self"
                            formaction="red/v1/presentation/edit_menu">
                        <i class="edit icon"></i>
                        <?= empty($editMenu) ? "Zapnout editaci menu" : "Vypnout editaci menu" ?>
                    </button>
                </form>
                <?php empty($editMenu) ? include "ovladaci-prvky-menu.php" : "" ?>
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
