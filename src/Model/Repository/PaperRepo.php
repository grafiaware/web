<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;
use Model\Entity\PaperInterface;
use Model\Entity\Paper;
use Model\Dao\PaperDao;
use Model\Hydrator\HydratorInterface;

use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class PaperRepo extends RepoAbstract implements RepoInterface {

    public function __construct(PaperDao $paperDao, HydratorInterface $paperHydrator) {
        $this->dao = $paperDao;
        $this->hydrator = $paperHydrator;
    }

    /**
     *
     * @param type $menuItemId
     * @return PaperInterface|null
     */
    public function get($menuItemId): ?PaperInterface {
        $index = $menuItemId;
        if (!isset($this->collection[$index])) {
            $this->recreateEntity($index, $this->dao->get($menuItemId));
        }
        return $this->collection[$index] ?? NULL;
    }

    public function add(PaperInterface $paper) {
        $this->collection[$this->indexFromEntity($paper)] = $paper;
    }

    public function remove(PaperInterface $paper) {
        $this->removed[] = $paper;
        unset($this->collection[$this->indexFromEntity($paper)]);
    }

    /**
     *
     * @param array $row
     * @return string index
     */
    protected function recreateEntity($index, $row) {
        if ($row) {
            $paper = new Paper();
            $this->hydrator->hydrate($paper, $row);
            $paper->setPersisted();
            $this->collection[$index] = $paper;
        }
    }

    protected function indexFromEntity(PaperInterface $paper) {
        return $paper->getMenuItemIdFk();
    }

    protected function indexFromRow($row) {
        return $row['menu_item_id_fk'];
    }


}
