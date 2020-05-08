<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Controller;

use Model\Entity\{
    StatusSecurity, StatusPresentation
};
use Model\Repository\{
    StatusSecurityRepo, StatusPresentationRepo
};

use st

/**
 * Description of StatusFrontControllerAbstract
 *
 * @author pes2704
 */
abstract class StatusFrontControllerAbstract extends FrontControllerAbstract implements FrontControllerInterface {

    /**
     * @var StatusSecurity
     */
    protected $statusSecurity;

    /**
     * @var StatusPresentation
     */
    protected $statusPresentation;

    /**
     *
     * @param StatusSecurityRepo $statusSecurityRepo
     * @param StatusPresentationRepo $statusPresentationRepo
     */
    public function __construct(
            StatusSecurityRepo  $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo


            ) {
        $this->statusSecurity = $statusSecurityRepo->get();
        $this->statusPresentation = $statusPresentationRepo->get();
    }
}
