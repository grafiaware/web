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
    <!-- horizontalne-prohodit** -->
    <div class="" data-template="columns_perex_contents">
        <div class="ui grid">
            <div class="sixteen wide column">
                <?= $headline ?>
            </div>
        </div>
        <div class="ui grid horizontalne-prohodit">
            <div class="sixteen wide mobile eight wide computer column">
                <?= $perex ?>
            </div>
            <div class="sixteen wide mobile eight wide computer column">
                <div class="ui grid">
                    <?= $this->repeat(__DIR__."/section/columns_perex_contents.php", $sections, 'paperSection'); ?>
                </div>
            </div>
        </div>
<!--        <div class="ui grid stackable horizontalne-prohodit">
            <div class="eight wide column">
                <?php /*= $perex */?>
            </div>
            <div class="eight wide column">
                <div class="ui grid">
                    <?php /*= $this->repeat(__DIR__."/section/columns_perex_contents.php", $sections, 'paperSection'); */?>
                </div>
            </div>
        </div>-->
    </div>