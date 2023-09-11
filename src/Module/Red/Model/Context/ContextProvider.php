<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Context;

use Model\Context\ContextProviderInterface;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;

/**
 * Description of ContextFactory
 *
 * @author pes2704
 */
class ContextProvider implements ContextProviderInterface {

    /**
     * @var StatusSecurityRepo
     */
    protected $statusSecurityRepo;

    /**
     * @var StatusPresentationRepo
     */
    protected $statusPresentationRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo
            ) {
        $this->statusSecurityRepo = $statusSecurityRepo;
        $this->statusPresentationRepo = $statusPresentationRepo;
    }

    public function showOnlyPublished(): bool {
        $userActions = $this->statusSecurityRepo->get()->getUserActions();
        return isset($userActions) ? !$userActions->presentEditableContent() : false;
    }
}
