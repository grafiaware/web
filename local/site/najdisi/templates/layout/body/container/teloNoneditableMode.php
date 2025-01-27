                <div class="fourteen wide computer thirteen wide large screen column">
                    <header id="header">
                        <?php include "telo/hlavicka.php"; ?>
                        <?php include "telo/prihlaseni.php"; ?>
                    </header>
                    <div class="ui grid equal width stackable ">
                        <div class="one wide mobile five wide tablet four wide computer column">
                            <?php include "telo/svislemenu.php"; ?>
                            <div class="blok-menu-paticka">
                                <p class="text velky tucne primarni-barva">Organizátor veletrhu</p>
                                <a href="https://www.grafia.cz/" class="" target="_blank"><img class="" src="layout-images/Grafia_logo.jpg" width="" height="" alt="Logo Grafia, organizátor"/></a>
                            </div>
                            <div class="blok-menu-paticka">
                                <p class="text velky tucne primarni-barva">Podporovatel veletrhu</p>
                                <p><a href="https://www.sew-eurodrive.cz/domu.html" class="" target="_blank"><img class="" src="layout-images/SEW_logo.png" width="" height="" alt="Logo Sew, partner"/></a></p>
                                <p class="text okraje-vertical"><a href="https://www.manpower.cz/" class="" target="_blank"><img class="" src="layout-images/Manpower_logo.png" width="" height="" alt="Logo Manpower, partner"/></a></p>
                            </div>
                        </div>
                        <div class="column">
                            <main class="page-content">
                                <?= $content ?? '' ?>
                            </main>
                            
                        </div>
                    </div>
                </div>

                <div class="one wide mobile two wide tablet one wide computer column">
                    <div class="fix-bar no-fix"></div>
                    <img class="klic-img" src="layout-images/klicPrilezitosti2.png" alt="Klíč k příležitostem" height="504" width="212" />
                </div>