<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */
?>

<div class="sekundarni-barva blokEL-obr-text">
    <div class="ui stackable centered grid">
        <div class="sixteen wide column">
            <img src="/_www_tz_files/files/pexels-andrea-piacquadio-web-orez.jpg" width="100%" height="" alt="Obrázek"/>
            <div class="velky text vlevo">
                <p>
                    <?= $this->mono('Ve spolupráci s <b>odborníky</b> proto společnost <b>Grafia</b> pořádá akci, jejímž cílem je <b>zvýšit povědomí veřejnosti o zdravém životním stylu, podpoře vlastní imunity a rozumném přístupu k vlastnímu zdraví.</b>') ?>
                </p>
            </div>
        </div>
    </div>
</div>