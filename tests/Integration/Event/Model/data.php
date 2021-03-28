<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

$headline = 'Můžete se těšit na tyto přednášky';
$perex =
    '
Další přednášky průběžně doplňujeme, koukněte sem za pár dnů!

Přednášky můžete i opakovaně zhlédnout na našem youtube kanálu. Odkaz na youtube kanál zde najdete po 28. 3. 2021';
$footer = 'Další přednášky budou postupně přibývat, sledujte tuto stránku!';


$linkButtonAttributes = ['class' => 'ui large red button'];
$linkButtonTextPrihlasitSe = 'Zde se budete moci přihlásit';
$linkButtonTextZhlednout = 'Zhlédnout záznam';

$eventType = [
    'Přednáška' => ['name'=>'Přednáška', 'icon'=>'chalkboard teacher icon'],
    'Pohovor'=> ['name'=>'Pohovor', 'icon'=> 'microphone icon'],
    'Poradna' => ['name'=>'Poradna', 'icon'=> 'user friends icon'],
];

$eventContent =
    [
    'Pracovně právní problematika v době covidu' => [
        'eventType' => $eventType['Přednáška'],
        'title' => 'Pracovně právní problematika v době covidu',
        'perex' => 'Výpovědi, překážky v práci, náhrada mzdy, náležitosti pracovní smlouvy…',
        'institution' => ['type'=>'', 'name'=>'Státní úřad inspekce práce'],
        'party' => 'Otto Slabý'
        ],
    'Wienerberger' => [
        'eventType' => $eventType['Přednáška'],
                'title' => 'Wienerberger',
                'perex' => 'Firemní prezentace',
                'institution' => ['type'=>'', 'name'=>'Wienerberger'],
                'party' => 'Barbora Krejčová'
        ],
    'Daikin' => [
        'eventType' => $eventType['Přednáška'],
                'title' => 'Daikin',
                'perex' => 'Firemní prezentace: Svěží vzduch pro tvojí kariéru! ',
                'institution' => ['type'=>'', 'name'=>'Daikin'],
                'party' => 'Tomáš Matoušek'
        ],
    'Jak oslovit zaměstnavatele a jak se připravit na pracovní pohovor' => [
        'eventType' => $eventType['Přednáška'],
                'title' => 'Jak oslovit zaměstnavatele a jak se připravit na pracovní pohovor',
                'perex' => 'Co má a nemá obsahovat životopis, co psát do motivačního dopisu, abyste zaujali. Nejčastější chyby při pracovním pohovoru a jak se jich vyvarovat.',
                'institution' => ['type'=>'', 'name'=>'Grafia'],
                'party' => 'Petra Součková'
        ],
    'Vyhlášení cen Mamma Parent Friendly' => [
        'eventType' => $eventType['Přednáška'],
                'title' => 'Vyhlášení cen Mamma Parent Friendly',
                'perex' => 'Ocenění pro podniky přátelské rodině za rok 2020',
                'institution' => ['type'=>'', 'name'=>'Grafia a MO Plzeň 3'],
                'party' => ''
        ],
    'Zvolená rekvalifikace zdarma – cesta k nové profesi' => [
        'eventType' => $eventType['Přednáška'],
                'title' => 'Zvolená rekvalifikace zdarma – cesta k nové profesi',
                'perex' => 'Co jsou „zvolené rekvalifikace“, proč se stát „zájemcem o zaměstnání“, nové projekty OUTPLACEMENT a FLEXI',
                'institution' => ['type'=>'', 'name'=>'ÚP ČR, Krajská pobočka v Plzni'],
                'party' => 'Světlana Skalová'
        ],
    'Kermi' => [
        'eventType' => $eventType['Přednáška'],
                'title' => 'Kermi',
                'perex' => 'Firemní prezentace: „Jsme tu s vámi, již 25 let, přidejte se do našeho týmu“',
                'institution' => ['type'=>'', 'name'=>'Kermi'],
                'party' => 'Jana Čedíková'
        ],
   'Nástup do práce po rodičovské dovolené? Bomba!' => [
        'eventType' => $eventType['Přednáška'],
                'title' => 'Nástup do práce po rodičovské dovolené? Bomba!',
                'perex' => 'Projekt Moje budoucnost vám pomůže s motivací, rekvalifikací, PC dovednostmi, jazyky i s vlastním hledáním nové práce.',
                'institution' => ['type'=>'', 'name'=>'Grafia'],
                'party' => 'David Brabec'
        ],
    'Možnosti zaměstnání v zahraničí' => [
        'eventType' => $eventType['Přednáška'],
                'title' => 'Možnosti zaměstnání v zahraničí',
                'perex' => 'Chcete vycestovat za prací do zahraničí? EURES poradí, jak na to.',
                'institution' => ['type'=>'', 'name'=>'EURES'],
                'party' => 'Markéta Vondrová'
        ],

    'Possehl Electronics Czech Republic' => [
        'eventType' => $eventType['Přednáška'],
                'title' => 'Possehl Electronics Czech Republic',
                'perex' => 'Firemní prezentace',
                'institution' => ['type'=>'', 'name'=>'Possehl Electronics Czech Republic'],
                'party' => 'Michail Rais'
        ],
    'Jaká je budoucnost absolventů VŠ?' => [
        'eventType' => $eventType['Přednáška'],
            'title' => 'Jaká je budoucnost absolventů VŠ?',
            'perex' => 'Rozhovor s děkanem FST ZČU o perspektivách studijních oborů.',
            'institution' => ['type'=>'', 'name'=>'ZČU v Plzni'],
            'party' => 'Milan Edl'
        ],
    'Konplan – firma budoucnosti' => [
        'eventType' => $eventType['Přednáška'],
            'title' => 'Konplan – firma budoucnosti',
            'perex' => 'Firemní prezentace',
            'institution' => ['type'=>'', 'name'=>'Konplan'],
            'party' => 'Vendula Linková, Martin Junek'
        ],
    'Jak nastartovat svůj vlastní business?' => [
        'eventType' => $eventType['Přednáška'],
            'title' => 'Jak nastartovat svůj vlastní business?',
            'perex' => 'Nehledáte zaměstnání, ale chcete začít podnikat? Máte nápad na start up? Chcete se vyhnout problémům?',
            'institution' => ['type'=>'', 'name'=>'CzechInvest'],
            'party' => 'Milan Kavka'
        ],
    'Vzdělávací, rodinné a sociální programy pro občany v Plzni' => [
        'eventType' => $eventType['Přednáška'],
            'title' => 'Vzdělávací, rodinné a sociální programy pro občany v Plzni',
            'perex' => 'Co dělat v těžkých životních událostech? Jak být úspěšným rodičem? Poradenství pro rodiny, samoživitele a odborné sociální služby',
            'institution' => ['type'=>'', 'name'=>'MO Plzeň 3'],
            'party' => 'Stanislav Šec'
        ],
    'Jak se nezbláznit z covidu?' => [
        'eventType' => $eventType['Přednáška'],
            'title' => 'Jak se nezbláznit z covidu?',
            'perex' => 'Doléhá na vás „domácí vězení“? Necítíte se dobře? Jste naštvaní nebo apatičtí? Umíme pomáhat!',
            'institution' => ['type'=>'', 'name'=>'Ledovec'],
            'party' => 'Martin Fojtíček'
        ],
    'Budoucnost profesí v Plzeňském kraji' => [
        'eventType' => $eventType['Přednáška'],
            'title' => 'Budoucnost profesí v Plzeňském kraji',
            'perex' => 'Data, grafy, predikce… Jakým směrem se vydat, abyste v našem kraji měli perspektivu zaměstnání.',
            'institution' => ['type'=>'', 'name'=>'Pakt zaměstnanosti Plzeňského kraje'],
            'party' => 'Marcel Gondorčín'
        ],
    'AKKA Czech Republic' => [
        'eventType' => $eventType['Přednáška'],
            'title' => 'AKKA Czech Republic',
            'perex' => 'Firemní prezentace: Nahlédněte s námi do Prototypového centra v Plzni',
            'institution' => ['type'=>'', 'name'=>'AKKA Czech Republic'],
            'party' => 'Štefan Bárta'
        ],
    'Jakou VŠ zvolit, aby se o vás personalisté prali?' => [
        'eventType' => $eventType['Přednáška'],
            'title' => 'Jakou VŠ zvolit, aby se o vás personalisté prali?',
            'perex' => 'Přednáška děkana FST ZČU ',
            'institution' => ['type'=>'', 'name'=>'FST ZČU'],
            'party' => 'Milan Edl'
        ],
    'Péče o válečného veterána byl skvělý jazykový kurz' => [
        'eventType' => $eventType['Přednáška'],
            'title' => 'Péče o válečného veterána byl skvělý jazykový kurz',
            'perex' => 'Interview s místostarostou',
            'institution' => ['type'=>'', 'name'=>'MO Plzeň 3'],
            'party' => 'Ondřej Ženíšek'
        ],
    'Domluvte se ve své profesi anglicky/německy a učte se za peníze EU! ' => [
        'eventType' => $eventType['Přednáška'],
            'title' => 'Domluvte se ve své profesi anglicky/německy a učte se za peníze EU! ',
            'perex' => 'Chcete studovat jazyky a kurzy jsou drahé? Poradíme, kde studovat bezplatně!',
            'institution' => ['type'=>'', 'name'=>'Grafia'],
            'party' => ''
        ],
    'Valeo Autoklimatizace' => [
        'eventType' => $eventType['Přednáška'],
            'title' => 'Valeo Autoklimatizace',
            'perex' => 'Firemní prezentace',
            'institution' => ['type'=>'', 'name'=>'Valeo Autoklimatizace'],
            'party' => ''
        ],
    'Chcete se stát dobrovolníkem?' => [
        'eventType' => $eventType['Přednáška'],
            'title' => 'Chcete se stát dobrovolníkem?',
            'perex' => 'Dobrovolnictví podporuje druhé a dá vašemu životu nový smysl',
            'institution' => ['type'=>'', 'name'=>'Regionální dobrovolnické centrum Plzeňského kraje Totem'],
            'party' => 'Vlasta Faiferlíková'
        ],
    'Dobrovolníkem na vlastní kůži' => [
        'eventType' => $eventType['Přednáška'],
            'title' => 'Dobrovolníkem na vlastní kůži',
            'perex' => 'Jak skloubit dobrovolnictví s prací místostarostky? Co nového jsem se o sobě i o druhých naučila…',
            'institution' => ['type'=>'', 'name'=>'MO Plzeň 1'],
            'party' => 'Ilona Jehličková'
        ],
    'Diecézní charita' => [
        'eventType' => $eventType['Přednáška'],
            'title' => 'Diecézní charita',
            'perex' => 'prezentace služeb',
            'institution' => ['type'=>'', 'name'=>''],
            'party' => ''
        ],
    'Zásady bezpečného chování' => [
        'eventType' => $eventType['Přednáška'],
            'title' => 'Zásady bezpečného chování',
            'perex' => 'Zajištění majetku, života a zdraví, jak se nenechat napálit… Projekt Zabezpečte se',
            'institution' => ['type'=>'', 'name'=>'Společná přednáška MP a PČR'],
            'party' => 'Karel Zahut, Markéta Fialová'
        ],
    'Nespalte se při koupi a prodeji nemovitosti' => [
        'eventType' => $eventType['Přednáška'],
            'title' => 'Nespalte se při koupi a prodeji nemovitosti',
            'perex' => 'Jaká úskalí vás čekají, čeho se vyvarovat, co zjistit předem?',
            'institution' => ['type'=>'', 'name'=>'MO Plzeň 3'],
            'party' => ' Petr Baloun'
        ],

    'Jak se pozná, že už jsem se zbláznil?' => [
        'eventType' => $eventType['Přednáška'],
            'title' => 'Jak se pozná, že už jsem se zbláznil?',
            'perex' => '',
            'institution' => ['type'=>'', 'name'=>'Ledovec'],
            'party' => ' Martin Fojtíček'
        ],
     'Brýle – doplněk osobnosti i pracovní nástroj' => [
        'eventType' => $eventType['Přednáška'],
            'title' => 'Brýle – doplněk osobnosti i pracovní nástroj',
            'perex' => 'Jak brýle ovlivní pracovní výkon i akceptaci okolím? Srozumitelně od optika',
            'institution' => ['type'=>'', 'name'=>'Optika Klatovy'],
            'party' => ' Pavlína Stoklásková'
        ],
     'Jak si uhlídat své peníze?' => [
        'eventType' => $eventType['Přednáška'],
            'title' => 'Jak si uhlídat své peníze?',
            'perex' => 'Finanční gramotnost pro každého',
            'institution' => ['type'=>'', 'name'=>'Grafia'],
            'party' => ''
        ],
     'Home office – dobrý pomocník, ale zlý pán' => [
        'eventType' => $eventType['Přednáška'],
            'title' => 'Home office – dobrý pomocník, ale zlý pán',
            'perex' => 'Jsme na home office výkonnější? Jak se podílí home office na naší náladě? Co potřebují zaměstnanci, aby se nezbláznili?',
            'institution' => ['type'=>'', 'name'=>'Grafia a Konplan'],
            'party' => 'Jana Brabcová, Vendula Linková'
        ],
     'K čemu je nám národní soustava kvalifikací?' => [
        'eventType' => $eventType['Přednáška'],
            'title' => 'K čemu je nám národní soustava kvalifikací?',
            'perex' => 'Víte, že můžete získat kvalifikaci, aniž byste léta studovali ve škole? Umíte svůj obor, jen na to nemáte „papír“? Pak jste tu správně!',
            'institution' => ['type'=>'', 'name'=>'NPI'],
            'party' => 'Veronika Menčíková, Robert Gamba'
        ],
     'Ke kariérovému poradci na preventivní prohlídku' => [
        'eventType' => $eventType['Přednáška'],
            'title' => 'Ke kariérovému poradci na preventivní prohlídku',
            'perex' => 'Co je kariérové poradenství a proč navštěvovat kariérového poradce, i když práci právě nehledám?',
            'institution' => ['type'=>'', 'name'=>'Grafia'],
            'party' => 'Štěpánka Pirnosová'
        ],
     'Jak uvažuje personalista - náborář?' => [
        'eventType' => $eventType['Přednáška'],
            'title' => 'Jak uvažuje personalista - náborář?',
            'perex' => 'Nahlédněte do tajů výběrových řízení a zjistěte, co rozhoduje o (ne)přijetí na pracovní pozici! Diskuse s náborovými pracovníky ČEZ a Assa Abloy',
            'institution' => ['type'=>'', 'name'=>'Grafia'],
            'party' => ''
        ],
     'Zacvičte si s Krašovskou!' => [
        'eventType' => $eventType['Přednáška'],
            'title' => 'Zacvičte si s Krašovskou!',
            'perex' => '',
            'institution' => ['type'=>'', 'name'=>'Krašovská Aktivity centrum'],
            'party' => ''
        ],

    // not published
        'Na co dát pozor při uzavírání pracovní smlouvy?' => [
            'eventType' => $eventType['Přednáška'],
                'title' => 'Na co dát pozor při uzavírání pracovní smlouvy?',
                'perex' => 'Co musí a nemusí být v pracovní smlouvě a proč? Užitečné tipy a rady pro vás.',
                'institution' => ['type'=>'', 'name'=>''],
                'party' => '',
            ],
        'Jsem z tý školy blázen?' => [
            'eventType' => $eventType['Přednáška'],
                'title' => 'Jsem z tý školy blázen?',
                'perex' => 'Jak poznat, že potřebuji pomoc…',
                'institution' => ['type'=>'', 'name'=>''],
                'party' => '',
            ],


    ];

