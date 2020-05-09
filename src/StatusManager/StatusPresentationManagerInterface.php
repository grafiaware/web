<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace StatusManager;

use Psr\Http\Message\ServerRequestInterface;

use Model\Entity\StatusPresentationInterface;

/**
 *
 * @author pes2704
 */
interface StatusPresentationManagerInterface {

    /**
     *
     * @return StatusPresentationInterface
     */
    public function createPresentationStatus(): StatusPresentationInterface;

    /**
     *
     * @param StatusPresentationInterface $statusPresentation
     * @param ServerRequestInterface $request
     * @return void
     */
    public function regenerateStatusPresentation(StatusPresentationInterface $statusPresentation, ServerRequestInterface $request): void;

}
