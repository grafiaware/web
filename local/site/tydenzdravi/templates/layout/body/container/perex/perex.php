<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */
?>
        <div class="obr-upoutavka"> 
            <div class="ui two column stackable centered grid">
                <div class="sixteen wide column">
                    <div class="sekundarni-barva podklad nadpis vlevo">
                        <p>Věnujte týden svému zdraví!</p>
                    </div>
                    <img src="/_www_tz_files/files/pexels-photo-863977-web-orez.jpg" width="100%" alt="Obrázek"/>
                </div>
                <div class="ten wide column">
                    <img src="/_www_tz_files/files/pexels-cemal-taskiran-web-orez.jpg" width="100%" alt="Obrázek"/>
                </div>
                <div class="six wide column">
                    <div class="primarni-barva podklad nadpis vpravo">
                        <p>21.—27. 11. 2020</p>
                    </div>
                    <div class="sekundarni-barva podklad blok text">
                        <p class="podnadpis"><b>TÝDEN ZDRAVÍ</b><br/>Zdravá rodina</p>
                        <p>
                             <?= $this->mono('<b>Zúčastněte se nové osvětové akce na podporu prevence a udržení dobrého zdravotního stavu všech generací!</b>') ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

