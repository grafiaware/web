<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
use Pes\Text\Text;
use Pes\Text\Html;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */

?>
<div class="vypis-prac-pozic">
    <div  class="navazat-kontakt">
    <div class="ui grid">
        <div class="sixteen wide column center aligned">
            <div class="ui large button profil-visible">Mám zájem o zaměstnání, přeji si poslat své údaje z profilu návštěvníka</div>
        </div>
        <div class="sixteen wide column">
            <div class="profil hidden">
                <?= $this->insert(__DIR__.'/vypis-pozic/osobni-udaje.php', ['container' => $container, 'shortName'=>$shortName]) ?>
            </div>
        </div>
    </div>
    </div>

    <div class="ui styled fluid accordion">
        <?= $this->repeat(__DIR__.'/vypis-pozic/pozice.php', $pracovniPozice)?>
    </div>
</div>


