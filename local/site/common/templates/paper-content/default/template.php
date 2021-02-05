<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\View\Renderer\ClassMap\ClassMapInterface;
use Component\Renderer\Html\Authored\ElementWrapper;
use Component\Renderer\Html\Authored\Buttons;
use Model\Entity\PaperContentInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var ClassMapInterface $classMap */
/** @var ElementWrapper $elementWrapper */
/** @var Buttons $buttons */
/** @var PaperContentInterface $paperContent */
?>
<section>
    <div class="paper-content">
        <div class="ui right tiny corner blue label">
        </div>
        <div class="semafor">

        </div>
        <div class="author-text">
            <?= $elementWrapper->wrapContent($paperContent) ?>
        </div>
    </div>
</section>