$timelinePoint = [
        1 => '30. 3. 2021',
        2 => '31. 3. 2021',
        3 => '1. 4. 2021',
];

$eventList =
    [
        [

            'published' => '1',
            'timelinePoint' => '30. 3. 2021',
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
        ] + $eventContent['Pracovně právní problematika v době covidu'],
        [
            'published' => '1',
            'timelinePoint' => '30. 3. 2021',
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
        ] + $eventContent['Wienerberger'],
        [
            'published' => '1',
            'timelinePoint' => '30. 3. 2021',
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
        ] + $eventContent['Daikin'],
        [
            'published' => '1',
            'timelinePoint' => '30. 3. 2021',
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
        ] + $eventContent['Jak oslovit zaměstnavatele a jak se připravit na pracovní pohovor'],
        [
            'published' => '1',
            'timelinePoint' => '30. 3. 2021',
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
        ] + $eventContent['Vyhlášení cen Mamma Parent Friendly'],
        [
            'published' => '1',
            'timelinePoint' => '30. 3. 2021',
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
        ] + $eventContent['Zvolená rekvalifikace zdarma – cesta k nové profesi'],
        [
            'published' => '1',
            'timelinePoint' => '30. 3. 2021',
                'startTime' => '13:55',
                'endTime' => '14:10',
            'linkButton' => [
                            'show' => 0,
                            'linkButtonAttributes' => $linkButtonAttributes +
                                [
                                    'href' => '',
                                    'target' => ''
                                ],
                            'linkButtonText' => $linkButtonTextPrihlasitSe
                            ],
        ] + $eventContent['Kermi'],
        [
            'published' => '1',
            'timelinePoint' => '30. 3. 2021',
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
        ] + $eventContent['Nástup do práce po rodičovské dovolené? Bomba!'],
        [
            'published' => '1',
            'timelinePoint' => '30. 3. 2021',
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
        ] + $eventContent['Možnosti zaměstnání v zahraničí'],
#######################################################################################
        [
            'published' => '1',
            'timelinePoint' => '30. 3. 2021',
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
        ] + $eventContent['Possehl Electronics Czech Republic'],
        [
            'published' => '1',
            'timelinePoint' => '30. 3. 2021',
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
        ] + $eventContent['Jaká je budoucnost absolventů VŠ?'],
        [
            'published' => '1',
            'timelinePoint' => '30. 3. 2021',
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
        ] + $eventContent['Konplan – firma budoucnosti'],
        [
            'published' => '1',
            'timelinePoint' => '30. 3. 2021',
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
        ] + $eventContent['Jak nastartovat svůj vlastní business?'],
        [
            'published' => '1',
            'timelinePoint' => '30. 3. 2021',
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
        ] + $eventContent['Vzdělávací, rodinné a sociální programy pro občany v Plzni'],
        [
            'published' => '1',
            'timelinePoint' => '30. 3. 2021',
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
        ] + $eventContent['Jak se nezbláznit z covidu?'],
        [
            'published' => '1',
            'timelinePoint' => '30. 3. 2021',
                'startTime' => '17:55',
                'endTime' => '18:15',
            'linkButton' => [
                            'show' => 0,
                            'linkButtonAttributes' => $linkButtonAttributes +
                                [
                                    'href' => '',
                                    'target' => ''
                                ],
                            'linkButtonText' => $linkButtonTextPrihlasitSe
                            ],
        ] + $eventContent['Zacvičte si s Krašovskou!'],
        [
            'published' => '1',
            'timelinePoint' => '31. 3. 2021',
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
        ] + $eventContent['Budoucnost profesí v Plzeňském kraji'],

        [
            'published' => '1',
            'timelinePoint' => '31. 3. 2021',
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
        ] + $eventContent['AKKA Czech Republic'],
        [
            'published' => '1',
            'timelinePoint' => '31. 3. 2021',
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
        ] + $eventContent['Jakou VŠ zvolit, aby se o vás personalisté prali?'],
        [
            'published' => '1',
            'timelinePoint' => '31. 3. 2021',
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
        ] + $eventContent['Péče o válečného veterána byl skvělý jazykový kurz'],
        [
            'published' => '1',
            'timelinePoint' => '31. 3. 2021',
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
        ] + $eventContent['Domluvte se ve své profesi anglicky/německy a učte se za peníze EU! '],
        [
            'published' => '1',
            'timelinePoint' => '31. 3. 2021',
                'startTime' => '11:45',
                'endTime' => '12:00',
            'linkButton' => [
                            'show' => 0,
                            'linkButtonAttributes' => $linkButtonAttributes +
                                [
                                    'href' => '',
                                    'target' => ''
                                ],
                            'linkButtonText' => $linkButtonTextPrihlasitSe
                            ],
        ] + $eventContent['Valeo Autoklimatizace'],
        [
            'published' => '1',
            'timelinePoint' => '31. 3. 2021',
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
        ] + $eventContent['Chcete se stát dobrovolníkem?'],
        [
            'published' => '1',
            'timelinePoint' => '31. 3. 2021',
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
        ] + $eventContent['Dobrovolníkem na vlastní kůži'],
        [
            'published' => '1',
            'timelinePoint' => '31. 3. 2021',
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
        ] + $eventContent['Diecézní charita'],
        [
            'published' => '1',
            'timelinePoint' => '31. 3. 2021',
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
        ] + $eventContent['Zásady bezpečného chování'],
        [
            'published' => '1',
            'timelinePoint' => '31. 3. 2021',
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
        ] + $eventContent['Nespalte se při koupi a prodeji nemovitosti'],
        [
            'published' => '1',
            'timelinePoint' => '31. 3. 2021',
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
        ] + $eventContent['Jak se pozná, že už jsem se zbláznil?'],
        [
            'published' => '1',
            'timelinePoint' => '31. 3. 2021',
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
        ] + $eventContent['Zacvičte si s Krašovskou!'],
        [
            'published' => '1',
            'timelinePoint' => '1. 4. 2021',
                'startTime' => '10:05',
                'endTime' => '10:35',
            'linkButton' => [
                            'show' => 0,
                            'linkButtonAttributes' => $linkButtonAttributes +
                                [
                                    'href' => '',
                                    'target' => ''
                                ],
                            'linkButtonText' => $linkButtonTextPrihlasitSe
                            ],
        ] + $eventContent['Jak si uhlídat své peníze?'],
        [
            'published' => '1',
            'timelinePoint' => '1. 4. 2021',
                'startTime' => '10:40',
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
        ] + $eventContent['Home office – dobrý pomocník, ale zlý pán'],
        [
            'published' => '1',
            'timelinePoint' => '1. 4. 2021',
                'startTime' => '11:15',
                'endTime' => '11:35',
            'linkButton' => [
                            'show' => 0,
                            'linkButtonAttributes' => $linkButtonAttributes +
                                [
                                    'href' => '',
                                    'target' => ''
                                ],
                            'linkButtonText' => $linkButtonTextPrihlasitSe
                            ],
        ] + $eventContent['K čemu je nám národní soustava kvalifikací?'],
        [
            'published' => '1',
            'timelinePoint' => '1. 4. 2021',
                'startTime' => '11:40',
                'endTime' => '12:05',
            'linkButton' => [
                            'show' => 0,
                            'linkButtonAttributes' => $linkButtonAttributes +
                                [
                                    'href' => '',
                                    'target' => ''
                                ],
                            'linkButtonText' => $linkButtonTextPrihlasitSe
                            ],
        ] + $eventContent['Ke kariérovému poradci na preventivní prohlídku'],
        [
            'published' => '1',
            'timelinePoint' => '1. 4. 2021',
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
        ] + $eventContent['Jak uvažuje personalista - náborář?'],
        [
            'published' => '1',
            'timelinePoint' => '1. 4. 2021',
                'startTime' => '13:35',
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
        ] + $eventContent['Zacvičte si s Krašovskou!'],

########################
        [
            'published' => '0',
            'timelinePoint' => '31. 3. 2021',
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
        ] + $eventContent['Brýle – doplněk osobnosti i pracovní nástroj'],
        [
            'published' => '0',
            'timelinePoint' => '1. 4. 2021',
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
        ] + $eventContent['Na co dát pozor při uzavírání pracovní smlouvy?'],
        [
            'published' => '0',
            'timelinePoint' => '1. 4. 2021',
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
        ] + $eventContent['Jsem z tý školy blázen?'],


    ];

    $compareByStartTime = function ($boxA, $boxB) {
        return (str_replace(':', '', $boxA['startTime']) < str_replace(':', '', $boxB['startTime'])) ? -1 : 1;
    };

    $eventTypeName = "Přednáška";
    $institutionName = "Ledovec";
    $event = [];
    foreach ($timelinePoint as $tlPoint) {
        $boxItems = [];
        foreach ($eventList as $boxItem) {
            if (
                isset($eventTypeName) AND $boxItem['eventType']['name']==$eventTypeName
                    AND
                isset($institutionName) AND $boxItem['institution']['name']==$institutionName
                    AND
                    $boxItem['timelinePoint']==$tlPoint
                ) {
                $boxItems[] = $boxItem;
            }
        }

        uasort($boxItems, $compareByStartTime);

        $event[] = [
                    'timelinePoint' => $tlPoint,
                    'box' => $boxItems
                ];
    }
