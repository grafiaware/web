<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Dao\StatusDao;

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

    private $loadedFragment = [];

    protected $entity;

    public function __construct(StatusDao $statusDao) {
        $this->statusDao = $statusDao;
    }

    protected function load() {
        if (!isset($this->loadedFragment[static::FRAGMENT_NAME])) {
            $row = $this->statusDao->get(static::FRAGMENT_NAME);
            if ($row) {
                $this->entity = $row[0];
            }
            $this->loadedFragment[static::FRAGMENT_NAME] = true;
        }
    }

    public function flush(): void {
        if (isset($this->loadedFragment[static::FRAGMENT_NAME])) {   // pokud není loaded -> není entita
            if ($this->entity) {
                $this->statusDao->set(static::FRAGMENT_NAME, [$this->entity]);
            } else {
                $this->statusDao->delete(static::FRAGMENT_NAME);
            }
        }
        unset($this->loadedFragment[static::FRAGMENT_NAME]);
        $this->sessionClose();
    } 
    
    /**
     * Uloží a zavře session. To ukončí session lock.
     * V metodě flush() jednotlivých repository je odstraněna položka $this->loadedFragment. 
     * Metoda sessionClose() zjičťuje jestli je pole $this->loadedFragment prízdné a pokud je prázdné uloží a zavře session 
     * - v metodě dao finish() dojde h volání session_write_close().
     */
    private function sessionClose() {
        if (empty($this->loadedFragment)) {
            $this->statusDao->finish();   // zapíše a uzavře session       
        }
    }

    public function __destruct() {
        $this->flush();
    }
}
