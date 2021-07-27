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

    <article class="" data-template="<?= "list_of_courses" ?>">
        <section class="">
            <?= $headline ?>
            <?= $perex ?>
        </section>
        <div class="ui grid stackable centered">
            <div class="sixteen wide column">
                <content id="content_4" class="">
                    <p class="text velky primarni-barva zadne-okraje">Architekt kybernetické bezpečnosti</p>
                    <p class="text tucne zadne-okraje"><i class="calendar alternate icon"></i>15. - 16. 09. 2021</p>
                    <div class="text">
                        <div class="float-img vlevo">
                            floatovací obrázek 
                        </div>
                        <p>
                            Kurz je zaměřený na návrh a rozvoj informační architektury v rámci organizační bezpečnosti.
                            Architekt by měl navrhovat a realizovat, na základě požadavku zákona o kybernetické bezpečnosti, zabezpečení firmy proti úniku dat (písemné, elektronické), měl by znát rizika a faktory „nebezpečnosti“ nebo „bezpečnosti“ dané oblasti.
                        </p>
                    </div>
                    <p><a href="" class="ui button">Více informací</a> <a href="" class="ui primary button">Přihlásit se</a></p>
                    <hr/>
                </content>
            </div> 
            <?= $this->repeat(PROJECT_PATH."local/site/common/templates/paper-content/list_of_courses/template.php", $contents, 'paperContent'); ?>
        </div>
    </article>