<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Repository\RepoInterface;
/**
 * Description of RepoAbstract
 *
 * @author pes2704
 */
trait AggregateRepoTrait {

    protected $childRepositories = [];

    public function flushChildRepositories() {
        $this->collection = [];
        /** @var RepoInterface $repository */
        foreach ($this->childRepositories as $repository) {
            $repository->flush();
        }

        parent::flush();
    }


    public function __destruct() {
        if (!$this->readOnly) {
            $this->flushChildRepositories();
        }
    }
}
