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

        <div class="obr-upoutavka" data-template="images_paper">
            <div class="ui two column stackable centered grid">
                <div class="six wide column">
                    <img class="sirsi-obr"
                         src="@commonimages/sablony-prace-na-pc.png" 
                         srcset=" 
                          @commonimages/sablony-prace-na-pc.png 480w,  
                          @commonimages/sablony-prace-na-pc_mobile.png 820w"
                          sizes="
                            (max-width: 768px) 100vw,
                            120px"
                         width="476" height="671" alt="Pracovník na PC"/>
                    <div class="primarni-barva podklad nadpis presah">
                        <?= $headline ?>
                    </div>
                </div>
                <div class="ten wide column">
                    <img src="@commonimages/sablony-komunikace.jpg" width="790" height="394" alt="Jednání"/>
                    <img src="@commonimages/sablony-zabava.jpg" width="790" height="402" alt="Pracovní tým"/>
                    <div class="pruhledna-barva podklad blok podnadpis photo-scroll show-on-scroll">
                        <?= $perex ?>
                    </div>
                </div>
            </div>
            <div class="ui grid">
                 <?= $this->repeat(PROJECT_PATH."local/site/common/templates/paper-content/images_paper.php", $sections, 'paperSection'); ?>
            </div>
        </div>