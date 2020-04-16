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
     * Vrací  a smyže flash message. První volání vrací poslední nstavenou message a snaže ji. Další volání pak již vrací prázdný řetězec
     * a také do session je po prvím volání getFlash() již uložen jenprázdný řetězec.
     * @return string Flash message string.
     */
    public function getFlash() {
        $lastFlash = $this->flash;
        $this->flash = '';
        return $lastFlash;
    }

    /**
     * Nastaví flash message.
     * @param string $flash
     * @return $this
     */
    public function setFlash(string $flash): StatusFlashInterface {
        $this->flash = $flash;
        return $this;
    }
}
