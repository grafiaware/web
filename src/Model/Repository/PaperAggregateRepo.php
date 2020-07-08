<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;
use Model\Entity\PaperInterface;
use Model\Entity\PaperAggregate;
use Model\Dao\PaperDao;
use Model\Dao\DaoChildInterface;
use Model\Hydrator\PaperHydrator;
use Model\Repository\PaperContentRepo;
use Model\Hydrator\PaperChildHydrator;

use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class PaperAggregateRepo extends PaperRepo implements RepoInterface, PaperRepoInterface, RepoReadonlyInterface {

    public function __construct(PaperDao $paperDao, PaperHydrator $paperHydrator,
            PaperContentRepo $paperContentRepo, PaperChildHydrator $paperChildHydrator) {
        parent::__construct($paperDao, $paperHydrator);
        $this->registerOneToManyAssotiation('contents', 'id', $paperContentRepo);
        $this->registerHydrator($paperChildHydrator);
    }

    protected function createEntity() {
        return new PaperAggregate();
    }

    protected function indexFromEntity(PaperInterface $paper) {
        return $paper->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }


}
