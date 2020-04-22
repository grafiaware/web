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
                                    <?=$modal?>
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
                        <iframe style="border:none" src="https://frame.mapy.cz/s/gusojobeco" width="1000" height="250" frameborder="0"></iframe>

                    </div>
                </div>
            </footer>
        </div>
    </div>    
    