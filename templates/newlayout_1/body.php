    <div class="ui grid stackable centered">
        <div class="fifteen wide column">
            <header>
                        <div class="ui secondary  menu">
                            <div class="item logo">
                                <?=$logo?>
                            </div>
                            <div class="right menu">
                                <div class="item">
                                    <div class="jazyky">
                                        <?=$languageSelect?>
                                    </div>
                                </div>
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
                        <p>Bavíme, vzděláváme, jsme užiteční.</p>
                        <p> Vyzkoušejte si nás</p>
                <!--?= $banner ?-->
                <div class="background"></div>
            </header>
        </div>
        <div class="twelve wide column">
            <main class="page-content">
                <div class="ui two column grid">
                    <div class="three wide column">
                                                        <!--?= $aktualita11 ?-->

                        <nav id="mySidenav" class="svisle-menu">
                            <form action="">
                            <div class="close-item" onclick="hamburger_close()"><a href="javascript:void(0)"><i class="times circle outline large icon"></i>Zavřít</a></div>
                            <?=
                                $menuSvisle
                            ?>
                            </form>
                        </nav>
                        <div id="myOverlay" onclick="hamburger_close()"></div>
                        <span class="nav-mobile active" onclick="hamburger_open()"><i class="bars large icon"></i>Menu</span>
                    </div>
                    <div class="twelve wide column">
                        <div id="contents">
                            <div class="articleHeadlined">
                                <?=
                                    $content;
                                ?>

                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div class="fifteen wide column">
            <footer>       
                <div class="background-f"></div>
                <div class="ui four column grid">
                    <div class="three wide column">
                        <?=
                            $rychleOdkazy
                        ?>
                    </div>
                    <div class="two wide column bottom aligned">
                        <?=
                            $razitko
                            .
                            $socialniSite
                        ?>

                    </div>
                    <div class="two wide column left aligned bottom aligned">
                            <div class="kontaktni-udaje">
                                <p><b>GRAFIA, s.r.o.</b></p>
                                <p>Budilova 4</p>
                                <p>301 00 Plzeň</p>
                                <p>Tel.:377&nbsp;227&nbsp;701</p>
                                <p>E-mail: <a href="mailto:info@grafia.cz">info@grafia.cz</a></p>
                            </div>
                    </div>
                    <div class="eight wide column">
                        <iframe width="600" height="400" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.openstreetmap.org/export/embed.html?bbox=13.365973234176638%2C49.744568057781755%2C13.374931812286377%2C49.74814185389686&amp;layer=mapnik&amp;marker=49.74635325557977%2C13.370452523231506" style="border: 1px solid black"></iframe>
                        <br/><small><a href="https://www.openstreetmap.org/?mlat=49.74635&amp;mlon=13.37045#map=18/49.74635/13.37045">Zobrazit větší mapu</a></small>

                    </div>
                </div>
            </footer>
        </div>
    </div>    
    