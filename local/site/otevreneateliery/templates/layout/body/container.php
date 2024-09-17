<?php
use Pes\Text\Html;
use Pes\View\Renderer\PhpTemplateFunctionsInterface;
/** @var PhpTemplateFunctionsInterface $this */
?>

<style>
        #loader {
            border: 12px solid #f3f3f3;
            border-radius: 50%;
            border-top: 12px solid darkred;
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
            color: black;
            position: absolute;   
            top:10px;
            left:-30px;
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
            <div class="sixteen wide mobile fourteen wide tablet eleven wide computer eleven wide large screen eleven wide widescreen column">
                <div class="row">
                    <?php $isMenuEditableMode=false; ?>
                    <?= $this->insertIf( $isMenuEditableMode, __DIR__.'/container/teloEditableMode.php', $context); ?>
                    <?= $this->insertIf( !$isMenuEditableMode, __DIR__.'/container/teloNoneditableMode.php', $context); ?>
                    <?= $flash ?? '' ?>
                    <?= $info ?? '' ?>
                </div>
                <div class="row">
                    <footer id="footer">
                        <span id="kontakty"></span>
                        <?php include "container/paticka.php"; ?>
                        
                    </footer>
                </div>
            </div>
        </div>
    </div>

