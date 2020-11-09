    <!--<div class="ui container editable">--> <!--$this->attributes($bodyContainerAttributes) -->
    <div class="ui grid">
        <div class="three wide tablet four wide computer column">
            <div class="fix-bar">
                <?php include "container/telo/svislemenu.php"; ?>
                <?php include "container/hlavicka/prihlaseni.php"; ?>
                <?php include "container/telo/iconmenu.php"; ?>
            </div>
        </div>
        <div class="twelve wide tablet ten wide computer column">
            <header>
                <?php include "container/hlavicka.php"; ?>
            </header>
            <main class="page-content">
                <?= $flash ?? '' ?>
                <?= $poznamky ?? '' ?>
                <?php include "container/telo.php"; ?>
            </main>
        </div>
    </div>
    <footer>
        <?php include "container/paticka.php"; ?>
    </footer>