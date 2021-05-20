
<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Red\Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */
?>
        <div class="obr-upoutavka">
            <div class="ui two column stackable centered grid">
                <div class="sixteen wide column">
                    <div class="sekundarni-barva podklad nadpis vlevo">
                        <p><?= Text::mono('Věnujte týden svému zdraví!')?></p>
                    </div>
                    <img src="@images/pexels-photo-863977-web-orez.jpg" width="1178" height="518" alt="Obrázek"/>
                </div>
                <div class="ten wide column">
                    <img src="@images/pexels-cemal-taskiran-web-orez.jpg" width="727" height="474" alt="Obrázek"/>
                </div>
                <div class="six wide column">
                    <div class="primarni-barva podklad nadpis vpravo">
                        <p>21.—27. 11. 2020</p>
                    </div>
                    <div class="sekundarni-barva podklad blok text okraje">
                        <p class="podnadpis"><b>TÝDEN ZDRAVÍ</b><br/>Zdravá rodina</p>
                        <p>
                             <?= Text::mono('<b>Zúčastněte se nové osvětové akce na podporu prevence a udržení dobrého zdravotního stavu všech generací!</b>') ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>