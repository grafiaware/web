<?php
use Pes\Text\Html;
use Pes\View\Renderer\PhpTemplateFunctionsInterface;
/** @var PhpTemplateFunctionsInterface $this */
?>

<style>
        #loader {
            border: 12px solid #f3f3f3;
            border-radius: 50%;
            border-top: 12px solid #444444;
            width: 70px;
            height: 70px;
            animation: spin 1s linear infinite;
        }

        .center {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            margin: auto;
        }
        
        .spinnertext {
            color: red;
            position: absolute;            
            left:-5px;
        }

        @keyframes spin {
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
    
    <div id="loader"  class="center">

        <p class="spinnertext">waiting..</p>.
    </div>
    <div id="loaded">
        <div class="ui grid centered">
            <div class="row">
                <?php $isMenuEditableMode=false; ?>
                <?= $this->insertIf( $isMenuEditableMode, __DIR__.'/container/teloEditableMode.php', $context); ?>
                <?= $this->insertIf( !$isMenuEditableMode, __DIR__.'/container/teloNoneditableMode.php', $context); ?>
                <?= $flash ?? '' ?>
                <?= $info ?? '' ?>
            </div>
            <div class="row">
                <div class="fourteen wide mobile fourteen wide tablet eleven wide computer eleven wide large screen eleven wide widescreen column">
                    <footer>
                        <span id="kontakty"></span>
                        <?php include "container/paticka.php"; ?>
                        <?php include "container/telo/prihlaseni.php"; ?>
                    </footer>
                </div>
            </div>
        </div>
    </div>

