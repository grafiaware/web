<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Context;

/**
 * Description of PublishedContext
 *
 * @author pes2704
 */
class PublishedContext implements PublishedContextInterface {
    private $active;
    private $actual;

    public function __construct($active, $actual) {
        $this->active = (bool) $active;
        $this->actual = (bool) $actual;
    }

    public function selectActive(): bool {
        return $this->active;
    }

    public function selectActual():bool {
        return $this->actual;
    }


}
