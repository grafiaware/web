<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */
?>

<perex>
    <?php include "perex/perex.php" ?>
</perex>
<div class="blok-nadpis-obr-text">
    <div class="ui stackable centered grid">
        <div class="sixteen wide column">
            <div class="cerveny podklad nadpis vlevo">
                <p>O akci</p>
            </div>
            <img src="/_www_tz_files/files/pexels-photo-web-orez.jpg" width="100%" height="" />
            <div class="velky text vpravo">
                <p>
                    <?= $this->mono('V poslední době pod vlivem Covidu-19 lidé zanedbávali preventivní návštěvy lékařů, odsouvali i nutná ošetření a kontroly. Vlivem médií došlo u řady lidí k úzkostným a iracionálním reakcím, vedoucím až ke škodám na zdraví.') ?>
                </p>
            </div>
        </div>
    </div>

</div>
<div class="zeleny-blok-obr-text">
    <div class="ui stackable centered grid">
        <div class="sixteen wide column">
            <img src="/_www_tz_files/files/pexels-andrea-piacquadio-web-orez.jpg" width="100%" height="" />
            <div class="velky text vlevo">
                <p>
                    <?= $this->mono('Ve spolupráci s <b>odborníky</b> proto společnost <b>Grafia</b> pořádá akci, jejímž cílem je <b>zvýšit povědomí veřejnosti o zdravém životním stylu, podpoře vlastní imunity a rozumném přístupu k vlastnímu zdraví.</b>') ?>
                </p>
            </div>
        </div>
    </div>
</div>
<div class="dva-sloupce-nadpis" id="kontakt">
    <div class="ui two column stackable centered grid">
        <div class="six wide column middle aligned">
            <img src="/_www_tz_files/files/LogoGrafia.jpg" width="300" height="" />
        </div>
        <div class="ten wide column">
            <div class="cerveny podklad nadpis vpravo">
                <p>Organizátor</p>
            </div>
            <div class="velky text">
                <p>Autorem myšlenky Týdne zdraví a organizátorem akce je <b>Grafia, s.r.o.</b></p>
                <p>
                    <?= $this->mono('Umíme efektivně komunikovat akce našich zákazníků i ty vlastní, vzděláváme a zkoušíme dospělé, organizujeme eventy na klíč, vydáváme knihy... Děláme to už od roku 1993 – rádi a dobře! Budeme rádi za Vaše reakce či připomínky ke zlepšení: <br/> pište na <b>slukova@grafia.cz <br/>nebo volejte 774 484 855</b>') ?>
                </p>
            </div>

        </div>
    </div>
</div>

