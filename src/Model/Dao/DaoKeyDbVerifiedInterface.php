<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Dao;
use Model\Dao\Exception\DaoKeyVerificationFailedException;
/**
 *
 * @author pes2704
 */
interface DaoKeyDbVerifiedInterface {

    /**
     *
     * @param array $row
     * @throws DaoKeyVerificationFailedException Objekt musí vyhazovat výjimku DaoKeyVerificationFailedException, pokud se nepodařilo ověřit nastavený primární klíč entity jako platný (použitelný).
     *
     */
    public function insertWithKeyVerification($row);
}
