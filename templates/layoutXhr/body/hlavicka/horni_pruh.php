    <!-- LOGO -->
    <div id="component_logo" class="fifteen wide mobile three wide tablet three wide computer column bottom aligned center aligned">
        <script>$("#component_logo").load("component/namedpaper/a7");</script>
    </div>
    <!-- BANNER -->
    <div class="fifteen wide mobile eight wide tablet nine wide computer column bottom aligned center aligned">
        <nav class="presmerovani-menu">
            <?=
                $menuPresmerovani
            ?>
        </nav>
        <div id="component_banner">
            <script>$("#component_banner").load("component/namedpaper/a8");</script>
        </div>
    </div>
    <!-- MAPA -->
    <div class="fifteen wide mobile four wide tablet three wide computer column bottom aligned">
        <div id="component_mapa" class="kontakt-hlavicka">
            <script>$("#component_mapa").load("component/namedpaper/a6");</script>
            <div class="kontaktni-udaje">
                <p><b>GRAFIA, s.r.o.</b></p>
                <p>Budilova 4</p>
                <p>301 00 Plzeň</p>
                <p>Tel.:377&nbsp;227&nbsp;701</p>
                <p>E-mail: <a href="mailto:info@grafia.cz">info@grafia.cz</a></p>
            </div>
        </div>
        <div class="jazyky">
            <?=
                $languageSelect
            ?>
        </div>
        <div class="prihlaseni">
        <?=
            $modalLoginLogout
        ?>
        <?=
            $modalUserAction
        ?>
        </div>
    </div>
