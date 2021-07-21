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
        <?= $selectTemplate ?? "" ?>
        <article class="" data-template="<?= "sem jméno template" ?>">
            <?= $articleButtonForms ?? "" ?>
            <section>
                    <?= $headline ?>
                    <?= $perex ?>
            </section>
            <section>
                <?=
        $this->repeat(PROJECT_PATH."local/site/common/templates/paper-content/default/template.php", $contents, 'paperContent'); ?>
            </section>
        </article>