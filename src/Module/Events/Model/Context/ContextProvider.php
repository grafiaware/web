<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Context;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Model\Context\ContextProviderInterface;

/**
 * Description of ContextFactory
 *
 * @author pes2704
 */
class ContextProvider implements ContextProviderInterface {

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

    //TODO: Context factory pro events vrací showOnlyPublished() vždy true - vývojová verze - dodělat
    public function showOnlyPublished(): bool {
        return true;
    }
}
