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

        <div class="obr-upoutavka">
            <div class="ui two column stackable centered grid">
                <div class="six wide column">
                    <img class="sirsi-obr"
                         src="@siteimages/pan_s_tabletem.jpg" 
                         srcset=" 
                          @siteimages/pan_s_tabletem.jpg 480w,  
                          @siteimages/pan_s_tabletem_mobile.jpg 820w"
                          sizes="
                            (max-width: 768px) 100vw,
                            120px"
                         width="476" height="671" alt="Pracovník s tabletem"/>
                    <div class="primarni-barva podklad nadpis presah">
                        <?= $headline ?>
                    </div>
                </div>
                <div class="ten wide column">
                    <img src="@siteimages/mama.jpg" width="790" height="394" alt="Home office"/>
                    <img src="@siteimages/delnik_jupi.jpg" width="790" height="402" alt="Dělník na stavbě"/>
                    <div class="pruhledna-barva podklad blok podnadpis photo-scroll show-on-scroll">
                        <?= $perex ?>
                    </div>
                </div>
            </div>
            <div class="ui grid">
                 <?= $this->repeat(PROJECT_PATH."local/site/common/templates/paper-content/images_paper.php", $contents, 'paperContent'); ?>
            </div>
        </div>