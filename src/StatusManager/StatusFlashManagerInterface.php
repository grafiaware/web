<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace StatusManager;

use Model\Entity\StatusFlashInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 *
 * @author pes2704
 */
interface StatusFlashManagerInterface {

    public function createStatusFlash(): StatusFlashInterface;

    public function revolveStatusFlash(StatusFlashInterface $statusFlash, ServerRequestInterface $request): void;
}
