<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

?>

        <div class="title">
            <p class="podnadpis"><i class="dropdown icon"></i><?= $nazev ?>, <?= $mistoVykonu ?>  
                <span class="ui big red tag label"><?= $kategorie1 ?></span>
                <?= (($kategorie2 ?? '') ? '<span class="ui big red tag label">'.$kategorie2.'</span>' : '') ?>
            </p>
        </div>
        <div class="content">
            <div class="ui grid stackable">
                <div class="row">
                    <div class="four wide column"><b>Místo výkonu práce:</b></div>
                    <div class="six wide column"><?= $mistoVykonu ?></div>
                </div>
                <div class="row">
                    <div class="four wide column"><b>Požadované vzdělání:</b></div>
                    <div class="six wide column"><?= $vzdelani ?></div>
                </div>
                <div class="row">
                    <div class="four wide column">
                        <p><b>Popis pracovní pozice:</b></p>
                    </div>
                    <div class="twelve wide column"><p><?= $popisPozice ?></p></div>
                </div>
                <div class="row">
                    <div class="eight wide column">
                        <p><b>Požadujeme:</b></p>
                        <ul>
                            <?= $this->repeat(__DIR__.'/pozice/li.php', $pozadujeme, 'seznam') ?>
                        </ul>
                    </div>
                    <div class="eight wide column">
                        <p><b>Nabízíme:</b></p>
                        <ul>
                            <?= $this->repeat(__DIR__.'/pozice/li.php', $nabizime, 'seznam') ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>