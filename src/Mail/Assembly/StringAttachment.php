<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Mail\Assembly;

/**
 * Description of StringAttachment
 *
 * @author pes2704
 */
class StringAttachment {


    private $stringAttachment;
    private $altText;

    public function getStringAttachment() {
        return $this->stringAttachment;
    }

    public function getAltText() {
        return $this->altText;
    }

    public function setStringAttachment($stringAttachment) {
        $this->stringAttachment = $stringAttachment;
        return $this;
    }

    public function setAltText($altText) {
        $this->altText = $altText;
        return $this;
    }



}
