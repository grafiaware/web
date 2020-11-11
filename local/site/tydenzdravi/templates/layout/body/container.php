    <!--<div class="ui container editable">--> <!--$this->attributes($bodyContainerAttributes) -->
    <div class="ui grid">
        <div class="two wide mobile three wide tablet three wide computer four wide large screen four wide widescreen column">
            <div class="fix-bar">
                <?php include "container/telo/svislemenu.php"; ?>
                <?php include "container/hlavicka/prihlaseni.php"; ?>
                <?php include "container/telo/iconmenu.php"; ?>
            </div>
        </div>
        <div class="thirteen wide mobile twelve wide tablet twelve wide computer ten wide large screen ten wide widescreen column">
            <header>
                <?php include "container/hlavicka.php"; ?>
            </header>
            <main class="page-content">
                <?= $flash ?? '' ?>
                <?= $poznamky ?? '' ?>
                <?php include "container/prednasky.php"; ?>
            </main>
        </div>
    </div>
    <footer id="kontakt">
        <?php include "container/paticka.php"; ?>
    </footer>
