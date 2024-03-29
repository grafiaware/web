<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Component\Renderer\Html\Content\Authored\Paper\ElementWrapper;
use Red\Component\Renderer\Html\Content\Authored\Paper\Buttons;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var ElementWrapper $elementWrapper */
/** @var Buttons $buttons */
/** @var PaperAggregatePaperSectionInterface $paperAggregate */

?>

        <div class="template-circle" data-template="contents_in_circle">
            <div class="ui grid">
                <div class="sixteen wide column">
                    <section>
                        <?= $headline ?>
                        <?= $perex ?>
                    </section>
                </div>
            </div>
            <div class="ui grid">
                <div class="sixteen wide column">
                    <div id="circle" class="contents_in_circle">
                        <?= $this->repeat(__DIR__."/section/contents_in_circle_js.php", $sections, 'paperSection'); ?>
                    </div>
                </div>
            </div>
        </div>
