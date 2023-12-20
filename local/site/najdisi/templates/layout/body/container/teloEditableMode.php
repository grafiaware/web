                <div class="two wide column">
                    <div class="fix-bar no-fix">
                        <?php /* include "telo/prihlaseni.php"; */?>
                        <?php include "telo/svislemenu.php"; ?>
                    </div>
                </div>

                <div class="thirteen wide mobile eight wide tablet nine wide computer ten wide large screen eleven wide widescreen column right floated">
                    <header>
                        <?php include "telo/hlavicka.php"; ?>
                        <?php include "telo/prihlaseni.php"; ?>
                    </header>
                    <main class="page-content">
                        <?= $content ?? '' ?>
                    </main>
                </div>