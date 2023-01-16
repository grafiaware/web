<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Model\Repository\RepoAbstract;

use Red\Model\Entity\LanguageInterface;
use Red\Model\Entity\Language;
use Red\Model\Dao\LanguageDao;
use Red\Model\Hydrator\LanguageHydrator;

/**
 * Description of LanguageRepository
 *
 * @author pes2704
 */
class LanguageRepo extends RepoAbstract {

    public function __construct(LanguageDao $languageDao, LanguageHydrator $languageHydrator) {
        $this->dataManager = $languageDao;
        $this->registerHydrator($languageHydrator);

    }

    /**
     *
     * @param type $langCode
     * @return LanguageInterface|null
     */
    public function get($langCode): ?LanguageInterface {
        return $this->getEntity($langCode);
    }

    /**
     *
     * @return LanguageInterface array of
     */
    public function findAll() {
        return $this->findEntities();
    }

    public function add(LanguageInterface $entity) {
        $this->addEntity($entity);
    }

    public function remove(LanguageInterface $entity) {
        $this->removeEntity($entity);
    }

    protected function createEntity() {
        return new Language();
    }

    protected function indexFromEntity(LanguageInterface $language) {
        return $language->getLangCode();
    }

    protected function indexFromRow($row) {
        return $row['lang_code'];
    }
}
