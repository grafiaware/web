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

    <div class="" data-template="<?= "divided_rows" ?>">
        <div class="ui grid">
            <div class="sixteen wide column">
                <section>
                        <?= $headline ?>
                        <?= $perex ?>
                </section>
            </div>
        </div>
        <div class="ui grid stackable centered">
            <?= $this->repeat(PROJECT_PATH."local/site/common/templates/paper-content/divided_rows.php", $sections, 'paperSection'); ?>
        </div>
    </div>