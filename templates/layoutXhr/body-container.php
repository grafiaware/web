    <!--<div class="ui container editable">-->
    <div <?= $this->attributes($bodyContainerAttributes) ?> >
        <div class="column">
            <header>
                <?php include "body/hlavicka.php"; ?>
            </header>
            <main class="page-content">
                <div id="component_flash">
                    <script>$("#component_flash").load("component/flash");</script>
                </div>
                <?= $poznamky ?? '' ?>
                <?php include "body/telo.php"; ?>
            </main>
            <footer>
                <?php include "body/paticka.php"; ?>
            </footer>
        </div>
    </div>