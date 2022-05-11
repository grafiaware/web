<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Model\Repository\RepoAbstract;

use Model\Entity\EntityInterface;
use Red\Model\Entity\ArticleInterface;
use Red\Model\Entity\Article;
use Red\Model\Dao\ArticleDao;
use Model\Dao\DaoFkUniqueInterface;
use Red\Model\Hydrator\ArticleHydrator;

use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class ArticleRepo extends RepoAbstract implements ArticleRepoInterface {

    /**
     * @var DaoFkUniqueInterface
     */
    protected $dataManager;  // přetěžuje $dao v AbstractRepo - typ DaoChildInterface

    public function __construct(ArticleDao $articleDao, ArticleHydrator $articleHydrator) {
        $this->dataManager = $articleDao;
        $this->registerHydrator($articleHydrator);
    }

    /**
     *
     * @param type $id
     * @return PaperInterface|null
     */
    public function get($id): ?ArticleInterface {
        $key = $this->dataManager->getPrimaryKeyTouples(['id'=>$id]);
        return $this->getEntity($key);
    }

    /**
     *
     * @param type $menuItemIdFk
     * @return PaperInterface|null
     */
    public function getByReference($menuItemIdFk): ?EntityInterface {
        $key = $this->dataManager->getForeignKeyTouples('menu_item_id_fk', ['menu_item_id_fk'=>$menuItemIdFk]);
        return $this->getEntityByReference('menu_item_id_fk', $key);
    }

    public function add(ArticleInterface $article) {
        $this->addEntity($article);
    }

    public function remove(ArticleInterface $article) {
        $this->removeEntity($article);
    }

    protected function createEntity() {
        return new Article();
    }

    protected function indexFromEntity(ArticleInterface $article) {
        return $article->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }


}
