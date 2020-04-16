<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;


/**
 *
 * @author pes2704
 */
interface StatusFlashInterface {

    /**
     * Vrací message
     * @return string
     */
    public function getFlash();

    /**
     * Nastaví message
     * @param string $flash
     * @return $this
     */
    public function setFlash(string $flash): StatusFlashInterface;
}
