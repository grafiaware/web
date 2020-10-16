       <div class="ui container">
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
                        <div class="ui two column stackable centered grid">
                            <div class="eleven wide column">
                                <?=
                                    $content;
                                ?>
                            </div>
                            <div class="four wide column aktuality">
                                <h2>Aktuality</h2>
                                <div class="ui segment">
                                    <div class="ui header">Setkání HR manažerů</div>
                                    <div class="content">
                                        <p>27.6. 2018</p>
                                        <p>Café Papírna Plzeň</p>
                                        <p>Více: ZDE</p>
                                    </div>
                                </div>
                                <div class="ui segment">
                                    <div class="ui header">Připravujeme pro Vás:</div>
                                    <div class="content">
                                        <p>Víkend otevřených ateliérů:</p>
                                        <p>Plzeň tvořivá</p>
                                        <p>29. - 30. 9. 2018</p>
                                        <p>www.otevreneateliery.cz</p>
                                    </div>
                                </div>
                                <div class="ui segment">
                                    <div class="ui header">POVEZ II</div>
                                    <div class="content">
                                        <p>aktuálně opět můžete získat dotaci na školení Vašich zaměstnanců!</p>
                                        <p>Více informací: obchod@grafia.cz</p>
                                        <p>Tel.: 378 227 215</p>
                                    </div>
                                </div>
                                
                                <!-- ?= $aktuality ?-->
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <footer>     
                <div class="ui three column stackable centered grid">
                    <div class="five wide column middle aligned">
                        <?=
                            $rychleOdkazy
                        ?>
                    </div>
                    <div class="four wide column left aligned middle aligned center aligned">
                        <?=
                            $razitko
                        ?>
                            <div class="kontaktni-udaje">
                                <p><b>GRAFIA, s.r.o.</b></p>
                                <p>Budilova 4</p>
                                <p>301 00 Plzeň</p>
                                <p>Tel.:377&nbsp;227&nbsp;701</p>
                                <p>E-mail: <a href="mailto:info@grafia.cz">info@grafia.cz</a></p>
                            </div>
                    </div>
                    <div class="six wide column middle aligned">
                        <a href="https://osm.org/go/0JbQCAEeV-?m=" target="_blank"><img class="mapa" src="<?= Middleware\Web\AppContext::getAppPublicDirectory().'newlayout_3/img/mapa.png'?>" alt="mapa" /></a>

                    </div>
                </div>
            </footer>
        </div>    
           