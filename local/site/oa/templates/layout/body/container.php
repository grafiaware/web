<?php
use Pes\Text\Html;
?>
<!--<div class="ui container editable">-->
<div <?= Html::attributes($bodyContainerAttributes) ?> >
        <div class="column">
            <header>
                <?php include "container/hlavicka.php"; ?>
            </header>
            <main class="page-content">
                <?= $flash ?? '' ?>
                <?= $info ?? '' ?>
                <?php include "container/telo.php"; ?>
            </main>
            <footer>
                <?php include "container/paticka.php"; ?>
            </footer>
        </div>
    </div>