<?php
use Pes\Text\Html;
use Pes\View\Renderer\PhpTemplateFunctionsInterface;
/** @var PhpTemplateFunctionsInterface $this */
?>

    <div>
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

