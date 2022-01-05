
    <div <?= Html::attributes($bodyContainerAttributes)?> >
        <div class="ui grid">
            <?php include 'container/telo.php'?>
            <?= $flash ?? '' ?>
            <?= $poznamky ?? '' ?>
        </div>
        <footer>
            <span id="kontakty"></span>
            <?php include "container/paticka.php"; ?>
        </footer>
    </div>
