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
            <div class="sixteen wide column">
                <content id="" class="">
                    <p class="text velky primarni-barva zadne-okraje">Architekt kybernetické bezpečnosti</p>
                    <p class="text tucne zadne-okraje"><i class="calendar alternate icon"></i>15. - 16. 09. 2021</p>
                    <div class="text">
                        <div class="float-img vlevo">
                            floatovací obrázek
                        </div>
                        <?= $paperContent ?>
                    </div>
                    <p><a href="" class="ui button">Více informací</a> <a href="" class="ui primary button">Přihlásit se</a></p>
                    <hr/>
                </content>
            </div>