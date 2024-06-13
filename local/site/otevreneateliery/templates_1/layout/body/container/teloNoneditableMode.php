                <div class="two wide column">
                    <div class="fix-bar">
                        <?php include "telo/svislemenu.php"; ?>
                        <?php include "telo/prihlaseni.php"; ?>
                        <?php include "telo/iconmenu.php"; ?>
                    </div>
                </div>

                <div class="thirteen wide mobile twelve wide tablet twelve wide computer eleven wide large screen eleven wide widescreen column centered">
                    <header>
                        <?php include "telo/hlavicka.php"; ?>
                    </header>
                    <main class="page-content">
                        <?= $content ?? '' ?>
                    </main>
                </div>