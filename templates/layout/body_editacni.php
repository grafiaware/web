    <div class="ui container editable">
        <div class="column">
            <header>
                <?php include "body/hlavicka.php"; ?>
            </header>
            <main class="page-content">
                <?= $poznamky ?? '' ?>
                <?php include "body/telo.php"; ?>
            </main>
            <footer>
                <?php include "body/paticka.php"; ?>
            </footer>
        </div>
    </div>