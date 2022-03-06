<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Flash;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\StatusViewModelInterface;


/**
 * Description of FlashVieModel
 *
 * @author pes2704
 */
class FlashViewModel extends ViewModelAbstract implements FlashViewModelInterface {

    private $status;

    public function __construct(StatusViewModelInterface $status) {
        $this->status = $status;
    }

    public function getIterator() {
        $this->appendData( [
                    'flashMessages' => $this->status->getFlashMessages(),
                ]
            );
        return parent::getIterator();
    }
}
