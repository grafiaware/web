<?php
use Pes\Text\Html;
?>

    <div <?= Html::attributes($bodyContainerAttributes)?> >
        <div class="ui grid">
            
            <!--<div class="two wide column">-->
            <!-- editMenu změnit na: --> 
            <div class="two wide column editMenu">
            <!-- mění se jen otevírací html tag výše--> 
            
                <div class="fix-bar"> 
                    <?php include "container/telo/svislemenu.php"; ?>
                    <?php include "container/hlavicka/prihlaseni.php"; ?>
                    <?php include "container/telo/iconmenu.php"; ?>
                </div>
            </div>
            
            <!--<div class="thirteen wide mobile twelve wide tablet twelve wide computer eleven wide large screen eleven wide widescreen column centered">--> 
            <!-- editMenu změnit na: --> 
            <div class="thirteen wide mobile eight wide tablet nine wide computer ten wide large screen eleven wide widescreen column editMenu-article">
            <!-- mění se jen otevírací html tag výše-->   
            
                <header>
                    <?php include "container/hlavicka.php"; ?>
                </header>
                <main class="page-content">
                    <?= $content ?? '' ?>
                </main>
            </div>
            <?= $flash ?? '' ?>
            <?= $poznamky ?? '' ?>

            <div class="row">
                <div class="sixteen wide column">
                    <footer>
                        <span id="kontakty"></span>
                        <?php include "container/paticka.php"; ?>
                    </footer>
                </div>
            </div>
        </div>
    </div>

