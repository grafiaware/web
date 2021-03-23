<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */

$linkButtonAttributes = ['class' => 'ui large red button'];
$linkButtonTextPrihlasitSe = 'Zde se budete moci přihlásit';
$linkButtonTextZhlednout = 'Zhlédnout záznam';

$eventType = [
    'Přednáška' => ['name'=>'Přednáška', 'icon'=>'chalkboard teacher icon'],
    'Pohovor'=> ['name'=>'Pohovor', 'icon'=> 'microphone icon'],
    'Poradna' => ['name'=>'Poradna', 'icon'=> 'user friends icon'],
];

$event = [
    [
        'timelinePoint' => '30. 3. 2021',
        'box' => [
            [
                'published' => 1,
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'title' => 'Pracovně právní problematika v době covidu',
                'perex' => 'Výpovědi, překážky v práci, náhrada mzdy, náležitosti pracovní smlouvy…',
                'startTime' => '10:05',
                'endTime' => '10:40',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'institution' => ['type'=>'', 'name'=>'Státní úřad inspekce práce'],
                'party' => 'Otto Slabý'
            ],
            [
                'published' => 1,
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'title' => 'Wienerberger',
                'perex' => 'firemní prezentace',
                'startTime' => '10:45',
                'endTime' => '11:00',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'institution' => ['type'=>'', 'name'=>'Wienerberger'],
                'party' => ''
            ],
            [
                'published' => 1,
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'title' => 'Daikin',
                'perex' => 'firemní prezentace',
                'startTime' => '11:00',
                'endTime' => '11:15',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'institution' => ['type'=>'', 'name'=>'Daikin'],
                'party' => ''
            ],
            [
                'published' => 1,
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'title' => 'Jak oslovit zaměstnavatele a jak se připravit na pracovní pohovor',
                'perex' => 'Co má a nemá obsahovat životopis, co psát do motivačního dopisu, abyste zaujali. Nejčastější chyby při pracovním pohovoru a jak se jich vyvarovat.',
                'startTime' => '11:15',
                'endTime' => '11:45',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'institution' => ['type'=>'', 'name'=>'Grafia'],
                'party' => 'Petra Součková'
            ],
            [
                'published' => 1,
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'title' => 'Vyhlášení cen Mamma Parent Friendly',
                'perex' => 'Ocenění pro podniky přátelské rodině za rok 2020',
                'startTime' => '13:00',
                'endTime' => '13:30',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'institution' => ['type'=>'', 'name'=>'Grafia a ÚMO 3 Plzeň'],
                'party' => ''
            ],
            [
                'published' => 1,
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'title' => 'Zvolená rekvalifikace zdarma – cesta k nové profesi',
                'perex' => 'Co jsou „zvolené rekvalifikace“, proč se stát „zájemcem o zaměstnání“, nové projekty OUTPLACEMENT a FLEXI',
                'startTime' => '13:30',
                'endTime' => '13:55',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'institution' => ['type'=>'', 'name'=>'ÚP ČR, Krajská pobočka v Plzni'],
                'party' => 'Světlana Skalová'
            ],
            [
                'published' => 1,
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'title' => 'Nástup do práce po rodičovské dovolené? Bomba!',
                'perex' => 'Projekt Moje budoucnost vám pomůže s motivací, rekvalifikací, PC dovednostmi, jazyky i s vlastním hledáním nové práce.',
                'startTime' => '14:10',
                'endTime' => '14:30',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'institution' => ['type'=>'', 'name'=>'Grafia'],
                'party' => 'David Brabec'
            ],
            [
                'published' => 1,
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'title' => 'Možnosti zaměstnání v zahraničí',
                'perex' => 'Chcete vycestovat za prací do zahraničí? EURES poradí, jak na to.',
                'startTime' => '14:35',
                'endTime' => '15:00',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'institution' => ['type'=>'', 'name'=>'EURES'],
                'party' => 'Markéta Vondrová'
            ],
############################################################################
            [
                'published' => 1,
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'title' => 'Possehl Electronics Czech Republic',
                'perex' => 'firemní prezentace',
                'startTime' => '15:00',
                'endTime' => '15:15',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'institution' => ['type'=>'', 'name'=>'Possehl Electronics Czech Republic'],
                'party' => ''
            ],
            [
                'published' => 1,
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'title' => 'Jaká je budoucnost absolventů VŠ?',
                'perex' => 'rozhovor s děkanem FST ZČU o perspektivách studijních oborů',
                'startTime' => '15:15',
                'endTime' => '15:35',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'institution' => ['type'=>'', 'name'=>'ZČU v Plzni'],
                'party' => 'Milan Edl'
            ],
            [
                'published' => 1,
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'title' => 'Konplan – firma budoucnosti',
                'perex' => 'prezentace společnosti',
                'startTime' => '15:40',
                'endTime' => '15:55',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'institution' => ['type'=>'', 'name'=>'Konplan'],
                'party' => 'Vendula Linková, Martin Junek'
            ],
            [
                'published' => 1,
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'title' => 'Jak nastartovat svůj vlastní business?',
                'perex' => 'Nehledáte zaměstnání, ale chcete začít podnikat? Máte nápad na start up? Chcete se vyhnout problémům?',
                'startTime' => '15:55',
                'endTime' => '16:30',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'institution' => ['type'=>'', 'name'=>'CzechInvest'],
                'party' => 'Milan Kavka'
            ],
            [
                'published' => 1,
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'title' => 'Vzdělávací, rodinné a sociální programy pro občany v Plzni',
                'perex' => 'Co dělat v těžkých životních událostech? Jak být úspěšným rodičem? Poradenství pro rodiny, samoživitele a odborné sociální služby',
                'startTime' => '16:45',
                'endTime' => '17:05',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'institution' => ['type'=>'', 'name'=>'ÚMO 3 Plzeň'],
                'party' => 'Stanislav Šec'
            ],
            [
                'published' => 1,
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'title' => 'Jak se nezbláznit z covidu?',
                'perex' => 'Doléhá na vás „domácí vězení“? Necítíte se dobře? Jste naštvaní nebo apatičtí? Umíme pomáhat!',
                'startTime' => '17:10',
                'endTime' => '17:55',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'institution' => ['type'=>'', 'name'=>'Ledovec'],
                'party' => 'Martin Fojtíček'
            ],
        ],
    ],
    [
        'timelinePoint' => '31. 3. 2021',
        'box' => [
            [
                'published' => 1,
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'title' => 'Budoucnost profesí v Plzeňském kraji',
                'perex' => 'Data, grafy, predikce… Jakým směrem se vydat, abyste v našem kraji měli perspektivu zaměstnání.',
                'startTime' => '10:05',
                'endTime' => '10:25',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'institution' => ['type'=>'', 'name'=>'Pakt zaměstnanosti Plzeňského kraje'],
                'party' => 'Marcel Gondorčín'
            ],
            [
                'published' => 1,
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'title' => 'AKKA Czech Republic',
                'perex' => 'firemní prezentace',
                'startTime' => '10:30',
                'endTime' => '10:45',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'institution' => ['type'=>'', 'name'=>'AKKA Czech Republic'],
                'party' => ''
            ],
            [
                'published' => 1,
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'title' => 'Jakou VŠ zvolit, aby se o vás personalisté prali?',
                'perex' => 'Přednáška děkana FST ZČU ',
                'startTime' => '10:45',
                'endTime' => '11:10',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'institution' => ['type'=>'', 'name'=>'FST ZČU'],
                'party' => 'Milan Edl'
            ],
            [
                'published' => 1,
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'title' => 'Péče o válečného veterána byl skvělý jazykový kurz',
                'perex' => 'Interview s místostarostou',
                'startTime' => '11:10',
                'endTime' => '11:30',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'institution' => ['type'=>'', 'name'=>'ÚMO 3 Plzeň'],
                'party' => 'Ondřej Ženíšek'
            ],
            [
                'published' => 1,
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'title' => 'Domluvte se ve své profesi anglicky/německy a učte se za peníze EU! ',
                'perex' => 'Chcete studovat jazyky a kurzy jsou drahé? Poradíme, kde studovat bezplatně!',
                'startTime' => '11:30',
                'endTime' => '11:45',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'institution' => ['type'=>'', 'name'=>'Grafia'],
                'party' => ''
            ],
            [
                'published' => 1,
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'title' => 'Chcete se stát dobrovolníkem?',
                'perex' => 'Dobrovolnictví podporuje druhé a dá vašemu životu nový smysl',
                'startTime' => '13:00',
                'endTime' => '13:20',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'institution' => ['type'=>'', 'name'=>'Regionální dobrovolnické centrum Plzeňského kraje Totem'],
                'party' => 'Vlasta Faiferlíková'
            ],
            [
                'published' => 1,
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'title' => 'Dobrovolníkem na vlastní kůži',
                'perex' => 'Jak skloubit dobrovolnictví s prací místostarostky? Co nového jsem se o sobě i o druhých naučila…',
                'startTime' => '13:20',
                'endTime' => '13:30',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'institution' => ['type'=>'', 'name'=>'ÚMO 1 Plzeň'],
                'party' => 'Ilona Jehličková'
            ],
            [
                'published' => 1,
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'title' => 'Diecézní charita',
                'perex' => 'prezentace služeb',
                'startTime' => '13:30',
                'endTime' => '13:35',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'institution' => ['type'=>'', 'name'=>''],
                'party' => ''
            ],
            [
                'published' => 1,
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'title' => 'Zásady bezpečného chování',
                'perex' => 'Zajištění majetku, života a zdraví, jak se nenechat napálit… Projekt Zabezpečte se',
                'startTime' => '13:55',
                'endTime' => '14:35',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'institution' => ['type'=>'', 'name'=>'Společná přednáška MP a PČR'],
                'party' => 'Karel Zahut, Markéta Fialová'
            ],
            [
                'published' => 1,
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'title' => 'Nespalte se při koupi a prodeji nemovitosti',
                'perex' => 'Jaká úskalí vás čekají, čeho se vyvarovat, co zjistit předem?',
                'startTime' => '14:35',
                'endTime' => '14:55',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'institution' => ['type'=>'', 'name'=>'ÚMO 3 Plzeň'],
                'party' => ' Petr Baloun'
            ],
            [
                'published' => 1,
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'title' => 'Jak se pozná, že už jsem se zbláznil?',
                'perex' => '',
                'startTime' => '14:55',
                'endTime' => '15:35',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'institution' => ['type'=>'', 'name'=>'Ledovec'],
                'party' => ' Martin Fojtíček'
            ],
            [
                'published' => 0,
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'title' => 'Brýle – doplněk osobnosti i pracovní nástroj',
                'perex' => 'Jak brýle ovlivní pracovní výkon i akceptaci okolím? Srozumitelně od optika',
                'startTime' => '15:35',
                'endTime' => '15:55',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'institution' => ['type'=>'', 'name'=>'Optika Klatovy'],
                'party' => ' Pavlína Stoklásková'
            ],





            [
                'published' => '0',
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'title' => 'Na co dát pozor při uzavírání pracovní smlouvy?',
                'perex' => 'Co musí a nemusí být v pracovní smlouvě a proč? Užitečné tipy a rady pro vás.',
                'startTime' => '',
                'endTime' => '',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'institution' => ['type'=>'', 'name'=>''],
                'party' => '',
            ],
            [
                'published' => '0',
                'boxClass' => 'box-left',
                'eventType' => $eventType['Přednáška'],
                'title' => 'Jsem z tý školy blázen?',
                'perex' => 'Jak poznat, že potřebuji pomoc…',
                'startTime' => '',
                'endTime' => '',
                'linkButton' => [
                                'show' => 0,
                                'linkButtonAttributes' => $linkButtonAttributes +
                                    [
                                        'href' => '',
                                        'target' => ''
                                    ],
                                'linkButtonText' => $linkButtonTextPrihlasitSe
                                ],
                'institution' => ['type'=>'', 'name'=>''],
                'party' => '',
            ],
        ],

    ],

];


