<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;

/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */
?>

<div class="blok-nadpis-obr-text program">
    <div class="ui stackable centered grid">
        <div class="sixteen wide column">
            <?= Text::mono('
            <p class="text">Vážení návštěvníci, děkujeme vám za zájem o virtuální veletrh práce. Za 3 dny ho navštívilo přes 2.000 návštěvníků, kteří v průměru viděli 14 stránek (dle statistik webu). Asi polovina z nich se opakovaně vracela. Přednášky a prezentace na youtube kanálu zhlédlo přes 1.300 diváků. Další informace naleznete v sekci <a href="www/item/cs/605885f092f1f" target="_blank">pro média</a>. </p>
            <p class="text">I když veletrh skončil, stále se můžete <b>registrovat</b> či <b>přihlásit</b> jako již registrovaný návštěvník a <span class="primarni-barva text">do konce dubna</span> odpovědět na některou z <a href="www/item/cs/6064397e726a1" target="_blank">nabízených pozic</a> (vložit životopis a motivační dopis ze svého profilu). U každého firemního stánku naleznete také kontakty na náborové pracovníky.</p>
            <p class="text">Stále ještě můžete ze záznamu zhlédnout přednášky a prezentace na našem <a href="https://www.youtube.com/channel/UC-Di-88rpUfBZUHHVf7tntQ" target="_blank">youtube kanálu</a>.<br/>
            Využijte svou šanci, získejte informace a kontakty!</p>
            <p class="text okraje-vertical"></p>
            ')?>
            <p class="nadpis podtrzeny nastred nadpis-scroll show-on-scroll">Program</p>
            <img src="images/klic_foto.jpg" width="1280" height="420" alt="Obrázek k programu"/>
        </div>
        <div class="eight wide column"><a href="www/item/cs/604bcc5b3c5d7" class="ui primary fluid massive button">Zobrazit přednášky</a></div>
        <div class="eight wide column"><a href="www/item/cs/604bd0e2e440d" class="ui primary fluid massive button">Zobrazit poradny</a></div>
        <div class="sixteen wide column"><a href="www/item/cs/60619d3247985" class="ui primary fluid massive button">Online stánky podniků</a></div>
<!--        <div class="sixteen wide column">
                <p class="text tucne">
                    <?
                    Text::mono('V průběhu sedmi dnů vám nabídneme ZDARMA <b>online přednášky a rozhovory s odborníky</b> z různých oborů a také „body pro zdraví“, kde najdete <b>produkty či služby</b> na podporu zdravého životního stylu.')
                    ?>
                </p>
        </div>-->
    </div>
</div>