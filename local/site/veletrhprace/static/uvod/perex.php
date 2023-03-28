
<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;
use Pes\Text\Text;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperSectionInterface $paperAggregate */
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
                        <p>kontaktujte firmy do konce dubna</p>
                    </div>
                </div>
                <div class="ten wide column">
                    <img src="@siteimages/mama.jpg" width="790" height="394" alt="Home office"/>
                    <img src="@siteimages/delnik_jupi.jpg" width="790" height="402" alt="Dělník na stavbě"/>
                    <div class="pruhledna-barva podklad blok podnadpis photo-scroll show-on-scroll">
                        <p><?= Text::mono('Najděte si lepší práci <span class="primarni-barva">odkudkoli!</span>') ?></p>
                    </div>
                </div>
            </div>
        </div>