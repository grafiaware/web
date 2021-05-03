<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Dao;

use Pes\Session\SessionStatusHandlerInterface;

/**
 * Description of StatusDao
 *
 *
 *
 * @author pes2704
 */
class StatusDao {

    private $sessionHandler;
    private $handlerUid;

    public function __construct(SessionStatusHandlerInterface $sessionHandler) {
        $this->sessionHandler = $sessionHandler;
        $this->handlerUid = spl_object_hash($sessionHandler);
    }

    public function get($fragmentName) {
        return $this->sessionHandler->getFragmentArrayReference($fragmentName);
    }

    public function set($fragmentName, $row) {
        $this->sessionHandler->set($fragmentName, $row);
    }

    public function delete($fragmentName) {
        $this->sessionHandler->delete($fragmentName);
    }
}
