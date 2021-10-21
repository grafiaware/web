<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Text;

/** @var PhpTemplateRendererInterface $this */
?>

<div class="blok-nadpis-obr-text program">
    <div class="ui stackable centered grid">
        <div class="sixteen wide column">
            <?= Text::mono('
            <p class="text">Vážení návštěvníci, děkujeme vám za zájem o virtuální veletrh práce, který proběhl 30. 3. - 1. 4. 2021. Za 3&nbsp;dny ho navštívilo přes 2.000 návštěvníků, kteří v průměru viděli 14 stránek (dle statistik webu). Asi polovina z nich se opakovaně vracela. Přednášky a prezentace na youtube kanálu zhlédlo přes 1.300 diváků. Další informace naleznete v sekci <a href="web/v1/page/item/605885f092f1f" target="_blank">pro média</a>. </p>
            <p class="text"><b>I když veletrh skončil, stále se můžete registrovat či přihlásit</b> jako již registrovaný návštěvník a <span class="primarni-barva text">do konce dubna</span> odpovědět na některou z <a href="web/v1/page/item/6064397e726a1" target="_blank">nabízených pozic</a> (vložit životopis a motivační dopis ze svého profilu). U každého firemního stánku naleznete také kontakty na náborové pracovníky.</p>
            <p class="text">Stále ještě můžete ze záznamu zhlédnout přednášky a prezentace na našem <a href="https://www.youtube.com/channel/UC-Di-88rpUfBZUHHVf7tntQ" target="_blank">youtube kanálu</a>.<br/>
            Využijte svou šanci, získejte informace a kontakty!</p>
            <p class="text okraje-vertical"></p>
            ')?>
            <p class="nadpis podtrzeny nastred nadpis-scroll show-on-scroll">Anketa a slosování</p>
            <?=
                //<p class="text">Vyplňte <a href="https://forms.gle/w5NTnXbxEg6GGRLp7" target="_blank">ANKETU NÁVŠTĚVNÍKA</a> a zařaďte se do slosování o ceny, které proběhne 28. dubna!</p>
            Text::mono('
            <p class="text">Vyplňte <a href="web/v1/page/item/607400995acdd" target="_blank">ANKETU NÁVŠTĚVNÍKA</a> a zařaďte se do slosování o ceny, které proběhne 28. dubna!</p>
            <p class="text okraje-vertical"></p>
            ')?>
            <p class="nadpis podtrzeny nastred nadpis-scroll show-on-scroll">Program</p>
            <img src="@images/klic_foto.jpg" width="1280" height="420" alt="Obrázek k programu"/>
        </div>
        <div class="eight wide column"><a href="web/v1/page/item/604bcc5b3c5d7" class="ui primary fluid button">Zobrazit přednášky</a></div>
        <div class="eight wide column"><a href="web/v1/page/item/604bd0e2e440d" class="ui primary fluid button">Zobrazit poradny</a></div>
        <div class="sixteen wide column"><a href="web/v1/page/item/60619d3247985" class="ui primary fluid button">Online stánky podniků</a></div>
<!--        <div class="sixteen wide column">
                <p class="text tucne">
                    <?
                    Text::mono('V průběhu sedmi dnů vám nabídneme ZDARMA <b>online přednášky a rozhovory s odborníky</b> z různých oborů a také „body pro zdraví“, kde najdete <b>produkty či služby</b> na podporu zdravého životního stylu.')
                    ?>
                </p>
        </div>-->
    </div>
</div>