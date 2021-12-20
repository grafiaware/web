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

    <div class="" data-template="<?= "rows" ?>">
        <div class="ui grid">
            <div class="sixteen wide column">
                <section>
                        <?= $headline ?>  
                        <?= $perex ?>
                </section>
            </div>
        </div>
        <div class="ui grid stackable centered">
            <?= $this->repeat(PROJECT_PATH."local/site/common/templates/paper-content/rows.php", $contents, 'paperContent'); ?>
        </div>
    </div>