<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Flash;

use Status\Model\Repository\StatusFlashRepo;

/**
 * Description of FlashViewModelForRenderer
 *
 * @author pes2704
 */
class FlashViewModelForRenderer implements FlashViewModelForRendererInterface {

    private $statusFlashRepo;

    public function __construct(StatusFlashRepo $statusFlashRepo) {
        $this->statusFlashRepo = $statusFlashRepo;
    }

    public function getMessage() {
        $statusFlash = $this->statusFlashRepo->get();
        return $statusFlash ? $statusFlash->getMessages() ?? 'no flash' : 'no flash message';
    }
}
