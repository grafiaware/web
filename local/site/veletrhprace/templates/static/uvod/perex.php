
<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */
?>
        <div class="obr-upoutavka">
            <div class="ui two column stackable centered grid">
                <div class="six wide column">
                    <img class="sirsi-obr"
                         src="images/pan_s_tabletem.jpg" 
                         srcset=" 
                          public/site/veletrhprace/images/pan_s_tabletem.jpg , 
                          public/site/veletrhprace/images/pan_s_tabletem_mobile.jpg 2x"
                          sizes="
                            (min-width: 768px) 100vw, 
                            480px"
                         width="476" height="671" alt="Pracovník s tabletem"/>
                    <div class="primarni-barva podklad nadpis presah">
                        <p>30. 3.—1. 4. 2021</p>
                    </div>
                </div>
                <div class="ten wide column">
                    <img src="images/mama.jpg" width="790" height="394" alt="Home office"/>
                    <img src="images/delnik_jupi.jpg" width="790" height="402" alt="Dělník na stavbě"/>
                    <div class="pruhledna-barva podklad blok text velky photo-scroll show-on-scroll">
                        <p><?= Text::mono('Najděte si lepší práci <span class="primarni-barva text velky tucne">odkudkoli!</span>') ?></p>
                    </div>
                </div>
            </div>
        </div>