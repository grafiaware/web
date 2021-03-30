<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Arraymodel;

/**
 * Description of EventContent
 *
 * @author pes2704
 */
class EventContent {

    private $eventType;

    public function __construct() {
        $this->eventType = new EventType();
    }

    public function getEventContent($id) {

        $eventContent =
        [
        'Pracovně právní problematika v době covidu' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
            'title' => 'Pracovně právní problematika v době covidu',
            'perex' => 'Výpovědi, překážky v práci, náhrada mzdy, náležitosti pracovní smlouvy…',
            'institution' => ['id' => '', 'type'=>'', 'name'=>'Státní úřad inspekce práce'],
            'party' => 'Otto Slabý'
            ],
        'Wienerberger' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
                    'title' => 'Wienerberger',
                    'perex' => 'Firemní prezentace',
                    'institution' => ['id' => '', 'type'=>'', 'name'=>'Wienerberger'],
                    'party' => 'Barbora Krejčová'
            ],
        'Daikin' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
                    'title' => 'Daikin',
                    'perex' => 'Firemní prezentace: Svěží vzduch pro tvojí kariéru! ',
                    'institution' => ['id' => '', 'type'=>'', 'name'=>'Daikin'],
                    'party' => 'Tomáš Matoušek'
            ],
        'Jak oslovit zaměstnavatele a jak se připravit na pracovní pohovor' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
                    'title' => 'Jak oslovit zaměstnavatele a jak se připravit na pracovní pohovor',
                    'perex' => 'Co má a nemá obsahovat životopis, co psát do motivačního dopisu, abyste zaujali. Nejčastější chyby při pracovním pohovoru a jak se jich vyvarovat.',
                    'institution' => ['id' => '', 'type'=>'', 'name'=>'Grafia'],
                    'party' => 'Štěpánka Pirnosová'
            ],
        'Vyhlášení cen Mamma Parent Friendly' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
                    'title' => 'Vyhlášení cen Mamma Parent Friendly',
                    'perex' => 'Ocenění pro podniky přátelské rodině za rok 2020',
                    'institution' => ['id' => '', 'type'=>'', 'name'=>'Grafia a MO Plzeň 3'],
                    'party' => ''
            ],
        'Zvolená rekvalifikace zdarma – cesta k nové profesi' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
                    'title' => 'Zvolená rekvalifikace zdarma – cesta k nové profesi',
                    'perex' => 'Co jsou „zvolené rekvalifikace“, proč se stát „zájemcem o zaměstnání“, nové projekty OUTPLACEMENT a FLEXI',
                    'institution' => ['id' => '', 'type'=>'', 'name'=>'ÚP ČR, Krajská pobočka v Plzni'],
                    'party' => 'Světlana Skalová'
            ],
        'Kermi' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
                    'title' => 'Kermi',
                    'perex' => 'Firemní prezentace: „Jsme tu s vámi, již 25 let, přidejte se do našeho týmu“',
                    'institution' => ['id' => '', 'type'=>'', 'name'=>'Kermi'],
                    'party' => 'Jana Čedíková'
            ],
       'Nástup do práce po rodičovské dovolené? Bomba!' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
                    'title' => 'Nástup do práce po rodičovské dovolené? Bomba!',
                    'perex' => 'Projekt Moje budoucnost vám pomůže s motivací, rekvalifikací, PC dovednostmi, jazyky i s vlastním hledáním nové práce.',
                    'institution' => ['id' => '', 'type'=>'', 'name'=>'Grafia'],
                    'party' => 'David Brabec'
            ],
        'Možnosti zaměstnání v zahraničí' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
                    'title' => 'Možnosti zaměstnání v zahraničí',
                    'perex' => 'Chcete vycestovat za prací do zahraničí? EURES poradí, jak na to.',
                    'institution' => ['id' => '', 'type'=>'', 'name'=>'EURES'],
                    'party' => 'Markéta Vondrová'
            ],

        'Possehl Electronics Czech Republic' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
                    'title' => 'Possehl Electronics Czech Republic',
                    'perex' => 'Firemní prezentace',
                    'institution' => ['id' => '', 'type'=>'', 'name'=>'Possehl Electronics Czech Republic'],
                    'party' => 'Michail Rais'
            ],
        'Jaká je budoucnost absolventů VŠ?' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
                'title' => 'Jaká je budoucnost absolventů VŠ?',
                'perex' => 'Rozhovor s děkanem FST ZČU o perspektivách studijních oborů.',
                'institution' => ['id' => '', 'type'=>'', 'name'=>'ZČU v Plzni'],
                'party' => 'Milan Edl'
            ],
        'Konplan – firma budoucnosti' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
                'title' => 'Konplan – firma budoucnosti',
                'perex' => 'Firemní prezentace',
                'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],
                'party' => 'Vendula Linková, Martin Junek'
            ],
        'Jak nastartovat svůj vlastní business?' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
                'title' => 'Jak nastartovat svůj vlastní business?',
                'perex' => 'Nehledáte zaměstnání, ale chcete začít podnikat? Máte nápad na start up? Chcete se vyhnout problémům?',
                'institution' => ['id' => '', 'type'=>'', 'name'=>'CzechInvest'],
                'party' => 'Milan Kavka'
            ],
        'Vzdělávací, rodinné a sociální programy pro občany v Plzni' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
                'title' => 'Vzdělávací, rodinné a sociální programy pro občany v Plzni',
                'perex' => 'Co dělat v těžkých životních událostech? Jak být úspěšným rodičem? Poradenství pro rodiny, samoživitele a odborné sociální služby',
                'institution' => ['id' => '', 'type'=>'', 'name'=>'MO Plzeň 3'],
                'party' => 'Stanislav Šec'
            ],
        'Jak se nezbláznit z covidu?' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
                'title' => 'Jak se nezbláznit z covidu?',
                'perex' => 'Doléhá na vás „domácí vězení“? Necítíte se dobře? Jste naštvaní nebo apatičtí? Umíme pomáhat!',
                'institution' => ['id' => '', 'type'=>'', 'name'=>'Ledovec'],
                'party' => 'Martin Fojtíček'
            ],
        'Budoucnost profesí v Plzeňském kraji' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
                'title' => 'Budoucnost profesí v Plzeňském kraji',
                'perex' => 'Data, grafy, predikce… Jakým směrem se vydat, abyste v našem kraji měli perspektivu zaměstnání.',
                'institution' => ['id' => '', 'type'=>'', 'name'=>'Pakt zaměstnanosti Plzeňského kraje'],
                'party' => 'Marcel Gondorčín'
            ],
        'AKKA Czech Republic' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
                'title' => 'AKKA Czech Republic',
                'perex' => 'Firemní prezentace: Nahlédněte s námi do Prototypového centra v Plzni',
                'institution' => ['id' => '', 'type'=>'', 'name'=>'AKKA Czech Republic'],
                'party' => 'Štefan Bárta'
            ],
        'Jakou VŠ zvolit, aby se o vás personalisté prali?' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
                'title' => 'Jakou VŠ zvolit, aby se o vás personalisté prali?',
                'perex' => 'Přednáška děkana FST ZČU ',
                'institution' => ['id' => '', 'type'=>'', 'name'=>'FST ZČU'],
                'party' => 'Milan Edl'
            ],
        'Péče o válečného veterána byl skvělý jazykový kurz' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
                'title' => 'Péče o válečného veterána byl skvělý jazykový kurz',
                'perex' => 'Interview s místostarostou',
                'institution' => ['id' => '', 'type'=>'', 'name'=>'MO Plzeň 3'],
                'party' => 'Ondřej Ženíšek'
            ],
        'Domluvte se ve své profesi anglicky/německy a učte se za peníze EU! ' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
                'title' => 'Domluvte se ve své profesi anglicky/německy a učte se za peníze EU! ',
                'perex' => 'Chcete studovat jazyky a kurzy jsou drahé? Poradíme, kde studovat bezplatně!',
                'institution' => ['id' => '', 'type'=>'', 'name'=>'Grafia'],
                'party' => 'David Brabec'
            ],
        'Valeo Autoklimatizace' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
                'title' => 'Valeo Autoklimatizace',
                'perex' => 'Firemní prezentace',
                'institution' => ['id' => '', 'type'=>'', 'name'=>'Valeo Autoklimatizace'],
                'party' => 'Gábor Ifland'
            ],
        'Chcete se stát dobrovolníkem?' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
                'title' => 'Chcete se stát dobrovolníkem?',
                'perex' => 'Dobrovolnictví podporuje druhé a dá vašemu životu nový smysl',
                'institution' => ['id' => '', 'type'=>'', 'name'=>'Regionální dobrovolnické centrum Plzeňského kraje Totem'],
                'party' => 'Vlasta Faiferlíková'
            ],
        'Dobrovolníkem na vlastní kůži' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
                'title' => 'Dobrovolníkem na vlastní kůži',
                'perex' => 'Jak skloubit dobrovolnictví s prací místostarostky? Co nového jsem se o sobě i o druhých naučila…',
                'institution' => ['id' => '', 'type'=>'', 'name'=>'MO Plzeň 1'],
                'party' => 'Ilona Jehličková'
            ],
        'Diecézní charita' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
                'title' => 'Diecézní charita',
                'perex' => 'prezentace služeb',
                'institution' => ['id' => '', 'type'=>'', 'name'=>''],
                'party' => ''
            ],
        'Zásady bezpečného chování' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
                'title' => 'Zásady bezpečného chování',
                'perex' => 'Zajištění majetku, života a zdraví, jak se nenechat napálit… Projekt Zabezpečte se',
                'institution' => ['id' => '', 'type'=>'', 'name'=>'Společná přednáška MP a PČR'],
                'party' => 'Karel Zahut, Markéta Fialová'
            ],
        'Nespalte se při koupi a prodeji nemovitosti' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
                'title' => 'Nespalte se při koupi a prodeji nemovitosti',
                'perex' => 'Jaká úskalí vás čekají, čeho se vyvarovat, co zjistit předem?',
                'institution' => ['id' => '', 'type'=>'', 'name'=>'MO Plzeň 3'],
                'party' => ' Petr Baloun'
            ],

        'Jak se pozná, že už jsem se zbláznil?' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
                'title' => 'Jak se pozná, že už jsem se zbláznil?',
                'perex' => '',
                'institution' => ['id' => '', 'type'=>'', 'name'=>'Ledovec'],
                'party' => ' Martin Fojtíček'
            ],
         'Brýle – doplněk osobnosti i pracovní nástroj' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
                'title' => 'Brýle – doplněk osobnosti i pracovní nástroj',
                'perex' => 'Jak brýle ovlivní pracovní výkon i akceptaci okolím? Srozumitelně od optika',
                'institution' => ['id' => '', 'type'=>'', 'name'=>'Optika Klatovy'],
                'party' => ' Pavlína Stoklásková'
            ],
         'Jak si uhlídat své peníze?' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
                'title' => 'Jak si uhlídat své peníze?',
                'perex' => 'Finanční gramotnost pro každého',
                'institution' => ['id' => '', 'type'=>'', 'name'=>'Grafia'],
                'party' => 'Irena Šímová'
            ],
         'Home office – dobrý pomocník, ale zlý pán' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
                'title' => 'Home office – dobrý pomocník, ale zlý pán',
                'perex' => 'Jsme na home office výkonnější? Jak se podílí home office na naší náladě? Co potřebují zaměstnanci, aby se nezbláznili?',
                'institution' => ['id' => '', 'type'=>'', 'name'=>'Grafia a Konplan'],
                'party' => 'Jana Brabcová, Vendula Linková'
            ],
         'K čemu je nám národní soustava kvalifikací?' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
                'title' => 'K čemu je nám národní soustava kvalifikací?',
                'perex' => 'Víte, že můžete získat kvalifikaci, aniž byste léta studovali ve škole? Umíte svůj obor, jen na to nemáte „papír“? Pak jste tu správně!',
                'institution' => ['id' => '', 'type'=>'', 'name'=>'NPI'],
                'party' => 'Veronika Menčíková, Robert Gamba'
            ],
         'Ke kariérovému poradci na preventivní prohlídku' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
                'title' => 'Ke kariérovému poradci na preventivní prohlídku',
                'perex' => 'Co je kariérové poradenství a proč navštěvovat kariérového poradce, i když práci právě nehledám?',
                'institution' => ['id' => '', 'type'=>'', 'name'=>'Grafia'],
                'party' => 'Štěpánka Pirnosová'
            ],
         'Jak uvažuje personalista - náborář?' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
                'title' => 'Jak uvažuje personalista - náborář?',
                'perex' => 'Nahlédněte do tajů výběrových řízení a zjistěte, co rozhoduje o (ne)přijetí na pracovní pozici! Diskuse s náborovými pracovníky ČEZ a Assa Abloy',
                'institution' => ['id' => '', 'type'=>'', 'name'=>'Grafia'],
                'party' => ''
            ],
         'Zacvičte si s Krašovskou!' => [
            'eventType' => $this->eventType->getEventType('Přednáška'),
                'title' => 'Zacvičte si s Krašovskou!',
                'perex' => '',
                'institution' => ['id' => '', 'type'=>'', 'name'=>'Krašovská Aktivity centrum'],
                'party' => ''
            ],

        // not published
            'Na co dát pozor při uzavírání pracovní smlouvy?' => [
                'eventType' => $this->eventType->getEventType('Přednáška'),
                    'title' => 'Na co dát pozor při uzavírání pracovní smlouvy?',
                    'perex' => 'Co musí a nemusí být v pracovní smlouvě a proč? Užitečné tipy a rady pro vás.',
                    'institution' => ['id' => '', 'type'=>'', 'name'=>''],
                    'party' => '',
                ],
            'Jsem z tý školy blázen?' => [
                'eventType' => $this->eventType->getEventType('Přednáška'),
                    'title' => 'Jsem z tý školy blázen?',
                    'perex' => 'Jak poznat, že potřebuji pomoc…',
                    'institution' => ['id' => '', 'type'=>'', 'name'=>''],
                    'party' => '',
                ],


        ]
          +
        [
        "Kariérové poradenství" => [
            'eventType' => $this->eventType->getEventType('Poradna'),
            'title' => "Kariérové poradenství",
            'perex' => "Změna profese, jaké profesní kurzy a rekvalifikace zvolit s ohledem na trh práce v PK, \"kontrola životopisů\"…",
            'institution' => ['id' => '', 'type'=>'', 'name'=>'Grafia'],
            'party' => '',
            ],
        "Pracovně-právní poradna" => [
            'eventType' => $this->eventType->getEventType('Poradna'),
            'title' => "Pracovně-právní poradna",
            'perex' => "Pracovní právník radí českým i zahraničním zaměstnancům",
            'institution' => ['id' => '', 'type'=>'', 'name'=>'Státní úřad inspekce práce'],
            'party' => '',
            ],
        "Poradna pro začínající podnikatele" => [
            'eventType' => $this->eventType->getEventType('Poradna'),
            'title' => "Poradna pro začínající podnikatele",
            'perex' => "Jak založit skvělý start up? Co všechno potřebujete, než začnete podnikat? Chcete se vyhnout problémům?" ,
            'institution' => ['id' => '', 'type'=>'', 'name'=>'CzechInvest'],
            'party' => '',
            ],
        "Jak se nespálit v zahraničí" => [
            'eventType' => $this->eventType->getEventType('Poradna'),
            'title' => "Jak se nespálit v zahraničí",
            'perex' => "Potřebujete poradit se všemi náležitostmi práce mimo ČR?",
            'institution' => ['id' => '', 'type'=>'', 'name'=>'EURES'],
            'party' => '',
            ],
        "Jak se nespálit v zahraničí (telefonicky)" => [
            'eventType' => $this->eventType->getEventType('Poradna'),
            'title' => "Jak se nespálit v zahraničí",
            'perex' => "Potřebujete poradit se všemi náležitostmi práce mimo ČR? <b>Telefonicky</b> - čísla na poradce: <br/> +420 950 166 317,<br/> +420 950 148 320,<br/> +420 950 148 430",
            'institution' => ['id' => '', 'type'=>'', 'name'=>'EURES'],
            'party' => '',
            ],
        "Poradna pro cizince pracující v ČR" => [
            'eventType' => $this->eventType->getEventType('Poradna'),
            'title' => "Poradna pro cizince pracující v ČR",
            'perex' => "Špatné pracovní podmínky, nevyplacená  mzda, kontakt s českými úřady, aj. Poradna také telefonicky na čísle +420 737 651 641",
            'institution' => ['id' => '', 'type'=>'', 'name'=>'Diakonie Západ'],
            'party' => '',
            ],
        "Poradna první  psychologické pomoci" => [
            'eventType' => $this->eventType->getEventType('Poradna'),
            'title' => "Poradna první  psychologické pomoci. Poradna také telefonicky na čísle +420 733 414 421",
            'perex' => "",
            'institution' => ['id' => '', 'type'=>'', 'name'=>'Diakonie Západ'],
            'party' => '',
            ],
        "Poradna v těžkých životních situacích (občanská poradna)" => [
            'eventType' => $this->eventType->getEventType('Poradna'),
            'title' => "Poradna v těžkých životních situacích (občanská poradna)",
            'perex' => "Práva zaměstnance, evidence na ÚP, sociální dávky, finanční problematika (včetně oddlužení) a mnohé další. Poradna také telefonicky na čísle +420 775 720 492",
            'institution' => ['id' => '', 'type'=>'', 'name'=>'Diakonie Západ'],
            'party' => '',
            ],
        "Psychoporadna pro rodiče" => [
            'eventType' => $this->eventType->getEventType('Poradna'),
            'title' => "Psychoporadna pro rodiče",
            'perex' => "Návrat do práce, rodinné vztahy, jak najít čas a motivaci pro svůj rozvoj?",
            'institution' => ['id' => '', 'type'=>'', 'name'=>'Grafia'],
            'party' => '',
            ],
        ]
        +
