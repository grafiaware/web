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
use Model\Dao\DaoChildInterface;
use Red\Model\Hydrator\ArticleHydrator;

use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class ArticleRepo extends RepoAbstract implements ArticleRepoInterface {

    /**
     * @var DaoChildInterface
     */
    protected $dao;  // přetěžuje $dao v AbstractRepo - typ DaoChildInterface

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
        return $this->getEntity($id);
    }

    /**
     *
     * @param type $menuItemIdFk
     * @return ArticleInterface|null
     */
    public function getByReference($menuItemIdFk): ?EntityInterface {
        return $this->getEntityByReference($menuItemIdFk);
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

    protected function indexFromKeyParams($id) {
        return $id;
    }

    protected function indexFromEntity(ArticleInterface $article) {
        return $article->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }


}
