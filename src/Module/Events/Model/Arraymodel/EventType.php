<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Arraymodel;

/**
 * Description of EventType
 *
 * @author pes2704
 */
class EventType {

    private $eventType = [
        'Prezentace' => ['name'=>'Prezentace', 'icon'=>'chalkboard teacher icon'],
        'Přednáška' => ['name'=>'Přednáška', 'icon'=>'chalkboard teacher icon'],
        'Pohovor'=> ['name'=>'Pohovor', 'icon'=> 'microphone icon'],
        'Poradna' => ['name'=>'Poradna', 'icon'=> 'user friends icon'],
    ];

    public function getEventType($type) {
        return $this->eventType[$type];
    }
}
