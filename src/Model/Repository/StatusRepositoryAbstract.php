<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Dao\StatusDao;
use LogicException;

/**
 * StatusRepositoryAbstract má metody pro zápis (aktualizaci) dat v session a destruktor, který zajišťuje automatické uložení (aktualizaci)
 * sat v session při zániku objektu.
 *
 * @author pes2704
 */
abstract class StatusRepositoryAbstract {

    /**
     * @var StatusDao
     */
    protected $statusDao;

    private static $loadedFragment = [];   // proměnná společná pro všechny SttausRepository

    protected $entity;

    public function __construct(StatusDao $statusDao) {
        $this->statusDao = $statusDao;
    }

    protected function load() {
        if (empty($_SESSION)) {
            throw new LogicException("Nejsou data v globálním poli \$_SESSION. Session v tomto běhu skriptu ještě nebyla spuštěna");
        }
        
        if (!isset(self::$loadedFragment[static::FRAGMENT_NAME])) {
            $row = $this->statusDao->get(static::FRAGMENT_NAME);
            if ($row) {
                $this->entity = $row[0];
            }
            self::$loadedFragment[static::FRAGMENT_NAME] = true;
        }
    }

    public function flush(): void {
        if (isset(self::$loadedFragment[static::FRAGMENT_NAME])) {   // pokud není loaded -> není entita
            if ($this->entity) {
                $this->statusDao->set(static::FRAGMENT_NAME, [$this->entity]);
            } else {
                $this->statusDao->delete(static::FRAGMENT_NAME);
            }
            // smaže fragment
            unset(self::$loadedFragment[static::FRAGMENT_NAME]);
        }
    } 

    public function __destruct() {
        $this->flush();
    }
}
