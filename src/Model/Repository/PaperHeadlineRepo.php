<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;
use Model\Entity\PaperHeadlineInterface;
use Model\Entity\PaperHeadline;
use Model\Dao\PaperHeadlineDao;
use Model\Hydrator\PaperHeadlineHydrator;

use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class PaperHeadlineRepo extends RepoAbstract implements RepoInterface {

    public function __construct(PaperHeadlineDao $headlineDao, PaperHeadlineHydrator $paperHeadlineHydrator) {
        $this->dao = $headlineDao;
        $this->hydrator = $paperHeadlineHydrator;
    }

    /**
     *
     * @param type $menuItemIdFk
     * @return PaperHeadlineInterface|null
     */
    public function get($menuItemIdFk): ?PaperHeadlineInterface {
        $index = $menuItemIdFk;
        if (!isset($this->collection[$index])) {
            $this->recreateEntity($index, $this->dao->get($menuItemIdFk));
        }
        return $this->collection[$index] ?? NULL;
    }


    public function add(PaperHeadlineInterface $paper) {
        $this->collection[$this->indexFromEntity($paper)] = $paper;
    }

    public function remove(PaperHeadlineInterface $paper) {
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
            $paper = new PaperHeadline();
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
