    <div class="ui stackable centered grid">
        <div class="fifteen wide column">
            <header>
                <div class="ui three column stackable centered grid equal width">
                    <div class="column">
                        <?=$logo?>
                    </div>
                    <div class="column">
                        <p>Bavíme, vzděláváme, jsme užiteční.</p>
                        <p> Vyzkoušejte si nás</p>
                        <!--?=$banner?-->
                    </div>
                    <div class="two wide column">
                        <div class="jazyky">
                            <?=$languageSelect?>
                        </div>
                    </div>
                </div>
            </header>

        </div>
    </div>    
    <div class="ui container">
        <div class="column">
            <main class="page-content">
                <?=$poznamky ?? '' ?>
                <nav id="mySidenav" class="svisle-menu">
                    <form action="">
                    <div class="close-item" onclick="hamburger_close()"><a href="javascript:void(0)"><i class="times circle outline large icon"></i>Zavřít</a></div>
                    <?=
                        $menuSvisle
                    ?>
                    </form>
                </nav>
                <div id="myOverlay" onclick="hamburger_close()"></div>
                <div class="ui secondary  menu">
                    <div class="item">
                        <span class="nav-mobile active" onclick="hamburger_open()"><i class="bars large icon"></i>Menu</span>
                    </div>
                    <div class="right menu">
                        <div class="item">
                            <?=$searchPhrase?>
                        </div>
                        <div class="item">
                            <div class="prihlaseni">
                                <?=
                                    $modalLoginLogout
                                ?>
                                <?=
                                    $modalUserAction
                                ?>
                            </div>
                        </div>
                    </div>
                 </div>
                <div id="contents">
                    <div class="articleHeadlined">
                        <div class="ui two column grid stackable centered">
                            <div class="eleven wide column">
                                <?=
                                    $content;
                                ?>
                            </div>
                            <div class="four wide column">
                                <?= $aktuality ?>
                            </div>
                        </div>

                    </div>
                </div>
            </main>
        </div>
    </div>    
    <footer>     
        <div class="ui four column grid stackable centered">
            <div class="three wide column">
                <?=
                    $rychleOdkazy
                ?>
            </div>
            <div class="two wide column bottom aligned">
                <?=
                    $socialniSite
                ?>

            </div>
            <div class="two wide column left aligned bottom aligned">
                    <?= $razitko ?>
                    <div class="kontaktni-udaje">
                        <p><b>GRAFIA, s.r.o.</b></p>
                        <p>Budilova 4</p>
                        <p>301 00 Plzeň</p>
                        <p>Tel.:377&nbsp;227&nbsp;701</p>
                        <p>E-mail: <a href="mailto:info@grafia.cz">info@grafia.cz</a></p>
                    </div>
            </div>
            <div class="eight wide column">
                <div id="mapa">
                    <script>
                        var stred = SMap.Coords.fromWGS84(13.3704967, 49.7463531);
                        var mapa = new SMap(JAK.gel("mapa"), stred, 17);
                        mapa.addDefaultLayer(SMap.DEF_BASE).enable();
                        mapa.addDefaultControls();	  

                        var layer = new SMap.Layer.Marker();
                        mapa.addLayer(layer);
                        layer.enable();

                        var options = {};
                        var marker = new SMap.Marker(stred, "myMarker", options);
                        layer.addMarker(marker);
                    </script>
                </div>
            </div>
        </div>
    </footer>
