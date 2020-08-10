<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

/**
 * Description of StatusFlash
 *
 * @author pes2704
 */
class StatusFlash implements StatusFlashInterface {

    private $flash='';

    /**
     * Vrací  a smaže flash message. První volání vrací poslední nastavenou message a snaže ji. Další volání pak již vrací prázdný řetězec
     * a také do session je po prvím volání getFlash() již uložen jenprázdný řetězec.
     * @return string Flash message string.
     */
    public function getFlashMessage() {
        $lastFlash = $this->flash;
        $this->flash = '';
        return $lastFlash;
    }

    /**
     * Nastaví flash message.
     * @param string $flash
     * @return $this
     */
    public function setFlashMessage(string $flash): StatusFlashInterface {
        $this->flash = $flash;
        return $this;
    }
}
