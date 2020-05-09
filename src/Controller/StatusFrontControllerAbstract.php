<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Controller;

use Model\Repository\StatusSecurityRepo;

/**
 * Description of StatusFrontControllerAbstract
 *
 * @author pes2704
 */
abstract class StatusFrontControllerAbstract extends FrontControllerAbstract {

    /**
     * @var StatusSecurityRepo
     */
    protected $statusSecurityRepo;

    /**
     *
     * @param StatusSecurityRepo $statusSecurityRepo
     */
    public function __construct(
            StatusSecurityRepo $statusSecurityRepo
            ) {
        $this->statusSecurityRepo = $statusSecurityRepo;

    }
}
