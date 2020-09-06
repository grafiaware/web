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
class StatusRepositoryAbstract extends RepoAbstract {

    /**
     * @var StatusDao
     */
    protected $statusDao;

    private $loaded=FALSE;

    protected $entity;

    public function __construct(StatusDao $statusDao) {
        $this->statusDao = $statusDao;
    }

    protected function load() {
        if (!$this->loaded) {
            $row = $this->statusDao->get(static::FRAGMENT_NAME);
            if ($row) {
                $this->entity = $row[0];
            }
            $this->loaded = TRUE;
        }
    }

    public function flush(): void {
        if ($this->entity) {
            $this->statusDao->set(static::FRAGMENT_NAME, [$this->entity]);
        } else {
            $this->statusDao->delete(static::FRAGMENT_NAME);
        }
    }
}
