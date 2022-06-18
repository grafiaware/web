<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Component\Renderer\Html\Content\Authored\Paper\ElementWrapper;
use Component\Renderer\Html\Content\Authored\Paper\Buttons;
use Red\Model\Entity\PaperAggregatePaperContentInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var ElementWrapper $elementWrapper */
/** @var Buttons $buttons */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

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
                        <?= $this->repeat(PROJECT_PATH."local/site/common/templates/paper-content/contents_in_circle_js.php", $sections, 'paperSection'); ?>
                    </div>
                </div>
            </div>
        </div>
