<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Context;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Model\Context\ContextFactoryInterface;
use Model\Context\PublishedContextInterface;
use Model\Context\PublishedContext;

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
            ) {
    }

    public function createPublishedContext(): PublishedContextInterface {
        return new PublishedContext(true);
    }
}
