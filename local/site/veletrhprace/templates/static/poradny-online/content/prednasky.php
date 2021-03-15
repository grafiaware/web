<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

    $odkazPrednaskyAttributes = ['class' => 'ui large secondary button'];
    $odkazPrednaskyTextPrihlasitSe = 'Zde se budete moci přihlásit';
    $odkazPrednaskyTextZhlednout = 'Zhlédnout záznam';

    $prednaska = [
        [
            'jmeno' => '',
            'funkce' => '',
            'nazevPrednasky' => 'Kariérové poradenství',
            'perex' => 'Jak mít ještě lepší životopis? Co změnit? Co tam nedávat? Jak odpovídat na nabídky práce, jakého zaměstnavatele zvolit...',
            'datumCas' => '',
            'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
            [
                'href' => '',
                'target' => ''
            ],
            'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
        ],
        [
            'jmeno' => '',
            'funkce' => '',
            'nazevPrednasky' => 'Pracovně-právní poradna',
            'perex' => 'Na co si dát pozor při uzavírání pracovní smlouvy? Kde najdu příslušná znění zákonů a pravidel pro pracovně-právní vztahy?',
            'datumCas' => '',
            'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
            [
                'href' => '',
                'target' => ''
            ],
            'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
        ],
        [
            'jmeno' => '',
            'funkce' => '',
            'nazevPrednasky' => 'Image brýlová poradna',
            'perex' => '"Jak volit obroučky s ohledem na obličej i profesi?" "Hodí se to k mému typu?" Profesionální poradenství - vyzkoušejte, konzultujte, nestyďte se.',
            'datumCas' => '',
            'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
            [
                'href' => '',
                'target' => ''
            ],
            'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
        ],
        [
            'jmeno' => '',
            'funkce' => '',
            'nazevPrednasky' => 'Poradna v těžkých životních situacích',
            'perex' => 'Potřebujete s odborníkem probrat, co se vám stalo, ať už jde o ztrátu zaměstnání, exekuce, rozpad rodiny, finanční potíže, úmrtí v rodině...',
            'datumCas' => '',
            'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
            [
                'href' => '',
                'target' => ''
            ],
            'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
        ],
        [
            'jmeno' => '',
            'funkce' => '',
            'nazevPrednasky' => 'Psycholog radí',
            'perex' => 'Trápí vás "doba covidová"? Děti jsou frustrované, celá rodina zavřená doma, k rodičům nemůžete... Nechcete o svých problémech mluvit s kamarády a rodinou? Potřebujete povzbudit a s někým hledat východisko?',
            'datumCas' => '',
            'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
            [
                'href' => '',
                'target' => ''
            ],
            'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
        ],
    ]
?>

<div class="prednasejici">
    <div class="ui two column internally celled grid centered">
        <div class="stretched row">
            <div class="eight wide column"><p><b>Téma</b></p></div>
            <div class="eight wide column"><p><b>Název přednášky</b></p></div>
        </div>
        <?= $this->repeat(__DIR__.'/prednasky/prednaska.php', $prednaska) ?>
    </div>
</div>
