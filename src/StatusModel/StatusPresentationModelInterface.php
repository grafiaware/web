<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace StatusModel;

use Model\Entity\StatusPresentationInterface;
use Model\Entity\StatusFlashInterface;
use Model\Entity\LanguageInterface;

use Psr\Http\Message\ServerRequestInterface;

/**
 *
 * @author pes2704
 */
interface StatusPresentationModelInterface {

    /**
     *
     * @return StatusPresentationInterface
     */
    public function getStatusPresentation($a=null);

    /**
     *
     * @return StatusFlashInterface
     */
    public function getStatusFlash();

}
