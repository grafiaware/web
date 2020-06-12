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
class PaperContentRepo extends RepoAbstract implements RepoInterface {

    public function __construct(PaperContentDao $paperContentDao, HydratorInterface $contentHydrator) {
        $this->dao = $paperContentDao;
        $this->hydrator = $contentHydrator;
    }

    /**
     *
     * @param int $id
     * @return PaperContentInterface|null
     */
    public function get($id): ?PaperContentInterface {
        $index = $menuItemIdFk;
        if (!isset($this->collection[$index])) {
            /** @var PaperContentDao $this->dao */
            $this->recreateEntity($index, $this->dao->get($id));
        }
        return $this->collection[$index] ?? NULL;
    }

    public function findByPaperIdFk($paperIdFk) {
        $selected = [];
        foreach ($this->dao->find("paper_id_fk = :paper_id_fk", [':paper_id_fk' => $paperIdFk]) as $paperContentRow) {
            $index = $this->indexFromRow($paperContentRow);
            $this->recreateEntity($index, $paperContentRow);
            $selected[$index] = $this->collection[$index];
        }
        return $selected;
    }

    public function add(PaperContentInterface $paperContent) {
        $this->collection[$this->indexFromEntity($paperContent)] = $paperContent;
    }

    public function remove(PaperContentInterface $paperContent) {
        $this->removed[] = $paperContent;
        unset($this->collection[$this->indexFromEntity($paperContent)]);
    }

    /**
     *
     * @param array $row
     * @return string index
     */
    protected function recreateEntity($index, $row) {
        if ($row) {
            $paper = new PaperContent();
            $this->hydrator->hydrate($paper, $row);
            $paper->setPersisted();
            $this->collection[$index] = $paper;
        }
    }

    protected function indexFromEntity(PaperContentInterface $paperContent) {
        return $paperContent->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }


}
