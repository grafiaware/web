    <!--<div class="ui container editable">--> <!--$this->attributes($bodyContainerAttributes) -->
    <div class="ui grid"> 
        <span class="ui grid stackable centered ui float middle aligned line-height normal"></span>
        <div class="two wide mobile two wide tablet two wide computer two wide large screen two wide widescreen column">
            <div class="fix-bar">
                <?php include "container/telo/svislemenu.php"; ?>
                <?php include "container/hlavicka/prihlaseni.php"; ?>
                <?php include "container/telo/iconmenu.php"; ?>
            </div>
        </div>
        <div class="thirteen wide mobile twelve wide tablet twelve wide computer eleven wide large screen eleven wide widescreen column centered">
            <header>
                <?php include "container/hlavicka.php"; ?>
            </header>
            <main class="page-content">
                <?= $content ?? '' ?>
            </main>
        </div>
        <?= $flash ?? '' ?>
        <?= $poznamky ?? '' ?>
    </div>
    <footer>
        <span id="kontakty"></span>
        <?php include "container/paticka.php"; ?>
    </footer>
