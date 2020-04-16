<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Controller;

use Model\Repository\StatusSecurityRepo;
use Model\Entity\StatusSecurity;

/**
 * Description of SecurityFrontControllerAbstract
 *
 * @author pes2704
 */
abstract  class SecurityFrontControllerAbstract extends FrontControllerAbstract implements FrontControllerInterface {

    /**
     * @var StatusSecurity
     */
    protected $statusSecurity;

    /**
     *
     * @param StatusSecurityRepo $statusSecurityRepo
     */
    public function __construct(StatusSecurityRepo $statusSecurityRepo) {
        $this->statusSecurity = $statusSecurityRepo->get();
    }
}
