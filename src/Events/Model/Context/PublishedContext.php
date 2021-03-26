<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Context;

use Model\Context\PublishedContextInterface;

/**
 * Description of PublishedContext
 *
 * @author pes2704
 */
class PublishedContext implements PublishedContextInterface {

    private $published;

    public function __construct($published) {
        $this->published = (bool) $published;
    }

    public function selectPublished(): bool {
        return $this->published;
    }

}
