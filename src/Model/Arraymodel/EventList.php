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

    private $linkButtonAttributesPlan = ['class' => 'ui large red button'];
    private $linkButtonTextPlan = 'To budu chtít vidět';

    private $linkButtonAttributesPrihlasit = ['class' => 'ui large red button'];
    private $linkButtonTextPrihlasit = 'Zde se můžete přihlásit';

    private $linkButtonAttributesVstoupit = ['class' => 'ui large green button', 'target' => '_blank'];
    private $linkButtonTextVstoupit = 'Vstupte do místnosti';

    private $linkButtonAttributesZhlednout = ['class' => 'ui large yellow button'];
    private $linkButtonTextZhlednout = 'Zhlédněte záznam';

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
                                'showEnroll' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,           // !!! timeline boxes a leaf - upravit druhý button na anchor
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
                                ],
                ] + $this->eventContent->getEventContent('Pracovně právní problematika v době covidu'),
            [
                'eventId' => 'event_id_002',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                    'startTime' => '10:45',
                    'endTime' => '11:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
                                ],
            ] + $this->eventContent->getEventContent('Wienerberger'),
            [
                'eventId' => 'event_id_003',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                    'startTime' => '11:00',
                    'endTime' => '11:15',
                'linkButtonEnroll' => [
                                'showEnroll' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
                                ],
            ] + $this->eventContent->getEventContent('Daikin'),
            [
                'eventId' => 'event_id_004',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                    'startTime' => '11:15',
                    'endTime' => '11:45',
                'linkButtonEnroll' => [
                                'showEnroll' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
                                ],
            ] + $this->eventContent->getEventContent('Jak oslovit zaměstnavatele a jak se připravit na pracovní pohovor'),
            [
                'eventId' => 'event_id_005',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                    'startTime' => '13:00',
                    'endTime' => '13:30',
                'linkButtonEnroll' => [
                                'showEnroll' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
                                ],
            ] + $this->eventContent->getEventContent('Vyhlášení cen Mamma Parent Friendly'),
            [
                'eventId' => 'event_id_006',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                    'startTime' => '13:30',
                    'endTime' => '13:55',
                'linkButtonEnroll' => [
                                'showEnroll' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
                                ],
            ] + $this->eventContent->getEventContent('Zvolená rekvalifikace zdarma – cesta k nové profesi'),
            [
                'eventId' => 'event_id_007',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                    'startTime' => '13:55',
                    'endTime' => '14:10',
                'linkButtonEnroll' => [
                                'showEnroll' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
                                ],
            ] + $this->eventContent->getEventContent('Kermi'),
            [
                'eventId' => 'event_id_008',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                    'startTime' => '14:10',
                    'endTime' => '14:30',
                'linkButtonEnroll' => [
                                'showEnroll' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
                                ],
            ] + $this->eventContent->getEventContent('Nástup do práce po rodičovské dovolené? Bomba!'),
            [
                'eventId' => 'event_id_009',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                    'startTime' => '14:35',
                    'endTime' => '15:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
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
                                'showEnroll' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
                                ],
            ] + $this->eventContent->getEventContent('Possehl Electronics Czech Republic'),
            [
                'eventId' => 'event_id_011',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                    'startTime' => '15:15',
                    'endTime' => '15:35',
                'linkButtonEnroll' => [
                                'showEnroll' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
                                ],
            ] + $this->eventContent->getEventContent('Jaká je budoucnost absolventů VŠ?'),
            [
                'eventId' => 'event_id_012',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                    'startTime' => '15:40',
                    'endTime' => '15:55',
                'linkButtonEnroll' => [
                                'showEnroll' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
                                ],
            ] + $this->eventContent->getEventContent('Konplan – firma budoucnosti'),
            [
                'eventId' => 'event_id_013',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                    'startTime' => '15:55',
                    'endTime' => '16:30',
                'linkButtonEnroll' => [
                                'showEnroll' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
                                ],
            ] + $this->eventContent->getEventContent('Jak nastartovat svůj vlastní business?'),
            [
                'eventId' => 'event_id_014',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                    'startTime' => '16:45',
                    'endTime' => '17:05',
                'linkButtonEnroll' => [
                                'showEnroll' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
                                ],
            ] + $this->eventContent->getEventContent('Vzdělávací, rodinné a sociální programy pro občany v Plzni'),
            [
                'eventId' => 'event_id_015',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                    'startTime' => '17:10',
                    'endTime' => '17:55',
                'linkButtonEnroll' => [
                                'showEnroll' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
                                ],
            ] + $this->eventContent->getEventContent('Jak se nezbláznit z covidu?'),
            [
                'eventId' => 'event_id_016',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                    'startTime' => '17:55',
                    'endTime' => '18:15',
                'linkButtonEnroll' => [
                                'showEnroll' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
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
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
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
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
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
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
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
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
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
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
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
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
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
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
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
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
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
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
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
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
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
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
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
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
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
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
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
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
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
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
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
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
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
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
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
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
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
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
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
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
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
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
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
                                'linkButtonAttributes' => $this->linkButtonAttributesPlan +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPlan
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextZhlednout
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
                                'showEnroll' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +
                                    [
                                        'href' => "https://cesnet.zoom.us/j/95620272138?pwd=N3BkbkxzWkZmUExzdlNzRlVXUG02dz09"
                                    ],
                                'linkButtonText' => $this->linkButtonTextVstoupit
                                ],
            ] + $this->eventContent->getEventContent("Kariérové poradenství"),
            [
                'eventId' => 'event_id_040',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                'startTime' => '12:30',
                'endTime' => '18:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +
                                    [
                                        'href' => "https://cesnet.zoom.us/j/95620272138?pwd=N3BkbkxzWkZmUExzdlNzRlVXUG02dz09"
                                    ],
                                'linkButtonText' => $this->linkButtonTextVstoupit
                                ],
            ] + $this->eventContent->getEventContent("Kariérové poradenství"),
            [
                'eventId' => 'event_id_041',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                'startTime' => '12:00',
                'endTime' => '14:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +
                                    [
                                        'href' => "https://cesnet.zoom.us/j/96860823819"
                                    ],
                                'linkButtonText' => $this->linkButtonTextVstoupit
                                ],
            ] + $this->eventContent->getEventContent("Jak se nespálit v zahraničí"),
            [
                'eventId' => 'event_id_042',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                'startTime' => '14:00',
                'endTime' => '15:50',
                'linkButtonEnroll' => [
                                'showEnroll' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +
                                    [
                                        'href' => "https://cesnet.zoom.us/j/96947664198"
                                    ],
                                'linkButtonText' => $this->linkButtonTextVstoupit
                                ],
            ] + $this->eventContent->getEventContent("Pracovně-právní poradna"),
            [
                'eventId' => 'event_id_043',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                'startTime' => '10:00',
                'endTime' => '12:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +
                                    [
                                        'href' => "https://cesnet.zoom.us/j/92891446260"
                                    ],
                                'linkButtonText' => $this->linkButtonTextVstoupit
                                ],
            ] + $this->eventContent->getEventContent("Poradna pro začínající podnikatele"),
            [
                'eventId' => 'event_id_044',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                'startTime' => '9:00',
                'endTime' => '12:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +
                                    [
                                        'href' => "https://cesnet.zoom.us/j/97849197559"
                                    ],
                                'linkButtonText' => $this->linkButtonTextVstoupit
                                ],
            ] + $this->eventContent->getEventContent("Poradna pro cizince pracující v ČR"),
            [
                'eventId' => 'event_id_045',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                'startTime' => '13:00',
                'endTime' => '17:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +
                                    [
                                        'href' => "https://cesnet.zoom.us/j/97849197559"
                                    ],
                                'linkButtonText' => $this->linkButtonTextVstoupit
                                ],
            ] + $this->eventContent->getEventContent("Poradna pro cizince pracující v ČR"),
            [
                'eventId' => 'event_id_046',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                'startTime' => '9:00',
                'endTime' => '12:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +
                                    [
                                        'href' => "https://cesnet.zoom.us/j/99353023722?pwd=V0xLMjJLYkkyei96MUVLZ05QWlVmQT09"
                                    ],
                                'linkButtonText' => $this->linkButtonTextVstoupit
                                ],
            ] + $this->eventContent->getEventContent("Poradna v těžkých životních situacích (občanská poradna)"),
            [
                'eventId' => 'event_id_047',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                'startTime' => '13:00',
                'endTime' => '16:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +
                                    [
                                        'href' => "https://cesnet.zoom.us/j/99353023722?pwd=V0xLMjJLYkkyei96MUVLZ05QWlVmQT09"
                                    ],
                                'linkButtonText' => $this->linkButtonTextVstoupit
                                ],
            ] + $this->eventContent->getEventContent("Poradna v těžkých životních situacích (občanská poradna)"),
            [
                'eventId' => 'event_id_048',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                'startTime' => '16:00',
                'endTime' => '18:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +
                                    [
                                        'href' => "https://cesnet.zoom.us/j/99550743023"
                                    ],
                                'linkButtonText' => $this->linkButtonTextVstoupit
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
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +
                                    [
                                        'href' => "https://cesnet.zoom.us/j/96797977877?pwd=T0g5M1FweVM2NHZBV0NLbEZQaDM5QT09"
                                    ],
                                'linkButtonText' => $this->linkButtonTextVstoupit
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
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +
                                    [
                                        'href' => "https://cesnet.zoom.us/j/94733969159"
                                    ],
                                'linkButtonText' => $this->linkButtonTextVstoupit
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
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +
                                    [
                                        'href' => "https://cesnet.zoom.us/j/96463837441"
                                    ],
                                'linkButtonText' => $this->linkButtonTextVstoupit
                                ],
            ] + $this->eventContent->getEventContent("Poradna pro začínající podnikatele"),
            [
                'eventId' => 'event_id_052',
                'published' => '1',
                'timelinePoint' => '31. 3. 2021',
                'startTime' => '15:00',
                'endTime' => '17:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextVstoupit
                                ],
            ] + $this->eventContent->getEventContent("Jak se nespálit v zahraničí (telefonicky)"),
            [
                'eventId' => 'event_id_053',
                'published' => '1',
                'timelinePoint' => '31. 3. 2021',
                'startTime' => '9:00',
                'endTime' => '16:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +
                                    [
                                        'href' => "https://cesnet.zoom.us/j/96689590788"
                                    ],
                                'linkButtonText' => $this->linkButtonTextVstoupit
                                ],
            ] + $this->eventContent->getEventContent("Poradna první  psychologické pomoci"),
            [
                'eventId' => 'event_id_054',
                'published' => '1',
                'timelinePoint' => '31. 3. 2021',
                'startTime' => '9:00',
                'endTime' => '12:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 1,
                                'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +
                                    [
                                        'href' => "https://cesnet.zoom.us/j/97528096426?pwd=c3krOXh1dG4rd1BPVHJ6Mlh4SlNmUT09"
                                    ],
                                'linkButtonText' => $this->linkButtonTextVstoupit
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
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +
                                    [
                                        'href' => "https://cesnet.zoom.us/j/97528096426?pwd=c3krOXh1dG4rd1BPVHJ6Mlh4SlNmUT09"
                                    ],
                                'linkButtonText' => $this->linkButtonTextVstoupit
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
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +
                                    [
                                        'href' => "https://cesnet.zoom.us/j/95620272138?pwd=N3BkbkxzWkZmUExzdlNzRlVXUG02dz09"
                                    ],
                                'linkButtonText' => $this->linkButtonTextVstoupit
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
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +
                                    [
                                        'href' => "https://cesnet.zoom.us/j/95620272138?pwd=N3BkbkxzWkZmUExzdlNzRlVXUG02dz09"
                                    ],
                                'linkButtonText' => $this->linkButtonTextVstoupit
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
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +
                                    [
                                        'href' => "https://cesnet.zoom.us/j/96154680240"
                                    ],
                                'linkButtonText' => $this->linkButtonTextVstoupit
                                ],
            ] + $this->eventContent->getEventContent("Pracovně-právní poradna"),
            [
                'eventId' => 'event_id_059',
                'published' => '1',
                'timelinePoint' => '1. 4. 2021',
                'startTime' => '10:00',
                'endTime' => '12:00',
                'linkButtonEnroll' => [
                                'showEnroll' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +
                                    [
                                        'href' => ""
                                    ],
                                'linkButtonText' => $this->linkButtonTextVstoupit
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
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +
                                    [
                                        'href' => "https://cesnet.zoom.us/j/96311880467"
                                    ],
                                'linkButtonText' => $this->linkButtonTextVstoupit
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
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +
                                    [
                                        'href' => "https://cesnet.zoom.us/j/96583526827?pwd=dFF0eE56ZnlSbk82cmFXNHRLV2RyUT09"
                                    ],
                                'linkButtonText' => $this->linkButtonTextVstoupit
                                ],
            ] + $this->eventContent->getEventContent("Poradna v těžkých životních situacích (občanská poradna)"),
            [
                'eventId' => 'event_id_062',
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
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +
                                    [
                                        'href' => "https://cesnet.zoom.us/j/99827389212?pwd=cDNXdjlHOFRZNkJrdFVRUDhyMndlUT09"
                                    ],
                                'linkButtonText' => $this->linkButtonTextVstoupit
                                ],
            ] + $this->eventContent->getEventContent("Kariérové poradenství"),

