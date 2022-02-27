<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Flash;

use Component\ViewModel\StatusViewModel;

use Status\Model\Repository\StatusFlashRepo;

use Status\Model\Entity\StatusFlashInterface;

/**
 * Description of FlashVieModel
 *
 * @author pes2704
 */
class FlashViewModel extends StatusViewModel implements FlashViewModelInterface {

    public function getIterator() {
        $this->appendData( [
                    'flashMessages' => $this->getFlashMessages(),
                    'postCommand' => $this->getPostCommand()
                ]
            );
        return parent::getIterator();
    }

    private function getFlashMessages() {
        $statusFlash = $this->statusFlashRepo->get();
        return $statusFlash ? $statusFlash->getMessages() : '';
    }

    private function getPostCommand() {
        $statusFlash = $this->statusFlashRepo->get();
        return $statusFlash ? $this->formatCommad($statusFlash->getPostCommand()) : '';
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
