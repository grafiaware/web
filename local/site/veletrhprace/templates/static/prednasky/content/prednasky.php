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
            'nazevPrednasky' => 'Jak oslovit zaměstnavatele?',
            'perex' => 'Co má a nemá obsahovat životopis, co psát do motivačního dopisu, abyste zaujali',
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
            'nazevPrednasky' => 'Jak se připravit na pracovní pohovor?',
            'perex' => 'Nejčastější chyby při pracovním pohovoru a jak se jich vyvarovat?',
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
            'nazevPrednasky' => 'Na co dát pozor při uzavírání pracovní smlouvy?',
            'perex' => 'Co musí a nemusí být v pracovní smlouvě a proč? Užitečné tipy a rady pro Vás.',
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
            'nazevPrednasky' => 'Brýle – pracovní pomůcka i módní doplněk',
            'perex' => 'Jak volba brýlí ovlivní váš výkon i akceptaci okolím',
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
            'nazevPrednasky' => 'Nástup do práce po rodičovské dovolené',
            'perex' => 'Projekt Moje budoucnost vám pomůže s motivací, rekvalifikací, PC dovednostmi, jazyky i s vlastním hledáním nové práce',
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
            'nazevPrednasky' => 'Jaká je budoucnost vysokoškolských absolventů?',
            'perex' => 'Interview s děkanem FST ZČU',
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
            'nazevPrednasky' => 'Jsem z tý školy blázen?',
            'perex' => 'Jak poznat, že potřebuji pomoc…',
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
            'nazevPrednasky' => 'Zvolená rekvalifikace zdarma – cesta k nové profesi',
            'perex' => 'Co musíte udělat, když chcete, aby vám ÚP proplatil kurz?',
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
            'nazevPrednasky' => 'Domluvíte se ve své profesi anglicky/německy?',
            'perex' => 'Využijte možnosti intenzívních kurzů odborné AJ/NJ v Plzni',
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
            'nazevPrednasky' => 'Možnosti zaměstnání v zahraničí',
            'perex' => 'Chcete vycestovat za prací do zahraničí? EURES poradí, jak na to.',
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
            'nazevPrednasky' => 'Jak nastartovat svůj vlastní business v době covidu?',
            'perex' => 'Chcete rozjet vlastní podnikání? A víte, kdo a jak Vám může pomoci?',
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
            'nazevPrednasky' => 'Jak se nezbláznit v době covidu?',
            'perex' => 'Máte pocit, že už vám hrabe? Jak se pozná, že potřebuji pomoc odborníka? A jakého?',
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
            'nazevPrednasky' => 'Ocenění Mamma Parent Friendly za rok 2020',
            'perex' => 'Vyhlášení cen pro podniky přátelské rodině a představení výhod práce u těchto podniků',
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
