<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Flash;

use Model\Repository\StatusFlashRepo;

/**
 * Description of FlashVieModel
 *
 * @author pes2704
 */
class FlashVieModel implements StatusBoardViewModelInterface {

    private $statusFlashRepo;

    public function __construct(StatusFlashRepo $statusFlashRepo) {
        $this->statusFlashRepo = $statusFlashRepo;
    }

    public function getFlash() {
        $flashStatus = $flashRepo->get();
        return $flashStatus ? $flashStatus->getFlash() : '';
    }
}
