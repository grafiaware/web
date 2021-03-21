<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

    $odkazPrednaskyAttributes = ['class' => 'ui large blue button'];
    $odkazPrednaskyTextPrihlasitSe = 'Zde se budete moci přihlásit';
    $odkazPrednaskyTextZhlednout = 'Zhlédnout záznam';

    $prednaska = [
        [
            'datum' => '30. 3. 2021',
            'prednasky' => [
                [
                    'publikovano' => '1',
                    'jmeno' => '',
                    'funkce' => 'Státní úřad inspekce práce',
                    'nazevPrednasky' => 'Pracovně právní problematika v době covidu',
                    'perex' => 'Výpovědi, překážky v práci, náhrada mzdy, náležitosti pracovní smlouvy…',
                    'casOd' => '10:05',
                    'casDo' => '10:40',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ],
                [
                    'publikovano' => '1',
                    'jmeno' => '',
                    'funkce' => '',
                    'nazevPrednasky' => 'Wienersberger',
                    'perex' => 'firemní prezentace',
                    'casOd' => '10:45',
                    'casDo' => '11:00',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ],
                [
                    'publikovano' => '1',
                    'jmeno' => 'Petra Součková',
                    'funkce' => 'Grafia',
                    'nazevPrednasky' => 'Jak oslovit zaměstnavatele a jak se připravit na pracovní pohovor',
                    'perex' => 'Co má a nemá obsahovat životopis, co psát do motivačního dopisu, abyste zaujali. Nejčastější chyby při pracovním pohovoru a jak se jich vyvarovat.',
                    'casOd' => '11:15',
                    'casDo' => '11:45',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ],
                [
                    'publikovano' => '1',
                    'jmeno' => '',
                    'funkce' => 'Grafia a UMO3 Plzeň',
                    'nazevPrednasky' => 'Vyhlášení cen Mamma Parent Friendly',
                    'perex' => 'Ocenění pro podniky přátelské rodině za rok 2020',
                    'casOd' => '13:00',
                    'casDo' => '13:30',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ],
                [
                    'publikovano' => '1',
                    'jmeno' => 'Světlana Skalová',
                    'funkce' => 'ÚP ČR, Krajská pobočka v Plzni',
                    'nazevPrednasky' => 'Zvolená rekvalifikace zdarma – cesta k nové profesi',
                    'perex' => 'Co jsou „zvolené rekvalifikace“, proč se stát „zájemcem o zaměstnání“, nové projekty OUTPLACEMENT a FLEXI',
                    'casOd' => '13:30',
                    'casDo' => '13:55',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ],
                [
                    'publikovano' => '1',
                    'jmeno' => 'David Brabec',
                    'funkce' => 'Grafia',
                    'nazevPrednasky' => 'Nástup do práce po rodičovské dovolené? Bomba!',
                    'perex' => 'Projekt Moje budoucnost vám pomůže s motivací, rekvalifikací, PC dovednostmi, jazyky i s vlastním hledáním nové práce.',
                    'casOd' => '14:10',
                    'casDo' => '14:30',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ],
                [
                    'publikovano' => '1',
                    'jmeno' => 'Markéta Vondrová',
                    'funkce' => 'EURES',
                    'nazevPrednasky' => 'Možnosti zaměstnání v zahraničí',
                    'perex' => 'Chcete vycestovat za prací do zahraničí? EURES poradí, jak na to.',
                    'casOd' => '14:35',
                    'casDo' => '15:00',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ],
                [
                    'publikovano' => '1',
                    'jmeno' => 'Milan Edl',
                    'funkce' => 'ZČU v Plzni',
                    'nazevPrednasky' => 'Jaká je budoucnost absolventů VŠ?',
                    'perex' => 'rozhovor s děkanem FST ZČU o perspektivách studijních oborů',
                    'casOd' => '15:15',
                    'casDo' => '15:35',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ],
                [
                    'publikovano' => '1',
                    'jmeno' => 'Vendula Linková, Martin Junek',
                    'funkce' => 'Konplan, s.r.o.',
                    'nazevPrednasky' => 'Konplan – firma budoucnosti',
                    'perex' => 'prezentace společnosti',
                    'casOd' => '15:40',
                    'casDo' => '15:55',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ],
                [
                    'publikovano' => '1',
                    'jmeno' => 'Milan Kavka',
                    'funkce' => 'CzechInvest',
                    'nazevPrednasky' => 'Jak nastartovat svůj vlastní business?',
                    'perex' => 'Nehledáte zaměstnání, ale chcete začít podnikat? Máte nápad na start up? Chcete se vyhnout problémům?',
                    'casOd' => '15:55',
                    'casDo' => '16:30',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ],
                [
                    'publikovano' => '1',
                    'jmeno' => 'Stanislav Šec',
                    'funkce' => 'UMO3 Plzeň',
                    'nazevPrednasky' => 'Vzdělávací, rodinné a sociální programy pro občany v Plzni',
                    'perex' => 'Co dělat v těžkých životních událostech? Jak být úspěšným rodičem? Poradenství pro rodiny, samoživitele a odborné sociální služby',
                    'casOd' => '16:45',
                    'casDo' => '17:05',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ],
                [
                    'publikovano' => '1',
                    'jmeno' => 'Martin Fojtíček',
                    'funkce' => 'Ledovec',
                    'nazevPrednasky' => 'Jak se nezbláznit z covidu?',
                    'perex' => 'Doléhá na vás „domácí vězení“? Necítíte se dobře? Jste naštvaní nebo apatičtí? Umíme pomáhat!',
                    'casOd' => '17:10',
                    'casDo' => '17:55',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ],
            ],
        ],
        [
            'datum' => '31. 3. 2021',
            'prednasky' => [
                [
                    'publikovano' => '1',
                    'jmeno' => 'Marcel Gondorčín',
                    'funkce' => 'Pakt zaměstnanosti Plzeňského kraje',
                    'nazevPrednasky' => 'Budoucnost profesí v Plzeňském kraji',
                    'perex' => 'Data, grafy, predikce… Jakým směrem se vydat, abyste v našem kraji měli perspektivu zaměstnání.',
                    'casOd' => '10:05',
                    'casDo' => '10:25',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ],
                [
                    'publikovano' => '1',
                    'jmeno' => 'Milan Edl',
                    'funkce' => 'FST ZČU',
                    'nazevPrednasky' => 'Jakou VŠ zvolit, aby se o vás personalisté prali?',
                    'perex' => 'Přednáška děkana FST ZČU ',
                    'casOd' => '10:45',
                    'casDo' => '11:10',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ],
                [
                    'publikovano' => '1',
                    'jmeno' => 'Ondřej Ženíšek',
                    'funkce' => 'UMO 3 Plzeň',
                    'nazevPrednasky' => 'Péče o válečného veterána byl skvělý jazykový kurz',
                    'perex' => 'Interview s místostarostou',
                    'casOd' => '11:10',
                    'casDo' => '11:30',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ],
                [
                    'publikovano' => '1',
                    'jmeno' => '',
                    'funkce' => 'Grafia',
                    'nazevPrednasky' => 'Domluvte se ve své profesi anglicky/německy a učte se za peníze EU! ',
                    'perex' => 'Chcete studovat jazyky a kurzy jsou drahé? Poradíme, kde studovat bezplatně!',
                    'casOd' => '11:30',
                    'casDo' => '11:45',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ],
                [
                    'publikovano' => '1',
                    'jmeno' => 'Bc. Vlasta Faiferlíková',
                    'funkce' => 'Regionální dobrovolnické centrum Plzeňského kraje Totem',
                    'nazevPrednasky' => 'Chcete se stát dobrovolníkem?',
                    'perex' => 'Dobrovolnictví podporuje druhé a dá vašemu životu nový smysl',
                    'casOd' => '13:00',
                    'casDo' => '13:20',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ],
                [
                    'publikovano' => '1',
                    'jmeno' => 'Ilona Jehličková',
                    'funkce' => 'UMO 1',
                    'nazevPrednasky' => 'Dobrovolníkem na vlastní kůži',
                    'perex' => 'Jak skloubit dobrovolnictví s prací místostarostky? Co nového jsem se o sobě i o druhých naučila…',
                    'casOd' => '13:20',
                    'casDo' => '13:30',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ],
                [
                    'publikovano' => '1',
                    'jmeno' => '',
                    'funkce' => '',
                    'nazevPrednasky' => 'Diecézní charita',
                    'perex' => 'prezentace služeb',
                    'casOd' => '13:30',
                    'casDo' => '13:35',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ],
                [
                    'publikovano' => '1',
                    'jmeno' => 'Karel Zahut, Markéta Fialová',
                    'funkce' => 'Společná přednáška MP a PČR',
                    'nazevPrednasky' => 'Zásady bezpečného chování',
                    'perex' => 'Zajištění majetku, života a zdraví, jak se nenechat napálit… Projekt Zabezpečte se',
                    'casOd' => '13:55',
                    'casDo' => '14:35',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ],
                [
                    'publikovano' => '1',
                    'jmeno' => ' Petr Baloun',
                    'funkce' => 'UMO 3 Plzeň',
                    'nazevPrednasky' => 'Nespalte se při koupi a prodeji nemovitosti',
                    'perex' => 'Jaká úskalí vás čekají, čeho se vyvarovat, co zjistit předem?',
                    'casOd' => '14:35',
                    'casDo' => '14:55',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ],
                [
                    'publikovano' => '1',
                    'jmeno' => ' Martin Fojtíček',
                    'funkce' => 'Ledovec',
                    'nazevPrednasky' => 'Jak se pozná, že už jsem se zbláznil?',
                    'perex' => '',
                    'casOd' => '14:55',
                    'casDo' => '15:35',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ],
                [
                    'publikovano' => '1',
                    'jmeno' => ' Pavlína Stoklásková',
                    'funkce' => 'Optika Klatovy',
                    'nazevPrednasky' => 'Brýle – doplněk osobnosti i pracovní nástroj',
                    'perex' => 'Jak brýle ovlivní pracovní výkon i akceptaci okolím? Srozumitelně od optika',
                    'casOd' => '15:35',
                    'casDo' => '15:55',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ],





                [
                    'publikovano' => '0',
                    'jmeno' => '',
                    'funkce' => '',
                    'nazevPrednasky' => 'Na co dát pozor při uzavírání pracovní smlouvy?',
                    'perex' => 'Co musí a nemusí být v pracovní smlouvě a proč? Užitečné tipy a rady pro vás.',
                    'casOd' => '',
                    'casDo' => '',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ],
                [
                    'publikovano' => '0',
                    'jmeno' => '',
                    'funkce' => '',
                    'nazevPrednasky' => 'Jsem z tý školy blázen?',
                    'perex' => 'Jak poznat, že potřebuji pomoc…',
                    'casOd' => '',
                    'casDo' => '',
                    'odkazPrednaskyAttributes' => $odkazPrednaskyAttributes +
                    [
                        'href' => '',
                        'target' => ''
                    ],
                    'odkazPrednaskyText' => $odkazPrednaskyTextPrihlasitSe
                ],
            ],

        ],

    ]
?>
<div class="paper">
    <headline>
        <?php include "headline.php" ?>
    </headline>
    <perex>
        <?php include "perex.php" ?>
    </perex>
    <content>
         <?= $this->repeat(__DIR__.'/content/prednasky.php', $prednaska) ?>
    </content>
</div>