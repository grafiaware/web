<?php
use Pes\Text\Html;
use Pes\View\Renderer\PhpTemplateFunctionsInterface;
/** @var PhpTemplateFunctionsInterface $this */
?>
<style>
    /*Loader načítání stránky*/
    #loader{
        position: absolute;
        width: 120px;
        height: 120px;
        top: 50%;
        left: 50%; 
        transform: translate(-50%, -50%);
        &::after{
            content: '';
            position: absolute;
            border: 12px solid #f3f3f3;
            border-radius: 50%;
            border-top: 12px solid darkred;
            width: 100%;
            height: 100%;
            box-sizing: border-box;
            animation: spin 1s linear infinite;
        }
    }
    .spinnertext{
        color: black;
        position: absolute;   
        top: 50%;
        left: 50%; 
        transform: translate(-50%, -50%);
    }
    @keyframes spin {
        100% {
            transform: rotate(360deg);
        }
    }
</style>

    <div id="loader">
        <p class="spinnertext">Načítání...</p>
    </div>
    <div id="loaded">
        <div class="ui grid centered">
            <div class="row">
                <?= $this->insertIf( $isContentEditable, __DIR__.'/container/teloEditableMode.php', $context); ?>
                <?= $this->insertIf( !$isContentEditable, __DIR__.'/container/teloNoneditableMode.php', $context); ?>
                <?= Html::tag('div', ['class'=>'cascade', 'data-red-apiuri'=>"web/v1/component/flash",])  ?>
                <?= Html::tag('div', ['class'=>'cascade', 'data-red-apiuri'=>"web/v1/component/infoBoard",])  ?>
            </div>
            <div class="row">
                <div class="thirteen wide computer twelve wide large screen twelve wide widescreen column">
                     <footer id="footer">
                        <span id="kontakty"></span>
                        <?php include "container/paticka.php"; ?>
                        
                    </footer>
                </div>
            </div>
        </div>
    </div>

