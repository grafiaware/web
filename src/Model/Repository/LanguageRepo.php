<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;
use Model\Entity\LanguageInterface;
use Model\Entity\Language;
use Model\Dao\LanguageDao;

/**
 * Description of LanguageRepository
 *
 * @author pes2704
 */
class LanguageRepo {

    /**
     *
     * @var LanguageDao
     */
    private $languageDao;

    public function __construct(LanguageDao $languageDao) {
        $this->languageDao = $languageDao;
    }

    /**
     *
     * @param type $langCode
     * @return LanguageInterface|null
     */
    public function get($langCode): ?LanguageInterface {
        $row = $this->languageDao->get($langCode);
        return $row ? $this->createItem($row) : NULL;
    }

    /**
     *
     * @return LanguageInterface array of
     */
    public function find($whereClause=null) {
        $entities = [];
        foreach ($this->languageDao->find($whereClause) as $row) {
            $entities[] = $this->createItem($row);
        }
        return $entities;
    }

    /**
     *
     * @param type $lang
     * @param type $row
     * @return LanguageInterface
     */
    private function createItem($row) {
        return (new Language())
                ->setLangCode($row['lang_code'])
                ->setLocale($row['locale'])
                ->setName($row['name'])
                ->setCollation($row['collation'])
                ->setState($row['state']);
    }

    public function add(EntityInterface $entity) {
        ;
    }

    public function remove(EntityInterface $entity) {
        ;
    }

}
