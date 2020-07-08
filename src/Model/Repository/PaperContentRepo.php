<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;
use Model\Entity\PaperContentInterface;
use Model\Entity\PaperContent;
use Model\Dao\PaperContentDao;
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
        $this->dao = $paperContentDao;
        $this->registerHydrator($contentHydrator);
    }

    /**
     *
     * @param int $contentId
     * @return PaperContentInterface|null
     */
    public function get($contentId): ?PaperContentInterface {
        $index = $contentId;
        if (!isset($this->collection[$index])) {
            /** @var PaperContentDao $this->dao */
            $this->recreateEntity($index, $this->dao->get($contentId));
        }
        return $this->collection[$index] ?? NULL;
    }

    public function findByReference($paperIdFk): iterable {
        $selected = [];
        foreach ($this->dao->findAllByFk($paperIdFk) as $paperContentRow) {
            $index = $this->indexFromRow($paperContentRow);
            if (!isset($this->collection[$index])) {
                $this->recreateEntity($index, $paperContentRow);
            }
            $selected[] = $this->collection[$index];
        }
        return $selected;
    }

    public function add(PaperContentInterface $paperContent) {
        if ($paperContent->isPersisted()) {
            $this->collection[$this->indexFromEntity($paperContent)] = $paperContent;
        } else {
            $this->new[] = $paperContent;
        }
    }

    public function remove(PaperContentInterface $paperContent) {
        $this->removed[] = $paperContent;
        unset($this->collection[$this->indexFromEntity($paperContent)]);
    }

    protected function createEntity() {
        return new PaperContent();
    }

    protected function indexFromEntity(PaperContentInterface $paperContent) {
        return $paperContent->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }


}
