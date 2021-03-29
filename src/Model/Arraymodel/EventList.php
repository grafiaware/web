<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Arraymodel;

use Model\Entity\StatusSecurityInterface;

/**
 * Description of EventList
 *
 * @author pes2704
 */
class EventList {

    private $statusSecurity;

    private $eventContent;

    private $linkButtonAttributesPrihlasit = ['class' => 'ui large red button'];
    private $linkButtonTextPrihlasit = 'Zde se můžete přihlásit';

    private $linkButtonAttributesZhlednout = ['class' => 'ui large yellow button'];
    private $linkButtonTextZhlednout = 'Můžete zhlédnout záznam';

    private $timelinePoint = [
            1 => '30. 3. 2021',
            2 => '31. 3. 2021',
            3 => '1. 4. 2021',
    ];

    public function __construct(StatusSecurityInterface $statusSecurity) {
        $this->statusSecurity = $statusSecurity;
        $this->eventContent = new EventContent();
    }


    private function getList() {

        $eventList =
##### PORADNY ##########################

        [
            [
                'eventId' => 'event_id_001',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                    'startTime' => '10:05',
                    'endTime' => '10:40',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Pracovně právní problematika v době covidu'),
            [
                'eventId' => 'event_id_002',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                    'startTime' => '10:45',
                    'endTime' => '11:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Wienerberger'),
            [
                'eventId' => 'event_id_003',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                    'startTime' => '11:00',
                    'endTime' => '11:15',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Daikin'),
            [
                'eventId' => 'event_id_004',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                    'startTime' => '11:15',
                    'endTime' => '11:45',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Jak oslovit zaměstnavatele a jak se připravit na pracovní pohovor'),
            [
                'eventId' => 'event_id_005',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                    'startTime' => '13:00',
                    'endTime' => '13:30',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Vyhlášení cen Mamma Parent Friendly'),
            [
                'eventId' => 'event_id_006',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                    'startTime' => '13:30',
                    'endTime' => '13:55',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Zvolená rekvalifikace zdarma – cesta k nové profesi'),
            [
                'eventId' => 'event_id_007',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                    'startTime' => '13:55',
                    'endTime' => '14:10',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Kermi'),
            [
                'eventId' => 'event_id_008',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                    'startTime' => '14:10',
                    'endTime' => '14:30',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Nástup do práce po rodičovské dovolené? Bomba!'),
            [
                'eventId' => 'event_id_009',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                    'startTime' => '14:35',
                    'endTime' => '15:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Možnosti zaměstnání v zahraničí'),
    #######################################################################################
            [
                'eventId' => 'event_id_010',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                    'startTime' => '15:00',
                    'endTime' => '15:15',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Possehl Electronics Czech Republic'),
            [
                'eventId' => 'event_id_011',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                    'startTime' => '15:15',
                    'endTime' => '15:35',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Jaká je budoucnost absolventů VŠ?'),
            [
                'eventId' => 'event_id_012',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                    'startTime' => '15:40',
                    'endTime' => '15:55',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Konplan – firma budoucnosti'),
            [
                'eventId' => 'event_id_013',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                    'startTime' => '15:55',
                    'endTime' => '16:30',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Jak nastartovat svůj vlastní business?'),
            [
                'eventId' => 'event_id_014',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                    'startTime' => '16:45',
                    'endTime' => '17:05',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Vzdělávací, rodinné a sociální programy pro občany v Plzni'),
            [
                'eventId' => 'event_id_015',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                    'startTime' => '17:10',
                    'endTime' => '17:55',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Jak se nezbláznit z covidu?'),
            [
                'eventId' => 'event_id_016',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                    'startTime' => '17:55',
                    'endTime' => '18:15',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Zacvičte si s Krašovskou!'),
            [
                'eventId' => 'event_id_017',
                'published' => '1',
                'timelinePoint' => '31. 3. 2021',
                    'startTime' => '10:05',
                    'endTime' => '10:25',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Budoucnost profesí v Plzeňském kraji'),

            [
                'eventId' => 'event_id_018',
                'published' => '1',
                'timelinePoint' => '31. 3. 2021',
                    'startTime' => '10:30',
                    'endTime' => '10:45',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('AKKA Czech Republic'),
            [
                'eventId' => 'event_id_019',
                'published' => '1',
                'timelinePoint' => '31. 3. 2021',
                    'startTime' => '10:45',
                    'endTime' => '11:10',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Jakou VŠ zvolit, aby se o vás personalisté prali?'),
            [
                'eventId' => 'event_id_020',
                'published' => '1',
                'timelinePoint' => '31. 3. 2021',
                    'startTime' => '11:10',
                    'endTime' => '11:30',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Péče o válečného veterána byl skvělý jazykový kurz'),
            [
                'eventId' => 'event_id_021',
                'published' => '1',
                'timelinePoint' => '31. 3. 2021',
                    'startTime' => '11:30',
                    'endTime' => '11:45',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Domluvte se ve své profesi anglicky/německy a učte se za peníze EU! '),
            [
                'eventId' => 'event_id_022',
                'published' => '1',
                'timelinePoint' => '31. 3. 2021',
                    'startTime' => '11:45',
                    'endTime' => '12:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Valeo Autoklimatizace'),
            [
                'eventId' => 'event_id_023',
                'published' => '1',
                'timelinePoint' => '31. 3. 2021',
                    'startTime' => '13:00',
                    'endTime' => '13:20',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Chcete se stát dobrovolníkem?'),
            [
                'eventId' => 'event_id_024',
                'published' => '1',
                'timelinePoint' => '31. 3. 2021',
                    'startTime' => '13:20',
                    'endTime' => '13:30',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Dobrovolníkem na vlastní kůži'),
            [
                'eventId' => 'event_id_025',
                'published' => '1',
                'timelinePoint' => '31. 3. 2021',
                    'startTime' => '13:30',
                    'endTime' => '13:35',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Diecézní charita'),
            [
                'eventId' => 'event_id_026',
                'published' => '1',
                'timelinePoint' => '31. 3. 2021',
                    'startTime' => '13:55',
                    'endTime' => '14:35',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Zásady bezpečného chování'),
            [
                'eventId' => 'event_id_027',
                'published' => '1',
                'timelinePoint' => '31. 3. 2021',
                    'startTime' => '14:35',
                    'endTime' => '14:55',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Nespalte se při koupi a prodeji nemovitosti'),
            [
                'eventId' => 'event_id_028',
                'published' => '1',
                'timelinePoint' => '31. 3. 2021',
                    'startTime' => '14:55',
                    'endTime' => '15:35',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Jak se pozná, že už jsem se zbláznil?'),
            [
                'eventId' => 'event_id_029',
                'published' => '1',
                'timelinePoint' => '31. 3. 2021',
                    'startTime' => '15:35',
                    'endTime' => '15:55',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Zacvičte si s Krašovskou!'),
            [
                'eventId' => 'event_id_030',
                'published' => '1',
                'timelinePoint' => '1. 4. 2021',
                    'startTime' => '10:05',
                    'endTime' => '10:35',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Jak si uhlídat své peníze?'),
            [
                'eventId' => 'event_id_031',
                'published' => '1',
                'timelinePoint' => '1. 4. 2021',
                    'startTime' => '10:40',
                    'endTime' => '11:10',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Home office – dobrý pomocník, ale zlý pán'),
            [
                'eventId' => 'event_id_032',
                'published' => '1',
                'timelinePoint' => '1. 4. 2021',
                    'startTime' => '11:15',
                    'endTime' => '11:35',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('K čemu je nám národní soustava kvalifikací?'),
            [
                'eventId' => 'event_id_033',
                'published' => '1',
                'timelinePoint' => '1. 4. 2021',
                    'startTime' => '11:40',
                    'endTime' => '12:05',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Ke kariérovému poradci na preventivní prohlídku'),
            [
                'eventId' => 'event_id_034',
                'published' => '1',
                'timelinePoint' => '1. 4. 2021',
                    'startTime' => '13:00',
                    'endTime' => '13:30',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Jak uvažuje personalista - náborář?'),
            [
                'eventId' => 'event_id_035',
                'published' => '1',
                'timelinePoint' => '1. 4. 2021',
                    'startTime' => '13:35',
                    'endTime' => '13:55',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Zacvičte si s Krašovskou!'),

    ########################
            [
                'eventId' => 'event_id_036',
                'published' => '0',
                'timelinePoint' => '31. 3. 2021',
                    'startTime' => '15:35',
                    'endTime' => '15:55',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Brýle – doplněk osobnosti i pracovní nástroj'),
            [
                'eventId' => 'event_id_037',
                'published' => '0',
                'timelinePoint' => '1. 4. 2021',
                    'startTime' => '',
                    'endTime' => '',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Na co dát pozor při uzavírání pracovní smlouvy?'),
            [
                'eventId' => 'event_id_038',
                'published' => '0',
                'timelinePoint' => '1. 4. 2021',
                'startTime' => '',
                'endTime' => '',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent('Jsem z tý školy blázen?'),

##### PORADNY ##########################

            [
                'eventId' => 'event_id_039',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                'startTime' => '10:00',
                'endTime' => '12:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent("Kariérové poradenství"),
            [
                'eventId' => 'event_id_040',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                'startTime' => '12:30',
                'endTime' => '18:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent("Kariérové poradenství"),
            [
                'eventId' => 'event_id_041',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                'startTime' => '12:00',
                'endTime' => '14:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent("Jak se nespálit v zahraničí"),
            [
                'eventId' => 'event_id_042',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                'startTime' => '14:00',
                'endTime' => '15:50',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent("Pracovně-právní poradna"),
            [
                'eventId' => 'event_id_043',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                'startTime' => '10:00',
                'endTime' => '12:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent("Poradna pro začínající podnikatele"),
            [
                'eventId' => 'event_id_044',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                'startTime' => '9:00',
                'endTime' => '12:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent("Poradna pro cizince pracující v ČR"),
            [
                'eventId' => 'event_id_045',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                'startTime' => '13:00',
                'endTime' => '17:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent("Poradna pro cizince pracující v ČR"),
            [
                'eventId' => 'event_id_046',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                'startTime' => '9:00',
                'endTime' => '12:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent("Poradna v těžkých životních situacích (občanská poradna)"),
            [
                'eventId' => 'event_id_047',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                'startTime' => '13:00',
                'endTime' => '16:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent("Poradna v těžkých životních situacích (občanská poradna)"),
            [
                'eventId' => 'event_id_048',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                'startTime' => '16:00',
                'endTime' => '18:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent("Psychoporadna pro rodiče"),

            [
                'eventId' => 'event_id_049',
                'published' => '1',
                'timelinePoint' => '31. 3. 2021',
                'startTime' => '10:00',
                'endTime' => '14:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent("Kariérové poradenství"),
            [
                'eventId' => 'event_id_050',
                'published' => '1',
                'timelinePoint' => '31. 3. 2021',
                'startTime' => '14:00',
                'endTime' => '16:50',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent("Pracovně-právní poradna"),
            [
                'eventId' => 'event_id_051',
                'published' => '1',
                'timelinePoint' => '31. 3. 2021',
                'startTime' => '13:00',
                'endTime' => '14:45',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent("Poradna pro začínající podnikatele"),
            [
                'eventId' => 'event_id_052',
                'published' => '1',
                'timelinePoint' => '31. 3. 2021',
                'startTime' => '15:00',
                'endTime' => '17:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent("Jak se nespálit v zahraničí (telefonicky)"),
            [
                'eventId' => 'event_id_053',
                'published' => '1',
                'timelinePoint' => '31. 3. 2021',
                'startTime' => '9:00',
                'endTime' => '16:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent("Poradna první  psychologické pomoci"),
            [
                'eventId' => 'event_id_054',
                'published' => '1',
                'timelinePoint' => '31. 3. 2021',
                'startTime' => '9:00',
                'endTime' => '12:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent("Poradna v těžkých životních situacích (občanská poradna)"),
            [
                'eventId' => 'event_id_055',
                'published' => '1',
                'timelinePoint' => '31. 3. 2021',
                'startTime' => '13:00',
                'endTime' => '16:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent("Poradna v těžkých životních situacích (občanská poradna)"),

            [
                'eventId' => 'event_id_056',
                'published' => '1',
                'timelinePoint' => '1. 4. 2021',
                'startTime' => '10:00',
                'endTime' => '12:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent("Kariérové poradenství"),
            [
                'eventId' => 'event_id_057',
                'published' => '1',
                'timelinePoint' => '1. 4. 2021',
                'startTime' => '12:30',
                'endTime' => '16:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent("Kariérové poradenství"),
            [
                'eventId' => 'event_id_058',
                'published' => '1',
                'timelinePoint' => '1. 4. 2021',
                'startTime' => '14:00',
                'endTime' => '15:50',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent("Pracovně-právní poradna"),
            [
                'eventId' => 'event_id_059',
                'published' => '1',
                'timelinePoint' => '1. 4. 2021',
                'startTime' => '10:00',
                'endTime' => '12:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent("Jak se nespálit v zahraničí (telefonicky)"),
            [
                'eventId' => 'event_id_060',
                'published' => '1',
                'timelinePoint' => '1. 4. 2021',
                'startTime' => '9:00',
                'endTime' => '13:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent("Poradna pro cizince pracující v ČR"),
            [
                'eventId' => 'event_id_061',
                'published' => '1',
                'timelinePoint' => '1. 4. 2021',
                'startTime' => '9:00',
                'endTime' => '12:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [


                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
            ] + $this->eventContent->getEventContent("Poradna v těžkých životních situacích (občanská poradna)"),

        ];

        return $eventList;

    }

    private function isLogged() {
        $loginAggregate = $this->statusSecurity->getLoginAggregate();
        return isset($loginAggregate);
    }

    public function getEventList($eventTypeName = null, $institutionName = null, array $eventIdList = [], $enrolling=false) {
        $compareByStartTime = function ($boxA, $boxB) {
            return (str_replace(':', '', $boxA['startTime']) < str_replace(':', '', $boxB['startTime'])) ? -1 : 1;
        };

    //    $eventTypeName = "Přednáška";
    //    $eventTypeName = 'Pohovor';
    //    $eventTypeName = "Poradna";

    //    $institutionName = "Ledovec";
        $event = [];
        foreach ($this->timelinePoint as $tlPoint) {
            $boxItems = [];
            foreach ($this->getList() as $boxItem) {
                $isItem = array_search($boxItem['eventId'], $eventIdList);
                if (
                    ($boxItem['timelinePoint']==$tlPoint)
                    AND
                    ((isset($eventTypeName) AND $eventTypeName) ? $boxItem['eventType']['name']==$eventTypeName : true)
                    AND
                    ((isset($institutionName) AND $institutionName) ? $boxItem['institution']['name']==$institutionName : true)
                    AND
                    ((isset($eventIdList) AND $eventIdList) ? (array_search($boxItem['eventId'], $eventIdList)!==false) : true)
                    ) {


                    if (!$enrolling OR !$this->isLogged()) {
                        $boxItem['linkButtonEnroll']['showEnroll'] = 0;
                    }
                    $boxItems[] = $boxItem;
                }
            }

            uasort($boxItems, $compareByStartTime);

            $event[] = [
                        'timelinePoint' => $tlPoint,
                        'box' => $boxItems
                    ];
        }
        return $event;
    }
    
    public function getEventBoxItem($eventId=null) {
        if (isset($eventId)) {
            foreach ($this->getList() as $boxItem) {
                if ($boxItem['eventId'] == $eventId) {
                    return $boxItem;
                }
            }
        }
    }

//    public function getEventBoxItem($eventId=null) {
//        if (isset($eventId)) {
//            foreach ($this->getList() as $boxItem) {
//                if ($boxItem['eventId'] == $eventId) {
//                    $boxes[$boxItem['timelinePoint']][] = $boxItem;
//
//
//                }
//            }
//            foreach ($boxes as $tlPoint => $boxItems) {
//                $event[] = [
//                        'timelinePoint' => $tlPoint,
//                        'box' => $boxItems
//                    ];
//            }
//            return $event;
//
//        }
//    }
}
