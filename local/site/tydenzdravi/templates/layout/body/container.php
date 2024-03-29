<?php
use Pes\Text\Html;
use Pes\View\Renderer\PhpTemplateFunctionsInterface;
/** @var PhpTemplateFunctionsInterface $this */
?>

    <div>
        <div class="ui grid">
            <div class="row">
                <?php $isMenuEditableMode=false; ?>
                <?= $this->insertIf( $isMenuEditableMode, __DIR__.'/container/teloEditableMode.php', $context); ?>
                <?= $this->insertIf( !$isMenuEditableMode, __DIR__.'/container/teloNoneditableMode.php', $context); ?>
                <?= $flash ?? '' ?>
                <?= $info ?? '' ?>
            </div>
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

