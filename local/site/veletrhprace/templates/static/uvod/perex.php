
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
                    <img class="sirsi-obr" src="images/pan_s_tabletem.jpg" width="" height="" alt="Obrázek"/>
                    <div class="primarni-barva podklad nadpis presah">
                        <p>17—19/3/2021</p>
                    </div>
                </div>
                <div class="ten wide column">
                    <img src="images/mama.jpg" width="" height="" alt="Obrázek"/>
                    <img src="images/delnik_jupi.jpg" width="" height="" alt="Obrázek"/>
                    <div class="pruhledna-barva podklad blok text velky photo-scroll show-on-scroll">
                        <p><?= Text::mono('Najděte si lepší práci <span class="primarni-barva text velky tucne">odkudkoli!</span>') ?></p>
                    </div>
                </div>
            </div>
        </div>