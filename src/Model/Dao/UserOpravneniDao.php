<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Dao;
use Pes\Database\Handler\HandlerInterface;

/**
 * Description of UserDao
 *
 * @author pes2704
 */
class UserOpravneniDao {

    protected $dbHandler;

    public function __construct(HandlerInterface $dbHandler) {
        $this->dbHandler = $dbHandler;
    }

    /**
     * Vrací asociativní pole s jménem a rolí uživatele - user, role. Jde o část řádky tabulky opravneni.
     *
     * @param type $user
     * @return array
     * @throws StatementFailureException
     */
    public function get($user) {
        //TODO: Svpboda Provizorní řešení get podle name - není unikátní -> nutno předělat opravneni na nové tabulky a user repo a dao
        $sql = "SELECT user, role FROM opravneni WHERE user=:user";
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
            user_error("V databázové tabulce opravneni existuje duplicitní záznam user=$user", E_USER_ERROR);
        }

        return $num_rows ? $statement->fetch(\PDO::FETCH_ASSOC) : [];
    }

    /**
     * Vrací asociativní pole s jménem uživatele - user. Jde o část řádky tabulky opravneni.
     *
     * @param type $user
     * @return array
     * @throws StatementFailureException
     */
    public function getByAuthentication($user, $password) {
        $sql = "SELECT user, role FROM opravneni WHERE user=:user AND password=:password";
        $statement = $this->dbHandler->prepare($sql);
//        $statement = $this->dbHandler->query($sql);
        if ($statement == FALSE) {
            $einfo = $this->dbHandler->errorInfo();
            throw new StatementFailureException($einfo[2].PHP_EOL.". Nevznikl PDO statement z sql příkazu: $sql", $einfo[1]);
        }
        $statement->bindParam(':user', $user);
        $statement->bindParam(':password', $password);
        $success = $statement->execute();
        if (!$success) {
            $einfo = $this->dbHandler->errorInfo();
            throw new StatementFailureException($einfo[2].PHP_EOL.". Nevykonal se PDO statement z sql příkazu: $sql", $einfo[1]);
        }
        $num_rows = $statement->rowCount();

        if ($num_rows > 1) {
            user_error("V databázové tabulce opravneni existuje duplicitní záznam user=$user", E_USER_ERROR);
        }

        return $num_rows ? $statement->fetch(\PDO::FETCH_ASSOC) : [];
    }
}
