        <nav id="mySidenav" class="svisle-menu">
            <div class="close-item" onclick="hamburger_close()"><a href="javascript:void(0)"><i class="times circle outline large icon"></i>Zavřít</a></div>
            <?=
                $menuSvisle
            ?>
        </nav>
        <div id="myOverlay" onclick="hamburger_close()"></div>
        <span class="nav-mobile active" onclick="hamburger_open()"><i class="bars large icon"></i>Menu</span>
