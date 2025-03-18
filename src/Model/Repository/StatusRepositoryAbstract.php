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
        // smaže fragment
        unset($this->loadedFragment[static::FRAGMENT_NAME]);
        // uloží a zavře session pokud byl smazán poslední fragment
        $this->sessionClose();
    } 
    
    /**
     * Pokud jsou všechny fragmenty prázdné (např. smazané), uloží a zavře session. To zabráníé vzniku session lock nebo ukončí session lock.
     * V metodě flush() jednotlivých repository je odstraněna položka se jménem příslušným danému repository $this->loadedFragment[jméno fragmentu]. 
     * Metoda sessionClose() zjišťuje jestli je pole $this->loadedFragment prízdné a pokud je prázdné, uloží a zavře session.
     * Pro uložení a zavření session volá metodu status DAO finish() - v metodě dao finish() dojde k volání session_write_close().
     * 
     * Celý tento mechanizmus tedy pracije tak, že po zavolání metody flush() jednotlivých repository každé repository smaže "svůj" fragment a po smazání 
     * posledního existujícího fragmentu dojde ke uložení a zavření session.
     * 
     * Toho se dosáhne destrukcí repository - v metodě __destuct se volá ->flush() nebo přímo voláním metod flush() jednotlivých repository.
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
