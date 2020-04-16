<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

/**
 *
 * @author pes2704
 */
interface RepoInterface extends RepoReadonlyInterface {

    /**
     * Nastaví repository tak, že se repository nepokouší ukládat.
     *
     * @param type $readOnly
     * @return \Model\Repository\RepoInterface
     */
    public function setReadOnly($readOnly): RepoInterface;

    public function isReadOnly();

    public function flush();
}
