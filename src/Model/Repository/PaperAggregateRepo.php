<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;
use Model\Entity\PaperInterface;
use Model\Entity\Paper;
use Model\Hydrator\PaperHydrator;


use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class PaperAggregateRepo extends AggregateRepoAbstract implements RepoInterface {

    private $paperHeadlineRepo;
    private $paperContentRepo;

    public function __construct(PaperHeadlineRepo $paperHeadlineRepo, PaperContentRepo $paperContentRepo, PaperHydrator $paperHydrator) {
        $this->paperHeadlineRepo = $paperHeadlineRepo;
        $this->paperContentRepo = $paperContentRepo;
        $this->hydrator = $paperHydrator;
    }

    /**
     *
     * @param type $menuItemIdFk
     * @return PaperInterface|null
     */
    public function get($menuItemIdFk): ?PaperInterface {
        $index = $menuItemIdFk;
        if (!isset($this->collection[$index])) {
            $headline = $this->paperHeadlineRepo->get($menuItemIdFk);
            if ($headline) {
                $row =[];
                $row['menu_item_id_fk'] = $menuItemIdFk;
                $row['headline'] = $headline;
                $row['contents'] = $this->paperContentRepo->findByMenuItemIdFk($menuItemIdFk);
                $this->recreateEntity($index, $row);
            }
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
