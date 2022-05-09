<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Model\Repository\RepoAbstract;

use Red\Model\Entity\PaperContentInterface;
use Red\Model\Entity\PaperContent;
use Red\Model\Dao\PaperContentDao;
use Model\Hydrator\HydratorInterface;

use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class PaperContentRepo extends RepoAbstract implements PaperContentRepoInterface {

    protected $dao;

    public function __construct(PaperContentDao $paperContentDao, HydratorInterface $contentHydrator) {
        $this->dataManager = $paperContentDao;
        $this->registerHydrator($contentHydrator);
    }

    /**
     *
     * @param int $contentId
     * @return PaperContentInterface|null
     */
    public function get($id): ?PaperContentInterface {
        $key = $this->dataManager->getPrimaryKeyTouples(['id'=>$id]);
        return $this->getEntity($key);
    }

    /**
     *
     * @param type $paperIdFk
     * @return iterable
     */
    public function findByReference($paperIdFk): iterable {
        $key = $this->dataManager->getForeignKeyTouples('paper_id_fk', ['paper_id_fk'=>$paperIdFk]);
        return $this->findEntitiesByReference('paper_id_fk', $key);
    }

    public function add(PaperContentInterface $paperContent) {
        $this->addEntity($paperContent);

    }

    public function remove(PaperContentInterface $paperContent) {
        $this->removeEntity($paperContent);
    }

    protected function createEntity() {
        return new PaperContent();
    }

    protected function indexFromKeyParams($id) {
        return $id;
    }

    protected function indexFromEntity(PaperContentInterface $paperContent) {
        return $paperContent->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }


}
