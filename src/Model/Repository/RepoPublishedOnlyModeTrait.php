<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Dao\ContextPublishedInterface;
use Model\Repository\Exception\UnableToSetOnlyPublishedModeException;

/**
 *
 * @author pes2704
 */
trait RepoPublishedOnlyModeTrait {

    private $onlyPublished = false;

    /**
     * Nastaví objekt dao používaný tímto repository do režimu čtení jen aktivní a současně aktuálních položek. Podmínkou je, že objekt dao používaný
     * tímto repository je typu ContextPublishedInterface.
     *
     * @param type $onlyPublished
     * @return void
     * @throws UnableToSetOnlyPublishedModeException Objekt DAO není typu ContextPublishedInterface
     */
    public function setOnlyPublishedMode($onlyPublished = true): void {
        if ($this->dao instanceof ContextPublishedInterface) {
            $this->dao->setContextPublished($onlyPublished, $onlyPublished);
        } else {
            throw new UnableToSetOnlyPublishedModeException("Objekt dao ". get_class($this->dao)." používaný tímto repository ". get_class($this)." není typu ContextPublishedInterface.");
        }

//TODO:
//        foreach ($this->childRepositories as $childRepo) {
//            if ($childRepo instanceof PublishedOnlyModeInterface) {
//                $childRepo->setOnlyPublishedMode($onlyPublished);
//            }
//        }

    }

    /**
     * Default hodnota je false. Hodnotu je možno změnit voláním metody setOnlyPublishedMode. Podmínkou je, že objekt dao používaný
     * tímto repository je typu ContextPublishedInterface.
     *
     * @return bool
     */
    public function getOnlyPublished() {
        return $this->onlyPublished;
    }

}
