<?php
use Pes\Text\Html;
?>

<div <?= Html::attributes($bodyContainerAttributes)?> >
    <div class="ui container" >
        <div class="column">
            <header>
                <?php include "container/hlavicka.php"; ?>
            </header>
            <main class="page-content">
                <?= $flash ?? '' ?>
                <?= $poznamky ?? '' ?>
                <?php include "container/telo.php"; ?>
            </main>
            <footer>
                <?php include "container/paticka.php"; ?>
            </footer>
        </div>
    </div>
</div>