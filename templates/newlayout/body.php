
    <div class="ui grid">
        <div class="fifteen wide column">
            <header>
                <div class="ui three column grid equal width">
                    <div class="column">
                        <?=$logo?>
                    </div>
                    <div class="column">
                        <p>Bavíme, vzděláváme, jsme užiteční.</p>
                        <p> Vyzkoušejte si nás</p>
                        <!--?=$banner?-->
                        <img class="hlavicka" src="public/web/newlayout/img/hlavicka1.png" alt="vzdělávání" />
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
                            <?=$modal?>
                        </div>
                    </div>
                 </div>
                <div id="contents">
                    <div class="articleHeadlined">
                        <?=
                        $content;
                        ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <footer>
    <div class="ui two column grid">
        <div class="seven wide column">
                <!--?=$mapa?-->
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