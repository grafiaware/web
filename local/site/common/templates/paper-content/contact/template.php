<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\View\Renderer\ClassMap\ClassMapInterface;
use Component\Renderer\Html\Authored\Paper\ElementWrapper;
use Component\Renderer\Html\Authored\Paper\Buttons;
use Red\Model\Entity\PaperContentInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var ClassMapInterface $classMap */
/** @var ElementWrapper $elementWrapper */
/** @var Buttons $buttons */
/** @var PaperContentInterface $paperContent */
?>
            <div class="column">
                <content id="" class="">
                    <div class="ui segment">
                        <?= $paperContent ?>
                    </div>
                </content>
            </div>