[
"Konzultujte pracovní příležitosti. Těší se na Vás Elizabeth Franková" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Konzultujte pracovní příležitosti. Těší se na Vás Elizabeth Franková",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'AKKA Czech Republic'],'party' => '',],
"Konzultujte pracovní příležitosti. Těší se na Vás Vanda Štěrbová a Kateřina Janků" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Konzultujte pracovní příležitosti. Těší se na Vás Vanda Štěrbová a Kateřina Janků",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'AKKA Czech Republic'],'party' => '',],
"Konzultujte pracovní příležitosti. Těší se na Vás Vanda Štěrbová" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Konzultujte pracovní příležitosti. Těší se na Vás Vanda Štěrbová",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'AKKA Czech Republic'],'party' => '',],
"Individuální kariérové poradenství. Těší se na Vás Vanda Štěrbová" => ['eventType' => $this->eventType->getEventType('Pohovor'),'title' => "Individuální kariérové poradenství. Těší se na Vás Vanda Štěrbová",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'AKKA Czech Republic'],'party' => '',],
"Konzultujte pracovní příležitosti. Těší se na Vás Kateřina Janků" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Konzultujte pracovní příležitosti. Těší se na Vás Kateřina Janků",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'AKKA Czech Republic'],'party' => '',],
"Konzultujte pracovní příležitosti. Těší se na Vás Elizabeth Franková" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Konzultujte pracovní příležitosti. Těší se na Vás Elizabeth Franková",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'AKKA Czech Republic'],'party' => '',],
"Individuální kariérové poradenství. Těší se na Vás Vanda Štěrbová" => ['eventType' => $this->eventType->getEventType('Pohovor'),'title' => "Individuální kariérové poradenství. Těší se na Vás Vanda Štěrbová",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'AKKA Czech Republic'],'party' => '',],
"Konzultujte pracovní příležitosti. Těší se na Vás Kateřina Janků" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Konzultujte pracovní příležitosti. Těší se na Vás Kateřina Janků",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'AKKA Czech Republic'],'party' => '',],
"Prezentace společnosti. Daikin Industries Czech Republic" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Prezentace společnosti. Daikin Industries Czech Republic",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Daikin'],'party' => '',],
"Prezentace společnosti." => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Prezentace společnosti.",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Daikin'],'party' => '',],
"Kariérní příležitost ve vývojovém oddělení" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Kariérní příležitost ve vývojovém oddělení",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Daikin'],'party' => '',],
"Prezentace společnosti." => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Prezentace společnosti.",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Daikin'],'party' => '',],
"Prezentace společnosti. Daikin Industries Czech Republic" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Prezentace společnosti. Daikin Industries Czech Republic",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Daikin'],'party' => '',],
"Prezentace společnosti." => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Prezentace společnosti.",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Daikin'],'party' => '',],
"Kariérní příležitost ve výrobním inženýringu" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Kariérní příležitost ve výrobním inženýringu",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Daikin'],'party' => '',],
"Prezentace společnosti." => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Prezentace společnosti.",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Daikin'],'party' => '',],
"Otevřené pracovní pozice s vyšší kvalifikací." => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Otevřené pracovní pozice s vyšší kvalifikací.",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Daikin'],'party' => '',],
"Prezentace společnosti." => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Prezentace společnosti.",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Daikin'],'party' => '',],
"Prezentace společnosti. Daikin Industries Czech Republic." => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Prezentace společnosti. Daikin Industries Czech Republic.",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Daikin'],'party' => '',],
"Konplan – digitalizace nápojového průmyslu" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Konplan – digitalizace nápojového průmyslu",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],'party' => '',],
"Individuální – zeptejte se na cokoliv našeho HR." => ['eventType' => $this->eventType->getEventType('Pohovor'),'title' => "Individuální – zeptejte se na cokoliv našeho HR.",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],'party' => '',],
"Elektrokonstrukce a softwarová hi-tech řešení v nápojovém průmyslu" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Elektrokonstrukce a softwarová hi-tech řešení v nápojovém průmyslu",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],'party' => '',],
"Pobavte se se zástupci elektro projektování a HR" => ['eventType' => $this->eventType->getEventType('Pohovor'),'title' => "Pobavte se se zástupci elektro projektování a HR",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],'party' => '',],
"Zeptejte se na cokoliv našeho HR" => ['eventType' => $this->eventType->getEventType('Pohovor'),'title' => "Zeptejte se na cokoliv našeho HR",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],'party' => '',],
"Strojní konstrukce – tradiční odvětví v digitální době" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Strojní konstrukce – tradiční odvětví v digitální době",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],'party' => '',],
"Pobavte se se zástupci strojní konstrukce a HR" => ['eventType' => $this->eventType->getEventType('Pohovor'),'title' => "Pobavte se se zástupci strojní konstrukce a HR",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],'party' => '',],
"Zeptejte se na cokoliv našeho HR" => ['eventType' => $this->eventType->getEventType('Pohovor'),'title' => "Zeptejte se na cokoliv našeho HR",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],'party' => '',],
"Administrativní engineering v nápojovém průmyslu" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Administrativní engineering v nápojovém průmyslu",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],'party' => '',],
"Pobavte se se zástupci administrativní části a HR" => ['eventType' => $this->eventType->getEventType('Pohovor'),'title' => "Pobavte se se zástupci administrativní části a HR",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],'party' => '',],
"Zeptejte se na cokoliv našeho HR" => ['eventType' => $this->eventType->getEventType('Pohovor'),'title' => "Zeptejte se na cokoliv našeho HR",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],'party' => '',],
"Konplan – digitalizace nápojového průmyslu" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Konplan – digitalizace nápojového průmyslu",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],'party' => '',],
"Individuální – zeptejte se na cokoliv našeho HR" => ['eventType' => $this->eventType->getEventType('Pohovor'),'title' => "Individuální – zeptejte se na cokoliv našeho HR",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],'party' => '',],
"Strojní konstrukce – tradiční odvětví v digitální době" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Strojní konstrukce – tradiční odvětví v digitální době",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],'party' => '',],
"Pobavte se se zástupci strojní konstrukce a HR" => ['eventType' => $this->eventType->getEventType('Pohovor'),'title' => "Pobavte se se zástupci strojní konstrukce a HR",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],'party' => '',],
"Zeptejte se na cokoliv našeho HR" => ['eventType' => $this->eventType->getEventType('Pohovor'),'title' => "Zeptejte se na cokoliv našeho HR",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],'party' => '',],
"Administrativní engineering v nápojovém průmyslu" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Administrativní engineering v nápojovém průmyslu",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],'party' => '',],
"Pobavte se se zástupci administrativní části a HR" => ['eventType' => $this->eventType->getEventType('Pohovor'),'title' => "Pobavte se se zástupci administrativní části a HR",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],'party' => '',],
"Zeptejte se na cokoliv našeho HR" => ['eventType' => $this->eventType->getEventType('Pohovor'),'title' => "Zeptejte se na cokoliv našeho HR",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],'party' => '',],
"Elektrokonstrukce a softwarová hi-tech řešení v nápojovém průmyslu" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Elektrokonstrukce a softwarová hi-tech řešení v nápojovém průmyslu",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],'party' => '',],
"Pobavte se se zástupci elektro projektování a HR" => ['eventType' => $this->eventType->getEventType('Pohovor'),'title' => "Pobavte se se zástupci elektro projektování a HR",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],'party' => '',],
"Zeptejte se na cokoliv našeho HR" => ['eventType' => $this->eventType->getEventType('Pohovor'),'title' => "Zeptejte se na cokoliv našeho HR",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],'party' => '',],
"Konplan – digitalizace nápojového průmyslu" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Konplan – digitalizace nápojového průmyslu",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],'party' => '',],
"Individuální – zeptejte se na cokoliv našeho HR." => ['eventType' => $this->eventType->getEventType('Pohovor'),'title' => "Individuální – zeptejte se na cokoliv našeho HR.",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],'party' => '',],
"Administrativní engineering v nápojovém průmyslu" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Administrativní engineering v nápojovém průmyslu",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],'party' => '',],
"Pobavte se se zástupci administrativní části a HR" => ['eventType' => $this->eventType->getEventType('Pohovor'),'title' => "Pobavte se se zástupci administrativní části a HR",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],'party' => '',],
"Zeptejte se na cokoliv našeho HR" => ['eventType' => $this->eventType->getEventType('Pohovor'),'title' => "Zeptejte se na cokoliv našeho HR",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],'party' => '',],
"Elektrokonstrukce a softwarová hi-tech řešení v nápojovém průmyslu" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Elektrokonstrukce a softwarová hi-tech řešení v nápojovém průmyslu",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],'party' => '',],
"Pobavte se se zástupci elektro projektování a HR" => ['eventType' => $this->eventType->getEventType('Pohovor'),'title' => "Pobavte se se zástupci elektro projektování a HR",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],'party' => '',],
"Zeptejte se na cokoliv našeho HR" => ['eventType' => $this->eventType->getEventType('Pohovor'),'title' => "Zeptejte se na cokoliv našeho HR",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],'party' => '',],
"Strojní konstrukce – tradiční odvětví v digitální době" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Strojní konstrukce – tradiční odvětví v digitální době",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],'party' => '',],
"Pobavte se se zástupci strojní konstrukce a HR" => ['eventType' => $this->eventType->getEventType('Pohovor'),'title' => "Pobavte se se zástupci strojní konstrukce a HR",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],'party' => '',],
"Zeptejte se na cokoliv našeho HR" => ['eventType' => $this->eventType->getEventType('Pohovor'),'title' => "Zeptejte se na cokoliv našeho HR",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Konplan'],'party' => '',],
"Prezentace společnosti.  Vstupte, rádi poskytneme aktuální informace o pracovních příležitostech v MD Elektronik" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Prezentace společnosti.  Vstupte, rádi poskytneme aktuální informace o pracovních příležitostech v MD Elektronik",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'MD Elektronik'],'party' => '',],
"Prezentace společnosti.  Vstupte, rádi poskytneme aktuální informace o pracovních příležitostech v MD Elektronik" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Prezentace společnosti.  Vstupte, rádi poskytneme aktuální informace o pracovních příležitostech v MD Elektronik",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'MD Elektronik'],'party' => '',],
"Prezentace společnosti.  Vstupte, rádi poskytneme aktuální informace o pracovních příležitostech v MD Elektronik" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Prezentace společnosti.  Vstupte, rádi poskytneme aktuální informace o pracovních příležitostech v MD Elektronik",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'MD Elektronik'],'party' => '',],
"Valeo – pracovní příležitosti pro techniky, inženýry a vývojáře" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Valeo – pracovní příležitosti pro techniky, inženýry a vývojáře",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Valeo Autoklimatizace'],'party' => '',],
"Valeo Rakovník – nábor do výroby – operátor výroby, skladník, údržbář" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Valeo Rakovník – nábor do výroby – operátor výroby, skladník, údržbář",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Valeo Autoklimatizace'],'party' => '',],
"Valeo – pracovní příležitosti pro techniky, inženýry a vývojáře" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Valeo – pracovní příležitosti pro techniky, inženýry a vývojáře",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Valeo Autoklimatizace'],'party' => '',],
"Valeo – Humpolec – nábor do výroby – operátor výroby, skladník, údržbář" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Valeo – Humpolec – nábor do výroby – operátor výroby, skladník, údržbář",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Valeo Autoklimatizace'],'party' => '',],
"Valeo – pracovní příležitosti pro techniky, inženýry a vývojáře" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Valeo – pracovní příležitosti pro techniky, inženýry a vývojáře",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Valeo Autoklimatizace'],'party' => '',],
"Prezentace – Valeo v ČR" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Prezentace – Valeo v ČR",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Valeo Autoklimatizace'],'party' => '',],
"Valeo Žebrák – nábor do výroby – operátor výroby, skladník, údržbář" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Valeo Žebrák – nábor do výroby – operátor výroby, skladník, údržbář",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Valeo Autoklimatizace'],'party' => '',],
"Valeo – pracovní příležitosti pro techniky, inženýry a vývojáře" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Valeo – pracovní příležitosti pro techniky, inženýry a vývojáře",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Valeo Autoklimatizace'],'party' => '',],
"Valeo – pracovní příležitosti pro techniky, inženýry a vývojáře" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Valeo – pracovní příležitosti pro techniky, inženýry a vývojáře",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Valeo Autoklimatizace'],'party' => '',],
"Valeo Žebrák – nábor do výroby – operátor výroby, skladník, údržbář" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Valeo Žebrák – nábor do výroby – operátor výroby, skladník, údržbář",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Valeo Autoklimatizace'],'party' => '',],
"Valeo – pracovní příležitosti pro techniky, inženýry a vývojáře" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Valeo – pracovní příležitosti pro techniky, inženýry a vývojáře",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Valeo Autoklimatizace'],'party' => '',],
"Valeo Rakovník – nábor do výroby – operátor výroby, skladník, údržbář" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Valeo Rakovník – nábor do výroby – operátor výroby, skladník, údržbář",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Valeo Autoklimatizace'],'party' => '',],
"Valeo – pracovní příležitosti pro techniky, inženýry a vývojáře" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Valeo – pracovní příležitosti pro techniky, inženýry a vývojáře",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Valeo Autoklimatizace'],'party' => '',],
"Prezentace – Valeo v ČR" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Prezentace – Valeo v ČR",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Valeo Autoklimatizace'],'party' => '',],
"Valeo – pracovní příležitosti pro techniky, inženýry a vývojáře" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Valeo – pracovní příležitosti pro techniky, inženýry a vývojáře",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Valeo Autoklimatizace'],'party' => '',],
"Prezentace – Valeo v ČR" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Prezentace – Valeo v ČR",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Valeo Autoklimatizace'],'party' => '',],
"Valeo Žebrák – nábor do výroby – operátor výroby, skladník, údržbář" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Valeo Žebrák – nábor do výroby – operátor výroby, skladník, údržbář",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Valeo Autoklimatizace'],'party' => '',],
"Valeo – pracovní příležitosti pro techniky, inženýry a vývojáře" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Valeo – pracovní příležitosti pro techniky, inženýry a vývojáře",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Valeo Autoklimatizace'],'party' => '',],
"Valeo Žebrák – nábor do výroby – operátor výroby, skladník, údržbář" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Valeo Žebrák – nábor do výroby – operátor výroby, skladník, údržbář",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Valeo Autoklimatizace'],'party' => '',],
"Valeo – pracovní příležitosti pro techniky, inženýry a vývojáře" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Valeo – pracovní příležitosti pro techniky, inženýry a vývojáře",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Valeo Autoklimatizace'],'party' => '',],
"Valeo Rakovník – nábor do výroby – operátor výroby, skladník, údržbář" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Valeo Rakovník – nábor do výroby – operátor výroby, skladník, údržbář",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Valeo Autoklimatizace'],'party' => '',],
"Valeo – pracovní příležitosti pro techniky, inženýry a vývojáře" => ['eventType' => $this->eventType->getEventType('Prezentace'),'title' => "Valeo – pracovní příležitosti pro techniky, inženýry a vývojáře",'perex' => "",'institution' => ['id' => '', 'type'=>'', 'name'=>'Valeo Autoklimatizace'],'party' => '',],
];
$a=1;
        return $eventContent[$id];
    }

}
