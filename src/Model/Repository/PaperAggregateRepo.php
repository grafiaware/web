<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;
use Model\Entity\PaperInterface;
use Model\Entity\PaperPaperContentsAggregate;
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
class PaperAggregateRepo extends PaperRepo implements RepoInterface, PaperRepoInterface {

    use AggregateRepoTrait;

    /**
     * @var DaoChildInterface
     */
    protected $dao;  // přetěžuje $dao v AbstractRepo - typ DaoChildInterface

    private $paperContentRepo;

    private $paperChildHydrator;

    public function __construct(PaperDao $paperDao, PaperHydrator $paperHydrator,
            PaperContentRepo $paperContentRepo, PaperChildHydrator $paperChildHydrator) {
        $this->dao = $paperDao;
        $this->hydrator = $paperHydrator;
        $this->paperContentRepo = $paperContentRepo;
        $this->childRepositories[] = $this->paperContentRepo;
        $this->paperChildHydrator = $paperChildHydrator;
    }

    /**
     *
     * @param array $row
     * @return string index
     */
    protected function recreateEntity($index, $row) {
        if ($row) {
            $row['contents'] = $this->paperContentRepo->findByPaperIdFk($row['id']);

            $paperAggregate = new PaperPaperContentsAggregate();
            $this->hydrator->hydrate($paperAggregate, $row);
            $this->paperChildHydrator->hydrate($paperAggregate, $row);
            $paperAggregate->setPersisted();
            $this->collection[$index] = $paperAggregate;
        }
    }

    protected function indexFromEntity(PaperInterface $paper) {
        return $paper->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }


}
