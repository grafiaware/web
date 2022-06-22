<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Component\Renderer\Html\Content\Authored\Paper\ElementWrapper;
use Component\Renderer\Html\Content\Authored\Paper\Buttons;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var ElementWrapper $elementWrapper */
/** @var Buttons $buttons */
/** @var PaperAggregatePaperSectionInterface $paperAggregate */

?>

    <div class="" data-template="columns_perex_contents">
        <div class="ui grid">
            <div class="sixteen wide column">
                <?= $headline ?>
            </div>
        </div>
        <div class="ui grid stackable horizontalne-prohodit">
            <div class="eight wide column">
                <?= $perex ?>
            </div>
            <div class="eight wide column">
                <div class="ui grid">
                    <?= $this->repeat(PROJECT_PATH."local/site/common/templates/paper-content/columns_perex_contents.php", $sections, 'paperSection'); ?>
                </div>
            </div>
        </div>
    </div>