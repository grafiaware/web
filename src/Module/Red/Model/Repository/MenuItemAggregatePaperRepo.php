<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Model\Repository\RepoAbstract;

use Red\Model\Dao\MenuItemDao;
use Model\Hydrator\HydratorInterface;
use Red\Model\Entity\MenuItemAggregatePaperInterface;

use Red\Model\Repository\PaperAggregateSectionsRepo;
use Red\Model\Entity\MenuItemAggregatePaper;
use Red\Model\Hydrator\MenuItemChildPaperHydrator;
use Red\Model\Entity\PaperInterface;

use \Model\Repository\RepoAssotiatingOneTrait;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class MenuItemAggregatePaperRepo extends RepoAbstract implements MenuItemAggregatePaperRepoInterface {
    /**
     *
     * @var MenuItemDao
     */
    protected $dataManager;

    use RepoAssotiatingOneTrait;

    public function __construct(MenuItemDao $menuItemDao, HydratorInterface $menuItemHydrator) {
        $this->dataManager = $menuItemDao;
        $this->registerHydrator($menuItemHydrator);
    }

    /**
     * Vrací MenuItemAggregatePaper podle hodnoty primárního klíče - kompozit z langCode a uid.
     * Vrací jen položky, které jsou aktivní a aktuální.
     *
     * @param string $langCodeFk
     * @param string $uidFk
     * @return MenuItemAggregatePaperInterface|null
     */
    public function get($langCodeFk, $uidFk): ?MenuItemAggregatePaperInterface {
        return $this->getEntity($langCodeFk, $uidFk);
    }

    /**
     * Vrací MenuItemAggregatePaper podle id, id je cizí klíč v entitách Paper, Article, Multipage.
     * Vrací jen položky, které jsou aktivní a aktuální.
     *
     * @param type $id
     * @return MenuItemAggregatePaperInterface|null
     */
    public function getById($id): ?MenuItemAggregatePaperInterface {
        return $this->getEntityByUnique(['id'=>$id]);
    }

    /**
     * Vrací MenuItemAggregatePaper podle hodnoty prettyUri.
     * Vrací jen položky, které jsou aktivní a aktuální.
     *
     * @param type $prettyUri
     * @return MenuItemAggregatePaperInterface|null
     */
    public function getByPrettyUri($prettyUri): ?MenuItemAggregatePaperInterface {
        return $this->getEntityByUnique(['prettyuri'=>$prettyUri]);
    }

    protected function createEntity() {
        return new MenuItemAggregatePaper();
    }
}
