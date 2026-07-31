<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Web\Component\ViewModel\Flash;

use Component\ViewModel\StatusViewModelInterface;

/**
 * Description of FlashViewModelForRenderer
 *
 * @author pes2704
 */
class FlashViewModelForRenderer implements FlashViewModelForRendererInterface {

    private StatusViewModelInterface $status;

    public function __construct(StatusViewModelInterface $status) {
        $this->status = $status;
    }

    public function getMessages() {
        return $this->status->getFlashMessages();
    }
}
