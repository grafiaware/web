                <div class="two wide column editMenu">
                    <div class="fix-bar">
                        <?php include "telo/prihlaseni.php"; ?>
                        <?php include "telo/svislemenu.php"; ?>
                    </div>
                </div>

                <div class="thirteen wide mobile eight wide tablet nine wide computer ten wide large screen eleven wide widescreen column editMenu-article">
                    <header>
                        <?php include "telo/hlavicka.php"; ?>
                    </header>
                    <main class="page-content">
                        <?= $content ?? '' ?>
                    </main>
                </div>