############## FIREMNÍ  ############################


            [
                'eventId' => 'event_id_063',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                'startTime' => '10:00',
                'endTime' => '12:00',
                'linkButtonEnroll' => [
                    'showEnroll' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +
                                    [
                                        'href' => "https://zoom.us/j/96068767501?pwd=YktaZ24venRtaThnelNDTHZvUkUzUT09"
                                    ],
                                'linkButtonText' => $this->linkButtonTextVstoupit
                                ],
            ] + $this->eventContent->getEventContent("Konzultujte pracovní příležitosti. Těší se na Vás Elizabeth Franková"),

            [
                'eventId' => 'event_id_064',
                'published' => '1',
                'timelinePoint' => '30. 3. 2021',
                'startTime' => '14:00',
                'endTime' => '16:30',
                'linkButtonEnroll' => [
                    'showEnroll' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +
                                    [
                                    ],
                                'linkButtonText' => $this->linkButtonTextPrihlasit
                                ],
                'linkButtonEnter' => [
                    'showEnter' => 0,
                                'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +
                                    [
                                        'href' => "https://zoom.us/j/98801845717?pwd=SDBTSHVUeXBCNStselNWNW9pM3JJUT09"
                                    ],
                                'linkButtonText' => $this->linkButtonTextVstoupit
                                ],

            ] + $this->eventContent->getEventContent("Konzultujte pracovní příležitosti. Těší se na Vás Vanda Štěrbová a Kateřina Janků"),

