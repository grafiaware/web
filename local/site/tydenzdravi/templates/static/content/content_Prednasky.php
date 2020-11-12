<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregateInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregateInterface $paperAggregate */
?>

<div class="prednasejici">
    <div class="ui two column internally celled grid centered">
        <div class="stretched row">
            <div class="eight wide column"><p><b>Přednášející</b></p></div>
            <div class="eight wide column"><p><b>Název přednášky</b></p></div>
        </div>
        <div class="stretched row">
            <div class="eight wide column"><p><b>MUDr. Miloš Pešek CSc.</b></p><p><?= $this->mono('přednosta plicní kliniky FN Plzeň')?> </p></div>
            <div class="eight wide column"><p><?= $this->mono('Odpovídá na dotazy')?></p></div>
        </div>
        <div class="stretched row">
            <div class="eight wide column"><p><b>Konstantin Wiesner</b></p><p><?= $this->mono('běžec, ultramaratonec')?></p></div>
            <div class="eight wide column"><p><?= $this->mono('Běhání pro zdraví')?></p></div>
        </div>
        <div class="stretched row">
            <div class="eight wide column"><p><b>Iveta Churavá</b></p><p><?= $this->mono('propagátorka regionálních potravin U Lidušky')?></p></div>
            <div class="eight wide column"><p><?= $this->mono('Opravdové jídlo')?></p></div>
        </div>
        <div class="stretched row">
            <div class="eight wide column"><p><b>Renata Kurková</b></p><p><?= $this->mono('lékárnice, homeopatička')?></p></div>
            <div class="eight wide column"><p><?= $this->mono('Schüsslerovy soli na podporu imunity')?></p></div>
        </div>
        <div class="stretched row">
            <div class="eight wide column"><p><b>Alena Vrbová</b></p><p><?= $this->mono('krizový intervent a sociální pracovnice Ledovec')?></p></div>
            <div class="eight wide column"><p><?= $this->mono('Nezblázni se z covidu')?></p></div>
        </div>
        <div class="stretched row">
            <div class="eight wide column"><p><b>Jindra Švarcová</b></p><p><?= $this->mono('oční optik a prezidentka Lions Clubu Plzeň Bohemia')?></p></div>
            <div class="eight wide column"><p><?= $this->mono('Nezanedbávejte své oči! A prevence očních vad u předškolních dětí')?></p></div>
        </div>
        <div class="stretched row">
            <div class="eight wide column"><p><b>Klub sportovních otužilců Plzeň</b></p></div>
            <div class="eight wide column"><p><?= $this->mono('Jak se začít otužovat')?></p></div>
        </div>
        <div class="stretched row">
            <div class="eight wide column"><p><b>MUDr. Pavel Tomeš</b></p><p><?= $this->mono('sexuolog')?></p></div>
            <div class="eight wide column"><p><?= $this->mono('Odpovídá na vaše dotazy')?></p></div>
        </div>
        <div class="stretched row">
            <div class="eight wide column"><p><b>Anička Černá</b></p><p><?= $this->mono('lektorka jógy smíchu')?></p></div>
            <div class="eight wide column"><p><?= $this->mono('Směje se celá rodina')?></p></div>
        </div>
        <div class="stretched row">
            <div class="eight wide column"><p><b>David Brabec</b></p><p><?= $this->mono('instruktor sebeobrany, moderátor a konzultant')?></p></div>
            <div class="eight wide column"><p><?= $this->mono('Sebeobranou ke zdraví')?></p></div>
        </div>
        <div class="stretched row">
            <div class="eight wide column"><p><b>Vlastimila Faiferlíková</b></p><p><?= $this->mono('Předsedkyně Správní rady, ředitelka organizace, garant a koordinátorka dobrovolnických projektů')?></p></div>
            <div class="eight wide column"><p><?= $this->mono('Rodinné vztahy a péče o seniory v dnešním světě')?></p></div>
        </div>
    </div>
</div>
