    <!--<div class="ui container editable">-->
    <div <?= $this->attributes($bodyContainerAttributes) ?> >
        <div class="column">
            <header>
                <?php include "container/hlavicka.php"; ?>
            </header>
            <main class="page-content">
                <div id="component_flash">
                    <script>$("#component_flash").load("component/v1/flash");</script>
                </div>
                <?= $poznamky ?? '' ?>
                <?php include "container/telo.php"; ?>
            </main>
            <footer>
                <?php include "container/paticka.php"; ?>
            </footer>
        </div>
    </div>