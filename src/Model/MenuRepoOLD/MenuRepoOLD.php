<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;
use Model\Entity\MenuItem;
use Model\Entity\MenuItemInterface;
use Model\Repository\Criteria\CriteriaInterface;
use Model\Dao\MenuDao;

use Pes\Type\Date;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class MenuRepoOLD {

    private $menuDao;

    public function __construct(MenuDao $menuDao) {
        $this->menuDao = $menuDao;
    }

    /**
     *
     * @param string $lang
     * @param string $list
     * @param bool $publishedOnly
     * @return MenuItemInterface
     */
    public function get($lang, $list, $publishedOnly=TRUE) {
        $row = $this->menuDao->get($lang, $list, $publishedOnly);
        return $row ? $this->createItem($lang, $row) : NULL;
    }

    /**
     *
     * @param string $lang
     * @param string $parentList
     * @return MenuItem array of
     */
    public function findChildren($lang, $parentList, $publishedOnly=TRUE) {
        $children = [];
        foreach($this->menuDao->findChildren($lang, $parentList, $publishedOnly) as $row) {
            $children[] = $this->createItem($lang, $row);
        }
        return $children;
    }

    private function createItem($lang, $row) {
        $dateStart = Date::createFromSqlDate($row['start']);
        $dateStop = Date::createFromSqlDate($row['stop']);
        return (new MenuItem())
                ->setLangCodeFk($lang)
                ->setHierarchyUid($row['uid'])
                ->setTitle($row['title'])
                ->setActive($row['active'])
                ->setActual($row['actual'] ?? TRUE)  // start a stop jsou NULL -> položka je platná stále
                ->setShowTime($dateStart ? $dateStart->getCzechStringDate() : NULL)
                ->setHideTime($dateStop ? $dateStop->getCzechStringDate() : NULL)
                ->setEditor($row['editor']);
    }

    public function add(EntityInterface $entity) {
        ;
    }

    public function remove(EntityInterface $entity) {
        ;
    }
}
