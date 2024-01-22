                <div class="thirteen wide mobile eight wide tablet nine wide computer ten wide large screen eleven wide widescreen column right floated">
                    <header>
                        <?php include "telo/hlavicka.php"; ?>
                        <?php include "telo/prihlaseni.php"; ?>
                    </header>
                    <div class="ui grid">
                        <div class="two wide column">
                            <?php include "telo/svislemenu.php"; ?>
                        </div>
                        <div class="thirteen wide mobile eight wide tablet nine wide computer ten wide large screen eleven wide widescreen column right floated">
                            <main class="page-content">
                                <?= $content ?? '' ?>
                            </main>
                            
                        </div>
                    </div>
                </div>

                <div class="two wide column">
                    <div class="fix-bar no-fix"></div>
                    <img class="klic-img" src="layout-images/klicPrilezitosti2.png" alt="Klíč k příležitostem" height="" width="" />
                </div>