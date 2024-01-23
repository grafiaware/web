                <div class="fourteen wide mobile fourteen wide tablet eleven wide computer eleven wide large screen eleven wide widescreen column">
                    <header id="header">
                        <?php include "telo/hlavicka.php"; ?>
                        <?php include "telo/prihlaseni.php"; ?>
                    </header>
                    <div class="ui grid">
                        <div class="one wide mobile five wide tablet five wide computer four wide large screen five wide widescreen column">
                            <?php include "telo/svislemenu.php"; ?>
                        </div>
                        <div class="fourteen wide mobile eleven wide tablet eleven wide computer eleven wide large screen eleven wide widescreen column right floated">
                            <main class="page-content">
                                <?= $content ?? '' ?>
                            </main>
                            
                        </div>
                    </div>
                </div>

                <div class="one wide mobile two wide tablet two wide computer two wide large screen two wide widescreen column">
                    <div class="fix-bar no-fix"></div>
                    <img class="klic-img" src="layout-images/klicPrilezitosti2.png" alt="Klíč k příležitostem" height="504" width="212" />
                </div>