<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Context;

use Model\Repository\StatusSecurityRepo;
use Model\Repository\StatusPresentationRepo;
use Model\Context\ContextFactoryInterface;
use Model\Context\PublishedContextInterface;

/**
 * Description of ContextFactory
 *
 * @author pes2704
 */
class ContextFactory implements ContextFactoryInterface {

    /**
     * @var StatusSecurityRepo
     */
//    protected $statusSecurityRepo;

    /**
     * @var StatusPresentationRepo
     */
//    protected $statusPresentationRepo;


    //TODO: statusy
    public function __construct(
//            StatusSecurityRepo $statusSecurityRepo,
//            StatusPresentationRepo $statusPresentationRepo
            ) {
//        $this->statusSecurityRepo = $statusSecurityRepo;
//        $this->statusPresentationRepo = $statusPresentationRepo;
    }

    public function createPublishedContext(): PublishedContextInterface {
//        $userActions = $this->statusSecurityRepo->get()->getUserActions();
//        $onlyPublished = $userActions ? true : true;
//        return new PublishedContext($onlyPublished);
        return new PublishedContext(true);
    }
}