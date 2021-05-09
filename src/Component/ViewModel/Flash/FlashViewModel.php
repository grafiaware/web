<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Flash;

use Status\Model\Repository\StatusFlashRepo;

use Status\Model\Entity\StatusFlashInterface;

/**
 * Description of FlashVieModel
 *
 * @author pes2704
 */
class FlashViewModel implements FlashViewModelInterface {

    private $statusFlashRepo;

    public function __construct(StatusFlashRepo $statusFlashRepo) {
        $this->statusFlashRepo = $statusFlashRepo;
    }

    public function getIterator() {
        $statusFlash = $this->statusFlashRepo->get();
        return new \ArrayObject(
                [
                    'flashMessage' => $statusFlash ? $statusFlash->getMessage() : '',
                    'postCommand' => $this->formatCommad($statusFlash->getPostCommand())
                ]
            );
    }

    private function formatCommad($command) {
        $msg = '';
        if (isset($command)) {
            foreach ($command as $key => $value) {
                $msg .=$key." => ".$value.PHP_EOL;
            }
        }
        return $msg;
    }
}
