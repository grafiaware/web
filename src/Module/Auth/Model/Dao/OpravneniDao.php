<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Model\Dao;

use Model\Dao\DaoAbstract;

/**
 * Description of RsDao
 *
 * @author pes2704
 */
class OpravneniDao {

    /**
     * Vrací asociativní pole s jménem uživatele - user. Jde o část řádky tabulky opravneni.
     *
     * @param type $user
     * @return array
     * @throws StatementFailureException
     */
    public function get($user) {
        $sql = "SELECT * FROM opravneni WHERE user=:user";
        $statement = $this->dbHandler->prepare($sql);
//        $statement = $this->dbHandler->query($sql);
        if ($statement == FALSE) {
            $einfo = $this->dbHandler->errorInfo();
            throw new StatementFailureException($einfo[2].PHP_EOL.". Nevznikl PDO statement z sql příkazu: $sql", $einfo[1]);
        }
        $statement->bindParam(':user', $user);
        $success = $statement->execute();
        if (!$success) {
            $einfo = $this->dbHandler->errorInfo();
            throw new StatementFailureException($einfo[2].PHP_EOL.". Nevykonal se PDO statement z sql příkazu: $sql", $einfo[1]);
        }
        $num_rows = $statement->rowCount();

        if ($num_rows > 1) {
            user_error("V databázi existuje duplicitní záznam user=$user", E_USER_ERROR);
        }

        return $num_rows ? $statement->fetch(\PDO::FETCH_ASSOC) : [];
    }
}
