<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Dao;

/**
 *
 * @author pes2704
 */
interface DaoReferenceNonuniqueInterface extends DaoWithReferenceInterface {

    public function findByReference($referenceName, array $key): array;
}
