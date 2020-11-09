    <!--<div class="ui container editable">--> <!--$this->attributes($bodyContainerAttributes) -->
    <div class="ui grid">
        <div class="one wide tablet two wide computer four wide large screen four wide widescreen column">
            <div class="fix-bar">
                <?php include "container/telo/svislemenu.php"; ?>
                <?php include "container/hlavicka/prihlaseni.php"; ?>
                <?php include "container/telo/iconmenu.php"; ?>
            </div>
        </div>
        <div class="fifteen wide tablet thirteen wide computer ten wide large screen ten wide widescreen column">
            <header>
                <?php include "container/hlavicka.php"; ?>
            </header>
            <main class="page-content">
                <?= $flash ?? '' ?>
                <?= $poznamky ?? '' ?>
                <?php include "container/telo_tiny.php"; ?>
            </main>
        </div>
    </div>
    <footer>
        <?php include "container/paticka.php"; ?>
    </footer>