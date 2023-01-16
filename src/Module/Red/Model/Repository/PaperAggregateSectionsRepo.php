<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Model\Repository\RepoAbstract;
//use Model\Repository\RepoReadonlyInterface;

use Red\Model\Entity\PaperAggregatePaperSection;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;
use Red\Model\Dao\PaperDao;
use Red\Model\Hydrator\PaperHydrator;

use Model\Repository\RepoAssotiatedOneTrait;
use Model\Repository\RepoAssotiatingManyTrait;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class PaperAggregateSectionsRepo extends RepoAbstract implements PaperAggregateSectionsRepoInterface {

    public function __construct(PaperDao $paperDao, PaperHydrator $paperHydrator) {
        $this->dataManager = $paperDao;
        $this->registerHydrator($paperHydrator);
    }

    use RepoAssotiatedOneTrait;  // pro get podle menu item id
    use RepoAssotiatingManyTrait; // pro paper section

    protected function createEntity() {
        return new PaperAggregatePaperSection();
    }

    ####################

    public function get($id): ?PaperAggregatePaperSectionInterface {
        return $this->getEntity($id);
    }

    public function getByMenuItemId($menuItemId): ?PaperAggregatePaperSectionInterface {
        return $this->getEntityByReference(PaperDao::REFERENCE_MENU_ITEM, $menuItemId);
    }

    /**
     *
     * @param PaperAggregatePaperSectionInterface $paperAggSection
     */
    public function add(PaperAggregatePaperSectionInterface $paperAggSection) {
        $this->addEntity($paperAggSection);
    }

    /**
     *
     * @param PaperAggregatePaperSectionInterface $paperAggSection
     */
    public function remove(PaperAggregatePaperSectionInterface $paperAggSection) {
        $this->removeEntity($paperAggSection);
    }

    #### protected ###########

    protected function indexFromEntity(PaperAggregatePaperSectionInterface $paperAggSection) {
        return $paperAggSection->getId;
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }
}
