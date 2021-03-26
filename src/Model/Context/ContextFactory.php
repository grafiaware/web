<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Context;

use Model\Repository\StatusSecurityRepo;
use Model\Repository\StatusPresentationRepo;

/**
 * Description of ContextFactory
 *
 * @author pes2704
 */
class ContextFactory implements ContextFactoryInterface {

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

    public function createPublishedContext(): PublishedContextInterface {
        $userActions = $this->statusSecurityRepo->get()->getUserActions();
        $onlyPublished = $userActions ? ! $userActions->isEditableArticle() OR $userActions->isEditableLayout() : true;
        return new PublishedContext($onlyPublished);
    }
}
