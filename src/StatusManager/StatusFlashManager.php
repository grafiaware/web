<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace StatusManager;

use Model\Entity\StatusFlashInterface;
use Model\Entity\StatusFlash;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Description of StatusFlashManager
 *
 * @author pes2704
 */
class StatusFlashManager implements StatusFlashManagerInterface {
    public function createStatusFlash(): StatusFlashInterface {
        return new StatusFlash();
    }

    public function revolveStatusFlash(StatusFlashInterface $statusFlash, ServerRequestInterface $request): void {
        $statusFlash->revolve($request);
    }
}
