<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Model\Repository\RepoInterface;
//use Model\Repository\RepoReadonlyInterface;

use Red\Model\Entity\PaperAggregatePaperContent;
use Red\Model\Dao\PaperDao;
use Red\Model\Hydrator\PaperHydrator;
use Red\Model\Repository\PaperContentRepo;
use Red\Model\Hydrator\PaperChildHydrator;
use Red\Model\Entity\PaperContentInterface;


/**
 * Description of Menu
 *
 * @author pes2704
 */
class PaperAggregateRepo extends PaperRepo implements RepoInterface, PaperRepoInterface {

    public function __construct(PaperDao $paperDao, PaperHydrator $paperHydrator,
            PaperContentRepo $paperContentRepo, PaperChildHydrator $paperChildHydrator) {
        parent::__construct($paperDao, $paperHydrator);
        $this->registerOneToManyAssociation(PaperContentInterface::class, 'id', $paperContentRepo);
        $this->registerHydrator($paperChildHydrator);
    }

    protected function createEntity() {
        return new PaperAggregatePaperContent();
    }
}
