<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Component\Renderer\Html\Authored\Paper\ElementWrapper;
use Component\Renderer\Html\Authored\Paper\Buttons;
use Red\Model\Entity\PaperAggregatePaperContentInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var ElementWrapper $elementWrapper */
/** @var Buttons $buttons */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

?>

        <div class="" data-template="<?= "contents_in_circle" ?>">
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
                        <?= $this->repeat(PROJECT_PATH."local/site/common/templates/paper-content/contents_in_circle_js.php", $contents, 'paperContent'); ?>
                    </div>
                </div>
            </div>
        </div>
