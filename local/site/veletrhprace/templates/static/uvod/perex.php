
<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */
?>
        <div class="obr-upoutavka">
            <div class="ui two column stackable centered grid">
                <div class="six wide column">
                    <img class="sirsi-obr"
                         src="images/pan_s_tabletem.jpg" 
                         srcset=" 
                          public/site/veletrhprace/images/pan_s_tabletem.jpg 480w,  
                          public/site/veletrhprace/images/pan_s_tabletem_mobile.jpg 820w"
                          sizes="
                            (max-width: 768px) 100vw,
                            (min-width: 769px) 480px"
                         width="476" height="671" alt="Pracovník s tabletem"/>
                    <div class="primarni-barva podklad nadpis presah">
                        <p>30. 3.—1. 4. 2021</p>
                    </div>
                </div>
                <div class="ten wide column">
                    <img src="images/mama.jpg" width="790" height="394" alt="Home office"/>
                    <img src="images/delnik_jupi.jpg" width="790" height="402" alt="Dělník na stavbě"/>
                    <div class="pruhledna-barva podklad blok podnadpis photo-scroll show-on-scroll">
                        <p><?= Text::mono('Najděte si lepší práci <span class="primarni-barva text velky tucne">odkudkoli!</span>') ?></p>
                    </div>
                </div>
            </div>
        </div>