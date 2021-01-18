<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ContentService\Paper;

use Model\Entity\PaperAggregateInterface;
use Model\Entity\PaperAggregate;

/**
 * Description of PaperService
 *
 * @author pes2704
 */
class PaperAggregateService implements PaperAggregateServiceInterface {

    /**
     * @var PaperAggregateRepo
     */
    protected $paperAggregateRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo,
            PaperAggregateRepo $paperAggregateRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo, $statusFlashRepo);
        $this->paperAggregateRepo = $paperAggregateRepo;
    }

    public function create(): PaperAggregateInterface {
        $paperAggregate = new PaperAggregate();
        $paperAggregate->setEditor($this->statusSecurityRepo->get()->getUser()->getUserName());

    }

    public function remove(PaperAggregateInterface $paperAggregate) {
        ;
    }
}
