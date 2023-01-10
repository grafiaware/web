<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

/**
 * Interface pro POTOMKOVSKÉ repository s asociací 1:N
 *
 * @author pes2704
 */
interface RepoAssotiatedManyInterface extends RepoInterface {
    /**
     * Metoda získá potomkovskou entoty z potomkovského repository pomocí reference. Hodnoty polí reference naplní z rodičovských dat.
     *
     * @param string $referenceName Jméno refernce z DAO
     * @param RowDataInterface $parentRowData Rodičovská data pro získání hodnot polí reference.
     * @return iterable
     */
    public function findByParentData(string $referenceName, RowDataInterface $parentRowData): iterable;

//    public function findByReference(string $parentTableName, ...$referenceParams): iterable;
}
