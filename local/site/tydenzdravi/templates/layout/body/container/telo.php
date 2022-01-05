        <div class="two wide mobile two wide tablet two wide computer two wide large screen two wide widescreen column">
            <div class="fix-bar">
                <?php include "telo/svislemenu.php"; ?>
                <?php include "telo/prihlaseni.php"; ?>
                <?php include "telo/iconmenu.php"; ?>
            </div>
        </div>
        <div class="thirteen wide mobile twelve wide tablet twelve wide computer ten wide large screen ten wide widescreen column centered">
            <header>
                <?php include "telo/hlavicka.php"; ?>
            </header> 
            <main class="page-content">
                <?= $content ?? '' ?>
            </main>
        </div>