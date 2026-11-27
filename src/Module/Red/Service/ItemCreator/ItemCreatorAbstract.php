<?php

namespace Red\Service\ItemCreator;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Enum\FlashSeverityEnum;

/**
 * Description of ContentService
 *
 * @author pes2704
 */
abstract class ItemCreatorAbstract {


    /**
     * @var StatusSecurityRepo
     */
    protected $statusSecurityRepo;

    /**
     * @var StatusPresentationRepo
     */
    protected $statusPresentationRepo;

    /**
     * @var StatusFlashRepo
     */
    protected $statusFlashRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo
            ) {
        $this->statusSecurityRepo = $statusSecurityRepo;
        $this->statusPresentationRepo = $statusPresentationRepo;
        $this->statusFlashRepo = $statusFlashRepo;
    }

    /**
     * Hláška do pruhu hlášek – stejně jako FrontControlerAbstract::addFlashMessage() (#187: creator není kontrolér).
     */
    protected function addFlashMessage($message, $severity = FlashSeverityEnum::INFO): void {
        $this->statusFlashRepo->get()->setMessage($message, $severity);
    }

}
