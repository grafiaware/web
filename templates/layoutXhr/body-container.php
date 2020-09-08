    <!--<div class="ui container editable">-->
    <div <?= $this->attributes($bodyContainerAttributes) ?> >
        <div class="column">
            <header>
                <?php include "body/hlavicka.php"; ?>
            </header>
            <main class="page-content">
                <?= $flash ?? '' ?>
                <?= $poznamky ?? '' ?>
                <?php include "body/telo.php"; ?>
            </main>
            <footer>
                <?php include "body/paticka.php"; ?>
            </footer>
        </div>
    </div>