//'linkButtonVideo' => ['showVideo' => 1,'linkButtonAttributes' => $this->linkButtonAttributesZhlednout +['href' => ""],'linkButtonText' => $this->linkButtonTextZhlednout],
            ['eventId' => 'event_id_065','published' => '1','timelinePoint' => '31. 3. 2021','startTime' => '10:00','endTime' => '12:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/99546974113?pwd=M2RZdlpEaWdkTUtLQ1lNdlpqV0puUT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Konzultujte pracovní příležitosti. Těší se na Vás Vanda Štěrbová"),
            ['eventId' => 'event_id_066','published' => '1','timelinePoint' => '31. 3. 2021','startTime' => '14:00','endTime' => '15:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/99407064825?pwd=WEU4dGZHNUdDSDRtWDlHeEZ0N2NDQT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Individuální kariérové poradenství. Těší se na Vás Vanda Štěrbová"),
            ['eventId' => 'event_id_067','published' => '1','timelinePoint' => '31. 3. 2021','startTime' => '15:00','endTime' => '17:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/92921752521?pwd=UFd5S0VvMW40Vk4rZWt2SzlXbzcvZz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Konzultujte pracovní příležitosti. Těší se na Vás Kateřina Janků"),
            ['eventId' => 'event_id_068','published' => '1','timelinePoint' => '1. 4. 2021','startTime' => '10:00','endTime' => '12:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/94000782413?pwd=VEd1MUxWdmcyWHFlWXJoSUhvOTBpZz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Konzultujte pracovní příležitosti. Těší se na Vás Elizabeth Franková"),
            ['eventId' => 'event_id_069','published' => '1','timelinePoint' => '1. 4. 2021','startTime' => '14:00','endTime' => '15:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/96009804919?pwd=Tm1jT2I3cm9lMllBMHJUOGUxN0pFdz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Individuální kariérové poradenství. Těší se na Vás Vanda Štěrbová"),
            ['eventId' => 'event_id_070','published' => '1','timelinePoint' => '1. 4. 2021','startTime' => '15:00','endTime' => '17:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/94525283912?pwd=cVFWZzNBOEhRNG1ieUxhb3NNYzRHQT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Konzultujte pracovní příležitosti. Těší se na Vás Kateřina Janků"),
            ['eventId' => 'event_id_071','published' => '1','timelinePoint' => '30. 3. 2021','startTime' => '10:00','endTime' => '12:00','linkButtonEnroll' => ['showEnroll' => 0,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/91429766125?pwd=djlQbVlpdzFPV0lMY3lRTWRxU3NoQT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Prezentace společnosti. Daikin Industries Czech Republic"),
            ['eventId' => 'event_id_072','published' => '1','timelinePoint' => '30. 3. 2021','startTime' => '13:00','endTime' => '15:00','linkButtonEnroll' => ['showEnroll' => 0,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/93962619978?pwd=elo5VEEzYit3RWFDWk5SaG5qTWE3QT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Prezentace společnosti."),
            ['eventId' => 'event_id_073','published' => '1','timelinePoint' => '30. 3. 2021','startTime' => '15:00','endTime' => '16:30','linkButtonEnroll' => ['showEnroll' => 0,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/93962619978?pwd=elo5VEEzYit3RWFDWk5SaG5qTWE3QT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Kariérní příležitost ve vývojovém oddělení"),
            ['eventId' => 'event_id_074','published' => '1','timelinePoint' => '30. 3. 2021','startTime' => '16:30','endTime' => '18:00','linkButtonEnroll' => ['showEnroll' => 0,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/93962619978?pwd=elo5VEEzYit3RWFDWk5SaG5qTWE3QT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Prezentace společnosti."),
            ['eventId' => 'event_id_075','published' => '1','timelinePoint' => '31. 3. 2021','startTime' => '10:00','endTime' => '12:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/94558041514?pwd=NXRmVjJ4Qkx4aUhaWmNndkhUYkZhQT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Prezentace společnosti. Daikin Industries Czech Republic"),
            ['eventId' => 'event_id_076','published' => '1','timelinePoint' => '31. 3. 2021','startTime' => '13:00','endTime' => '15:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/94691697163?pwd=MkNBbVRKVFFCTTdySUoyaGVoYTZ6QT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Prezentace společnosti."),
            ['eventId' => 'event_id_077','published' => '1','timelinePoint' => '31. 3. 2021','startTime' => '15:00','endTime' => '16:30','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/94691697163?pwd=MkNBbVRKVFFCTTdySUoyaGVoYTZ6QT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Kariérní příležitost ve výrobním inženýringu"),
            ['eventId' => 'event_id_078','published' => '1','timelinePoint' => '31. 3. 2021','startTime' => '16:30','endTime' => '18:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/94691697163?pwd=MkNBbVRKVFFCTTdySUoyaGVoYTZ6QT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Prezentace společnosti."),
            ['eventId' => 'event_id_079','published' => '1','timelinePoint' => '1. 4. 2021','startTime' => '10:00','endTime' => '10:30','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/96702867622?pwd=YTMxeGRlbjhjKzRRV0o5L3pLOXdPdz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Otevřené pracovní pozice s vyšší kvalifikací."),
            ['eventId' => 'event_id_080','published' => '1','timelinePoint' => '1. 4. 2021','startTime' => '10:30','endTime' => '12:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/96702867622?pwd=YTMxeGRlbjhjKzRRV0o5L3pLOXdPdz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Prezentace společnosti."),
            ['eventId' => 'event_id_081','published' => '1','timelinePoint' => '1. 4. 2021','startTime' => '13:00','endTime' => '17:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/96190474866?pwd=a09Ya3N6MlhoeHc1T2NjakVhT3FZUT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Prezentace společnosti. Daikin Industries Czech Republic."),
            ['eventId' => 'event_id_082','published' => '1','timelinePoint' => '30. 3. 2021','startTime' => '10:00','endTime' => '10:30','linkButtonEnroll' => ['showEnroll' => 0,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/94243501239?pwd=ak1SRTdqZDRtczlBTWsyRGtuK0Fjdz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Konplan – digitalizace nápojového průmyslu"),
            ['eventId' => 'event_id_083','published' => '1','timelinePoint' => '30. 3. 2021','startTime' => '10:30','endTime' => '11:30','linkButtonEnroll' => ['showEnroll' => 0,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/99786446722?pwd=YjVEbWdHcytYTFM0Z3JPVU5yNEl1UT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Individuální – zeptejte se na cokoliv našeho HR."),
            ['eventId' => 'event_id_084','published' => '1','timelinePoint' => '30. 3. 2021','startTime' => '11:30','endTime' => '12:00','linkButtonEnroll' => ['showEnroll' => 0,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/97728686928?pwd=UVFuVWtjZUlNWE1VNEFpNUZTM2lPUT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Elektrokonstrukce a softwarová hi-tech řešení v nápojovém průmyslu"),
            ['eventId' => 'event_id_085','published' => '1','timelinePoint' => '30. 3. 2021','startTime' => '12:00','endTime' => '13:00','linkButtonEnroll' => ['showEnroll' => 0,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/99340363225?pwd=N0FXZVpuUU93Szdod2UxK2lkaHdPdz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Pobavte se se zástupci elektro projektování a HR"),
            ['eventId' => 'event_id_086','published' => '1','timelinePoint' => '30. 3. 2021','startTime' => '13:00','endTime' => '13:30','linkButtonEnroll' => ['showEnroll' => 0,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/99340363225?pwd=N0FXZVpuUU93Szdod2UxK2lkaHdPdz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Zeptejte se na cokoliv našeho HR"),
            ['eventId' => 'event_id_087','published' => '1','timelinePoint' => '30. 3. 2021','startTime' => '13:30','endTime' => '14:00','linkButtonEnroll' => ['showEnroll' => 0,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/99630370335?pwd=ZU1ZdzZPR0N5WDN3WXhxS1FYb1k4UT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Strojní konstrukce – tradiční odvětví v digitální době"),
            ['eventId' => 'event_id_088','published' => '1','timelinePoint' => '30. 3. 2021','startTime' => '14:00','endTime' => '15:00','linkButtonEnroll' => ['showEnroll' => 0,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/91598816005?pwd=QlhId0xoYytyVjVyM1F0VzlwamJLdz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Pobavte se se zástupci strojní konstrukce a HR"),
            ['eventId' => 'event_id_089','published' => '1','timelinePoint' => '30. 3. 2021','startTime' => '15:00','endTime' => '15:30','linkButtonEnroll' => ['showEnroll' => 0,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/91598816005?pwd=QlhId0xoYytyVjVyM1F0VzlwamJLdz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Zeptejte se na cokoliv našeho HR"),
            ['eventId' => 'event_id_090','published' => '1','timelinePoint' => '30. 3. 2021','startTime' => '15:30','endTime' => '16:00','linkButtonEnroll' => ['showEnroll' => 0,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/93701014840?pwd=bDBJbEtLWHRPVDhsZWNiNUJVOS85Zz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Administrativní engineering v nápojovém průmyslu"),
            ['eventId' => 'event_id_091','published' => '1','timelinePoint' => '30. 3. 2021','startTime' => '16:00','endTime' => '17:00','linkButtonEnroll' => ['showEnroll' => 0,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/94861112451?pwd=Q3RmSS94Yk1FM0gwak5ZZDNkV1VMdz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Pobavte se se zástupci administrativní části a HR"),
            ['eventId' => 'event_id_092','published' => '1','timelinePoint' => '30. 3. 2021','startTime' => '17:00','endTime' => '18:00','linkButtonEnroll' => ['showEnroll' => 0,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/94861112451?pwd=Q3RmSS94Yk1FM0gwak5ZZDNkV1VMdz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Zeptejte se na cokoliv našeho HR"),
            ['eventId' => 'event_id_093','published' => '1','timelinePoint' => '31. 3. 2021','startTime' => '10:00','endTime' => '10:30','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/98024358835?pwd=M1NJbXdGd0k0bUpHaXVVRll2UksyQT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Konplan – digitalizace nápojového průmyslu"),
            ['eventId' => 'event_id_094','published' => '1','timelinePoint' => '31. 3. 2021','startTime' => '10:30','endTime' => '11:30','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/92061356857?pwd=ZjUvVDlNQnMvemZGMi9UU25EUUt0dz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Individuální – zeptejte se na cokoliv našeho HR"),
            ['eventId' => 'event_id_095','published' => '1','timelinePoint' => '31. 3. 2021','startTime' => '11:30','endTime' => '12:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/98358474632?pwd=NlgwOWJqOTNjbjUybHBJUDloVC9PUT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Strojní konstrukce – tradiční odvětví v digitální době"),
            ['eventId' => 'event_id_096','published' => '1','timelinePoint' => '31. 3. 2021','startTime' => '12:00','endTime' => '13:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/96536922209?pwd=OHJzRHlVeHRnUzFOSGFqY2xyWEdpdz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Pobavte se se zástupci strojní konstrukce a HR"),
            ['eventId' => 'event_id_097','published' => '1','timelinePoint' => '31. 3. 2021','startTime' => '13:00','endTime' => '13:30','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/96536922209?pwd=OHJzRHlVeHRnUzFOSGFqY2xyWEdpdz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Zeptejte se na cokoliv našeho HR"),
            ['eventId' => 'event_id_098','published' => '1','timelinePoint' => '31. 3. 2021','startTime' => '13:30','endTime' => '14:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/99469402866?pwd=S3pnNEM4Q0tCMlpldGNQSi9aMkFoUT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Administrativní engineering v nápojovém průmyslu"),
            ['eventId' => 'event_id_099','published' => '1','timelinePoint' => '31. 3. 2021','startTime' => '14:00','endTime' => '15:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/96739069034?pwd=SUYxeUhsSCtyRUNlNkVRQnA5SWtrdz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Pobavte se se zástupci administrativní části a HR"),
            ['eventId' => 'event_id_100','published' => '1','timelinePoint' => '31. 3. 2021','startTime' => '15:00','endTime' => '15:30','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/96739069034?pwd=SUYxeUhsSCtyRUNlNkVRQnA5SWtrdz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Zeptejte se na cokoliv našeho HR"),
            ['eventId' => 'event_id_101','published' => '1','timelinePoint' => '31. 3. 2021','startTime' => '15:30','endTime' => '16:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/93032194928?pwd=VXNOUnhuUVM1bkc2L05oWnduYklHUT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Elektrokonstrukce a softwarová hi-tech řešení v nápojovém průmyslu"),
            ['eventId' => 'event_id_102','published' => '1','timelinePoint' => '31. 3. 2021','startTime' => '16:00','endTime' => '17:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/91688765654?pwd=Z1YwTzU4Nmk2Ukx5YnJpUnFQNG56QT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Pobavte se se zástupci elektro projektování a HR"),
            ['eventId' => 'event_id_103','published' => '1','timelinePoint' => '31. 3. 2021','startTime' => '17:00','endTime' => '18:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/91688765654?pwd=Z1YwTzU4Nmk2Ukx5YnJpUnFQNG56QT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Zeptejte se na cokoliv našeho HR"),
            ['eventId' => 'event_id_104','published' => '1','timelinePoint' => '1. 4. 2021','startTime' => '10:00','endTime' => '10:30','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/99659532600?pwd=d3JiT3lmN09oU3VHRmZUL29OM0hHdz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Konplan – digitalizace nápojového průmyslu"),
            ['eventId' => 'event_id_105','published' => '1','timelinePoint' => '1. 4. 2021','startTime' => '10:30','endTime' => '11:30','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/91432145472?pwd=dGVVOTJlS0ZDNGlxTWZ2V3FOcVBEZz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Individuální – zeptejte se na cokoliv našeho HR."),
            ['eventId' => 'event_id_106','published' => '1','timelinePoint' => '1. 4. 2021','startTime' => '11:30','endTime' => '12:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/93892547469?pwd=VFJtRm1UVGt3QW9SMUdMODdTK3l1UT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Administrativní engineering v nápojovém průmyslu"),
            ['eventId' => 'event_id_107','published' => '1','timelinePoint' => '1. 4. 2021','startTime' => '12:00','endTime' => '13:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/99301592176?pwd=NWMzYWVTdHUvV2Y0bWR6Q3NqNkcyUT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Pobavte se se zástupci administrativní části a HR"),
            ['eventId' => 'event_id_108','published' => '1','timelinePoint' => '1. 4. 2021','startTime' => '13:00','endTime' => '13:30','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/99301592176?pwd=NWMzYWVTdHUvV2Y0bWR6Q3NqNkcyUT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Zeptejte se na cokoliv našeho HR"),
            ['eventId' => 'event_id_109','published' => '1','timelinePoint' => '1. 4. 2021','startTime' => '13:30','endTime' => '14:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/96399405297?pwd=N3k5djF4VzRCLzFUV0U3M0R5UzZrUT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Elektrokonstrukce a softwarová hi-tech řešení v nápojovém průmyslu"),
            ['eventId' => 'event_id_110','published' => '1','timelinePoint' => '1. 4. 2021','startTime' => '14:00','endTime' => '15:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/92879612649?pwd=WkdMUnZ3YW5hYUZ4UUdFM09QbjJCdz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Pobavte se se zástupci elektro projektování a HR"),
            ['eventId' => 'event_id_111','published' => '1','timelinePoint' => '1. 4. 2021','startTime' => '15:00','endTime' => '15:30','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/92879612649?pwd=WkdMUnZ3YW5hYUZ4UUdFM09QbjJCdz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Zeptejte se na cokoliv našeho HR"),
            ['eventId' => 'event_id_112','published' => '1','timelinePoint' => '1. 4. 2021','startTime' => '15:30','endTime' => '16:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/96786347397?pwd=Y011WHdHS2JOTXdWTWhSNC9pdkhRZz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Strojní konstrukce – tradiční odvětví v digitální době"),
            ['eventId' => 'event_id_113','published' => '1','timelinePoint' => '1. 4. 2021','startTime' => '16:00','endTime' => '17:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/99430160201?pwd=Q2VkWTRKU2VyU05LSFgyQTBLLy9Ldz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Pobavte se se zástupci strojní konstrukce a HR"),
            ['eventId' => 'event_id_114','published' => '1','timelinePoint' => '1. 4. 2021','startTime' => '17:00','endTime' => '18:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/99430160201?pwd=Q2VkWTRKU2VyU05LSFgyQTBLLy9Ldz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Zeptejte se na cokoliv našeho HR"),
            ['eventId' => 'event_id_115','published' => '1','timelinePoint' => '30. 3. 2021','startTime' => '10:00','endTime' => '18:00','linkButtonEnroll' => ['showEnroll' => 0,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/92767182030?pwd=ckJSN0RmMjRkV2phQm5IQWpVR0tnQT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Prezentace společnosti.  Vstupte, rádi poskytneme aktuální informace o pracovních příležitostech v MD Elektronik"),
            ['eventId' => 'event_id_116','published' => '1','timelinePoint' => '31. 3. 2021','startTime' => '10:00','endTime' => '18:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/91481458749?pwd=SHgwVElZeituZlhaSERMWjFVb2dmQT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Prezentace společnosti.  Vstupte, rádi poskytneme aktuální informace o pracovních příležitostech v MD Elektronik"),
            ['eventId' => 'event_id_117','published' => '1','timelinePoint' => '1. 4. 2021','startTime' => '10:00','endTime' => '18:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/93196667433?pwd=WmJVRTBPNWdhdzIrZmFWTk43Y1l1dz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Prezentace společnosti.  Vstupte, rádi poskytneme aktuální informace o pracovních příležitostech v MD Elektronik"),
            ['eventId' => 'event_id_118','published' => '1','timelinePoint' => '30. 3. 2021','startTime' => '10:00','endTime' => '11:00','linkButtonEnroll' => ['showEnroll' => 0,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/98912695684?pwd=MUl1N1RnQU9PcXdCdjgrZ214RHdEUT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Valeo – pracovní příležitosti pro techniky, inženýry a vývojáře"),
            ['eventId' => 'event_id_119','published' => '1','timelinePoint' => '30. 3. 2021','startTime' => '11:00','endTime' => '12:00','linkButtonEnroll' => ['showEnroll' => 0,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/98912695684?pwd=MUl1N1RnQU9PcXdCdjgrZ214RHdEUT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Valeo Rakovník – nábor do výroby – operátor výroby, skladník, údržbář"),
            ['eventId' => 'event_id_120','published' => '1','timelinePoint' => '30. 3. 2021','startTime' => '12:00','endTime' => '13:00','linkButtonEnroll' => ['showEnroll' => 0,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/98912695684?pwd=MUl1N1RnQU9PcXdCdjgrZ214RHdEUT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Valeo – pracovní příležitosti pro techniky, inženýry a vývojáře"),
            ['eventId' => 'event_id_121','published' => '1','timelinePoint' => '30. 3. 2021','startTime' => '13:00','endTime' => '14:00','linkButtonEnroll' => ['showEnroll' => 0,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/98912695684?pwd=MUl1N1RnQU9PcXdCdjgrZ214RHdEUT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Valeo – Humpolec – nábor do výroby – operátor výroby, skladník, údržbář"),
            ['eventId' => 'event_id_122','published' => '1','timelinePoint' => '30. 3. 2021','startTime' => '14:00','endTime' => '15:00','linkButtonEnroll' => ['showEnroll' => 0,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/98912695684?pwd=MUl1N1RnQU9PcXdCdjgrZ214RHdEUT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Valeo – pracovní příležitosti pro techniky, inženýry a vývojáře"),
            ['eventId' => 'event_id_123','published' => '1','timelinePoint' => '30. 3. 2021','startTime' => '15:00','endTime' => '16:00','linkButtonEnroll' => ['showEnroll' => 0,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/98912695684?pwd=MUl1N1RnQU9PcXdCdjgrZ214RHdEUT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Prezentace – Valeo v ČR"),
            ['eventId' => 'event_id_124','published' => '1','timelinePoint' => '30. 3. 2021','startTime' => '16:00','endTime' => '17:00','linkButtonEnroll' => ['showEnroll' => 0,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/98912695684?pwd=MUl1N1RnQU9PcXdCdjgrZ214RHdEUT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Valeo Žebrák – nábor do výroby – operátor výroby, skladník, údržbář"),
            ['eventId' => 'event_id_125','published' => '1','timelinePoint' => '30. 3. 2021','startTime' => '17:00','endTime' => '18:00','linkButtonEnroll' => ['showEnroll' => 0,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/98912695684?pwd=MUl1N1RnQU9PcXdCdjgrZ214RHdEUT09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Valeo – pracovní příležitosti pro techniky, inženýry a vývojáře"),
            ['eventId' => 'event_id_126','published' => '1','timelinePoint' => '31. 3. 2021','startTime' => '10:00','endTime' => '11:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/91006896216?pwd=VmNYQW92NHhhb0VWenBaQUFLczFVZz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Valeo – pracovní příležitosti pro techniky, inženýry a vývojáře"),
            ['eventId' => 'event_id_127','published' => '1','timelinePoint' => '31. 3. 2021','startTime' => '11:00','endTime' => '12:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/91006896216?pwd=VmNYQW92NHhhb0VWenBaQUFLczFVZz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Valeo Žebrák – nábor do výroby – operátor výroby, skladník, údržbář"),
            ['eventId' => 'event_id_128','published' => '1','timelinePoint' => '31. 3. 2021','startTime' => '12:00','endTime' => '13:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/91006896216?pwd=VmNYQW92NHhhb0VWenBaQUFLczFVZz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Valeo – pracovní příležitosti pro techniky, inženýry a vývojáře"),
            ['eventId' => 'event_id_129','published' => '1','timelinePoint' => '31. 3. 2021','startTime' => '13:00','endTime' => '14:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/91006896216?pwd=VmNYQW92NHhhb0VWenBaQUFLczFVZz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Valeo Rakovník – nábor do výroby – operátor výroby, skladník, údržbář"),
            ['eventId' => 'event_id_130','published' => '1','timelinePoint' => '31. 3. 2021','startTime' => '14:00','endTime' => '15:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/91006896216?pwd=VmNYQW92NHhhb0VWenBaQUFLczFVZz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Valeo – pracovní příležitosti pro techniky, inženýry a vývojáře"),
            ['eventId' => 'event_id_131','published' => '1','timelinePoint' => '31. 3. 2021','startTime' => '15:00','endTime' => '16:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/91006896216?pwd=VmNYQW92NHhhb0VWenBaQUFLczFVZz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Prezentace – Valeo v ČR"),
            ['eventId' => 'event_id_132','published' => '1','timelinePoint' => '31. 3. 2021','startTime' => '16:00','endTime' => '17:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/91006896216?pwd=VmNYQW92NHhhb0VWenBaQUFLczFVZz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Valeo – pracovní příležitosti pro techniky, inženýry a vývojáře"),
            ['eventId' => 'event_id_133','published' => '1','timelinePoint' => '1. 4. 2021','startTime' => '10:00','endTime' => '11:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/97695093145?pwd=OEM5MUd6d3h6VmkwOFZwNnZpZ3prdz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Prezentace – Valeo v ČR"),
            ['eventId' => 'event_id_134','published' => '1','timelinePoint' => '1. 4. 2021','startTime' => '11:00','endTime' => '12:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/97695093145?pwd=OEM5MUd6d3h6VmkwOFZwNnZpZ3prdz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Valeo Žebrák – nábor do výroby – operátor výroby, skladník, údržbář"),
            ['eventId' => 'event_id_135','published' => '1','timelinePoint' => '1. 4. 2021','startTime' => '12:00','endTime' => '13:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/97695093145?pwd=OEM5MUd6d3h6VmkwOFZwNnZpZ3prdz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Valeo – pracovní příležitosti pro techniky, inženýry a vývojáře"),
            ['eventId' => 'event_id_136','published' => '1','timelinePoint' => '1. 4. 2021','startTime' => '13:00','endTime' => '14:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/97695093145?pwd=OEM5MUd6d3h6VmkwOFZwNnZpZ3prdz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Valeo Žebrák – nábor do výroby – operátor výroby, skladník, údržbář"),
            ['eventId' => 'event_id_137','published' => '1','timelinePoint' => '1. 4. 2021','startTime' => '14:00','endTime' => '15:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/97695093145?pwd=OEM5MUd6d3h6VmkwOFZwNnZpZ3prdz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Valeo – pracovní příležitosti pro techniky, inženýry a vývojáře"),
            ['eventId' => 'event_id_138','published' => '1','timelinePoint' => '1. 4. 2021','startTime' => '15:00','endTime' => '16:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/97695093145?pwd=OEM5MUd6d3h6VmkwOFZwNnZpZ3prdz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Valeo Rakovník – nábor do výroby – operátor výroby, skladník, údržbář"),
            ['eventId' => 'event_id_139','published' => '1','timelinePoint' => '1. 4. 2021','startTime' => '16:00','endTime' => '17:00','linkButtonEnroll' => ['showEnroll' => 1,'linkButtonAttributes' => $this->linkButtonAttributesPrihlasit +[],'linkButtonText' => $this->linkButtonTextPrihlasit],'linkButtonEnter' => ['showEnter' => 0,'linkButtonAttributes' => $this->linkButtonAttributesVstoupit +['href' => "https://zoom.us/j/97695093145?pwd=OEM5MUd6d3h6VmkwOFZwNnZpZ3prdz09"],'linkButtonText' => $this->linkButtonTextVstoupit],] + $this->eventContent->getEventContent("Valeo – pracovní příležitosti pro techniky, inženýry a vývojáře"),
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
//                $inTime = $boxItem['timelinePoint']==$tlPoint;
//                $isEventType = (isset($eventTypeName) AND $eventTypeName) ? strpos($eventTypeName, $boxItem['eventType']['name'])!==false : true;
//                $isInstitution = (isset($institutionName) AND $institutionName) ? ($boxItem['institution']['name'] AND strpos($institutionName, $boxItem['institution']['name']))!==false : true;
//                $isEvendId = (isset($eventIdList) AND $eventIdList) ? (array_search($boxItem['eventId'], $eventIdList)!==false) : true;

                if (
                    ($boxItem['timelinePoint']==$tlPoint)
                    AND
                    ((isset($eventTypeName) AND $eventTypeName) ? ($boxItem['eventType']['name'] AND strpos($eventTypeName, $boxItem['eventType']['name'])!==false) : true)
                    AND
                    ((isset($institutionName) AND $institutionName) ? ($boxItem['institution']['name'] AND strpos($institutionName, $boxItem['institution']['name'])!==false) : true)
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
