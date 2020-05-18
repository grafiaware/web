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
abstract class AggregateRepoAbstract implements RepoInterface {

    protected $collection = [];
    protected $repositories = [];

    protected $readOnly = false;

    public function setReadOnly($readOnly): RepoInterface {
        $this->readOnly = (bool) $readOnly;
        return $this;
    }

    public function isReadOnly() {
        return $this->readOnly;

    }

    public function flush() {
        $this->collection = [];
        /** @var RepoInterface $repository */
        foreach ($this->repositories as $repository) {
            $repository->flush();
        }
    }

    public function __destruct() {
        if (!$this->readOnly) {
            $this->flush();
        }
    }


}
